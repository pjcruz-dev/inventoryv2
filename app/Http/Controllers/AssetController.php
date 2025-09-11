<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Vendor;
use App\Models\User;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Asset::with(['category', 'vendor', 'assignedUser']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_tag', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('category', function($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('vendor', function($vendorQuery) use ($search) {
                      $vendorQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('assignedUser', function($userQuery) use ($search) {
                      $userQuery->where('first_name', 'like', "%{$search}%")
                               ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }
        
        $assets = $query->paginate(15)->withQueryString();
        return view('assets.index', compact('assets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = AssetCategory::all();
        $vendors = Vendor::all();
        $users = User::where('status', 'active')
                    ->orderBy('first_name')
                    ->get();
        return view('assets.create', compact('categories', 'vendors', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_tag' => 'required|string|max:50|unique:assets',
            'category_id' => 'required|exists:asset_categories,id',
            'vendor_id' => 'required|exists:vendors,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'serial_number' => 'required|string|max:100|unique:assets',
            'purchase_date' => 'required|date',
            'warranty_end' => 'nullable|date',
            'cost' => 'required|numeric|min:0',
            'status' => 'required|in:Active,Inactive,Under Maintenance,Issue Reported,Pending Confirmation,Disposed',
            'movement' => 'required|in:New Arrival,Deployed,Returned,Transferred,Disposed'
        ]);

        Asset::create($validated);
        return redirect()->route('assets.index')->with('success', 'Asset created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Asset $asset)
    {
        $asset->load(['category', 'vendor']);
        return view('assets.show', compact('asset'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset)
    {
        $asset->load(['timeline.fromUser', 'timeline.toUser']);
        $categories = AssetCategory::all();
        $vendors = Vendor::all();
        $users = User::where('status', 'active')
                    ->orderBy('first_name')
                    ->get();
        return view('assets.edit', compact('asset', 'categories', 'vendors', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'asset_tag' => 'required|string|max:50|unique:assets,asset_tag,' . $asset->id,
            'category_id' => 'required|exists:asset_categories,id',
            'vendor_id' => 'required|exists:vendors,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'serial_number' => 'required|string|max:100|unique:assets,serial_number,' . $asset->id,
            'purchase_date' => 'required|date',
            'warranty_end' => 'nullable|date',
            'cost' => 'required|numeric|min:0',
            'status' => 'required|in:Active,Inactive,Under Maintenance,Issue Reported,Pending Confirmation,Disposed',
            'movement' => 'required|in:New Arrival,Deployed,Returned,Transferred,Disposed'
        ]);

        $asset->update($validated);
        return redirect()->route('assets.index')->with('success', 'Asset updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset)
    {
        $asset->delete();
        return redirect()->route('assets.index')->with('success', 'Asset deleted successfully.');
    }

    /**
     * Assign a user to an asset.
     */
    public function assign(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'assigned_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        $asset->update([
            'assigned_to' => $validated['assigned_to'],
            'assigned_date' => $validated['assigned_date']
        ]);

        // Log the assignment if notes are provided
        if (!empty($validated['notes'])) {
            // You can add logging functionality here if needed
        }

        return redirect()->route('assets.show', $asset)
                        ->with('success', 'User assigned to asset successfully.');
    }

    /**
     * Unassign a user from an asset.
     */
    public function unassign(Asset $asset)
    {
        $previousUser = $asset->assignedUser;
        
        $asset->update([
            'assigned_to' => null,
            'assigned_date' => null
        ]);

        // Create audit log
        \App\Models\Log::create([
            'category' => 'Asset',
            'asset_id' => $asset->id,
            'user_id' => auth()->id(),
            'role_id' => auth()->user()->role_id ?? 1,
            'department_id' => auth()->user()->department_id,
            'event_type' => 'unassigned',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'remarks' => 'Asset unassigned from ' . ($previousUser ? $previousUser->first_name . ' ' . $previousUser->last_name : 'unknown user'),
            'created_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User unassigned from asset successfully.'
        ]);
    }

    /**
     * Reassign an asset from one user to another.
     */
    public function reassign(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'new_assigned_to' => 'required|exists:users,id',
            'assigned_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        $previousUser = $asset->assignedUser;
        $newUser = \App\Models\User::find($validated['new_assigned_to']);
        
        $asset->update([
            'assigned_to' => $validated['new_assigned_to'],
            'assigned_date' => $validated['assigned_date']
        ]);

        // Create audit log for reassignment
        \App\Models\Log::create([
            'category' => 'Asset',
            'asset_id' => $asset->id,
            'user_id' => auth()->id(),
            'role_id' => auth()->user()->role_id ?? 1,
            'department_id' => auth()->user()->department_id,
            'event_type' => 'reassigned',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'remarks' => 'Asset reassigned from ' . 
                           ($previousUser ? $previousUser->first_name . ' ' . $previousUser->last_name : 'unassigned') . 
                           ' to ' . $newUser->first_name . ' ' . $newUser->last_name . 
                           (!empty($validated['notes']) ? '. Notes: ' . $validated['notes'] : ''),
            'created_at' => now()
        ]);

        return redirect()->route('assets.show', $asset)
                        ->with('success', 'Asset reassigned successfully.');
    }
}
