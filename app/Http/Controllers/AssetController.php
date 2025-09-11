<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Vendor;
use App\Models\User;
use App\Models\AssetAssignmentConfirmation;
use App\Mail\AssetAssignmentConfirmation as AssetAssignmentConfirmationMail;
use Illuminate\Support\Facades\Mail;

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
        
        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Movement filter
        if ($request->filled('movement')) {
            $query->where('movement', $request->movement);
        }
        
        // Assignment filter
        if ($request->filled('assignment')) {
            if ($request->assignment === 'assigned') {
                $query->whereNotNull('assigned_to');
            } elseif ($request->assignment === 'unassigned') {
                $query->whereNull('assigned_to');
            }
        }
        
        $assets = $query->paginate(15)->withQueryString();
        
        // Get filter options for the view
        $categories = AssetCategory::orderBy('name')->get();
        $statuses = Asset::distinct()->pluck('status')->filter()->sort()->values();
        $movements = Asset::distinct()->pluck('movement')->filter()->sort()->values();
        
        return view('assets.index', compact('assets', 'categories', 'statuses', 'movements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = AssetCategory::orderBy('name')->get();
        $vendors = Vendor::orderBy('name')->get();
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
            'movement' => 'required|in:New Arrival,Deployed Tagged,Returned,Transferred,Disposed'
        ]);

        // Set default status and movement for new assets
        $validated['status'] = 'Active';
        $validated['movement'] = 'New Arrival';

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
        $categories = AssetCategory::orderBy('name')->get();
        $vendors = Vendor::orderBy('name')->get();
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
            'movement' => 'required|in:New Arrival,Deployed Tagged,Returned,Transferred,Disposed'
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

        $user = User::find($validated['assigned_to']);
        
        // Update asset status to Pending Confirmation
        $asset->update([
            'assigned_to' => $validated['assigned_to'],
            'assigned_date' => $validated['assigned_date'],
            'status' => 'Pending Confirmation'
        ]);

        // Create AssetAssignment record (this will automatically create confirmation via model boot method)
        \App\Models\AssetAssignment::create([
            'asset_id' => $asset->id,
            'user_id' => $validated['assigned_to'],
            'assigned_by' => auth()->id(),
            'assigned_date' => $validated['assigned_date'],
            'status' => 'pending',
            'notes' => $validated['notes']
        ]);

        // Create audit log
        \App\Models\Log::create([
            'category' => 'Asset',
            'asset_id' => $asset->id,
            'user_id' => auth()->id(),
            'role_id' => auth()->user()->role_id ?? 1,
            'department_id' => auth()->user()->department_id,
            'event_type' => 'assigned',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'remarks' => 'Asset assigned to ' . $user->first_name . ' ' . $user->last_name . 
                        '. Confirmation email sent.' . 
                        (!empty($validated['notes']) ? ' Notes: ' . $validated['notes'] : ''),
            'created_at' => now()
        ]);

        return redirect()->route('assets.show', $asset)
                        ->with('success', 'Asset assigned successfully. Confirmation email sent to user.');
    }

    /**
     * Unassign a user from an asset (Return Process).
     */
    public function unassign(Asset $asset)
    {
        $previousUser = $asset->assignedUser;
        
        // Update asset status to Active and movement to Returned
        $asset->update([
            'assigned_to' => null,
            'assigned_date' => null,
            'status' => 'Active',
            'movement' => 'Returned'
        ]);

        // Mark any pending confirmations as completed (asset returned)
        AssetAssignmentConfirmation::where('asset_id', $asset->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
                'notes' => 'Asset returned - confirmation automatically completed'
            ]);

        // Create audit log
        \App\Models\Log::create([
            'category' => 'Asset',
            'asset_id' => $asset->id,
            'user_id' => auth()->id(),
            'role_id' => auth()->user()->role_id ?? 1,
            'department_id' => auth()->user()->department_id,
            'event_type' => 'returned',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'remarks' => 'Asset returned from ' . ($previousUser ? $previousUser->first_name . ' ' . $previousUser->last_name : 'unknown user') . '. Status updated to Active, movement to Returned.',
            'created_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Asset returned successfully. Status updated to Active.'
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
        $newUser = User::find($validated['new_assigned_to']);
        
        // Mark any existing pending confirmations as completed
        AssetAssignmentConfirmation::where('asset_id', $asset->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
                'notes' => 'Asset reassigned - previous confirmation automatically completed'
            ]);
        
        // Mark any existing pending assignments as completed
        \App\Models\AssetAssignment::where('asset_id', $asset->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'confirmed',
                'return_date' => now()
            ]);
        
        // Update asset status to Pending Confirmation for new assignment
        $asset->update([
            'assigned_to' => $validated['new_assigned_to'],
            'assigned_date' => $validated['assigned_date'],
            'status' => 'Pending Confirmation'
        ]);

        // Create new AssetAssignment record (this will automatically create confirmation via model boot method)
        \App\Models\AssetAssignment::create([
            'asset_id' => $asset->id,
            'user_id' => $validated['new_assigned_to'],
            'assigned_by' => auth()->id(),
            'assigned_date' => $validated['assigned_date'],
            'status' => 'pending',
            'notes' => $validated['notes']
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
                           '. Confirmation email sent.' .
                           (!empty($validated['notes']) ? ' Notes: ' . $validated['notes'] : ''),
            'created_at' => now()
        ]);

        return redirect()->route('assets.show', $asset)
                        ->with('success', 'Asset reassigned successfully. Confirmation email sent to new user.');
    }
}
