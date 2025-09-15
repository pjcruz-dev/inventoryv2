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
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\ActivityLogService;

class AssetController extends Controller
{
    use AuthorizesRequests;
    
    protected $activityLogService;
    
    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
        $this->middleware('auth');
        $this->middleware('throttle:60,1')->only(['store', 'update', 'destroy']);
        $this->middleware('permission:view_assets')->only(['index', 'show']);
        $this->middleware('permission:create_assets')->only(['create', 'store']);
        $this->middleware('permission:edit_assets')->only(['edit', 'update']);
        $this->middleware('permission:delete_assets')->only(['destroy']);
    }
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
        $users = User::where('status', 1)
                    ->orderBy('first_name')
                    ->get();
        return view('assets.create', compact('categories', 'vendors', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Asset::class);
        
        $validated = $request->validate(Asset::validationRules());

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
        $users = User::where('status', 1)
                    ->orderBy('first_name')
                    ->get();
        return view('assets.edit', compact('asset', 'categories', 'vendors', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        $this->authorize('update', $asset);
        
        $validated = $request->validate(Asset::updateValidationRules($asset->id));

        $asset->update($validated);
        return redirect()->route('assets.index')->with('success', 'Asset updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset)
    {
        $this->authorize('delete', $asset);
        
        // Check if asset is assigned before deletion
        if ($asset->assigned_to) {
            return redirect()->route('assets.index')
                ->with('error', 'Cannot delete asset that is currently assigned to a user.');
        }
        
        $asset->delete();
        return redirect()->route('assets.index')->with('success', 'Asset deleted successfully.');
    }

    /**
     * Assign a user to an asset.
     */
    public function assign(Request $request, Asset $asset)
    {
        $this->authorize('assign', $asset);
        
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

        // Create enhanced audit log
        $this->activityLogService->logActivity(
            $asset,
            'assigned',
            'Asset assigned to ' . $user->first_name . ' ' . $user->last_name . '. Status changed to Pending Confirmation.',
            $asset->getOriginal(), // old values
            $asset->getAttributes(), // new values
            [
                'assigned_user_id' => $validated['assigned_to'],
                'assigned_user_name' => $user->first_name . ' ' . $user->last_name,
                'assigned_date' => $validated['assigned_date'],
                'assignment_notes' => $validated['notes'],
                'previous_status' => $asset->getOriginal('status'),
                'new_status' => 'Pending Confirmation'
            ]
        );

        return redirect()->route('assets.show', $asset)
                        ->with('success', 'Asset assigned successfully. Confirmation email sent to user.');
    }

    /**
     * Unassign a user from an asset (Return Process).
     */
    public function unassign(Asset $asset)
    {
        $this->authorize('unassign', $asset);
        
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

        // Create enhanced audit log
        $this->activityLogService->logActivity(
            $asset,
            'returned',
            'Asset returned from ' . ($previousUser ? $previousUser->first_name . ' ' . $previousUser->last_name : 'unknown user') . '. Status updated to Active, movement to Returned.',
            $asset->getOriginal(), // old values
            $asset->getAttributes(), // new values
            [
                'previous_user_id' => $previousUser ? $previousUser->id : null,
                'previous_user_name' => $previousUser ? $previousUser->first_name . ' ' . $previousUser->last_name : 'unknown user',
                'previous_status' => $asset->getOriginal('status'),
                'new_status' => 'Active',
                'previous_movement' => $asset->getOriginal('movement'),
                'new_movement' => 'Returned',
                'return_processed_by' => auth()->user()->first_name . ' ' . auth()->user()->last_name
            ]
        );

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
        $this->authorize('reassign', $asset);
        
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

    /**
     * Generate a printable report of assets assigned to employees.
     */
    public function printEmployeeAssets()
    {
        $users = User::with([
            'assignedAssets.category',
            'assignedAssets.vendor',
            'assignedAssets.computer',
            'department'
        ])->whereHas('assignedAssets')->get();

        $totalUsers = $users->count();
        $totalAssets = $users->sum(function($user) {
            return $user->assignedAssets->count();
        });
        
        $assetsByCategory = $users->flatMap(function($user) {
            return $user->assignedAssets;
        })->groupBy('category.name')->map(function($assets) {
            return $assets->count();
        });

        return view('assets.print-employee-assets', compact('users', 'totalUsers', 'totalAssets', 'assetsByCategory'));
    }

    public function printSingleEmployeeAssets(User $user)
    {
        $user->load([
            'assignedAssets.category',
            'assignedAssets.vendor',
            'assignedAssets.computer',
            'department'
        ]);

        $totalAssets = $user->assignedAssets->count();
        
        $assetsByCategory = $user->assignedAssets->groupBy('category.name')->map(function($assets) {
            return $assets->count();
        });

        return view('assets.print-single-employee-assets', compact('user', 'totalAssets', 'assetsByCategory'));
    }

    /**
     * Generate printable labels for selected assets.
     */
    public function bulkPrintLabels(Request $request)
    {
        $request->validate([
            'asset_ids' => 'required|array|min:1',
            'asset_ids.*' => 'exists:assets,id',
            'label_width' => 'nullable|integer|min:50|max:800',
            'label_height' => 'nullable|integer|min:50|max:400'
        ]);

        $assets = Asset::with(['category', 'vendor', 'assignedUser', 'department'])
                      ->whereIn('id', $request->asset_ids)
                      ->get();

        if ($assets->isEmpty()) {
            return back()->with('error', 'No valid assets selected for printing.');
        }

        // Get custom dimensions from request or use defaults
        $labelWidth = $request->input('label_width', 320);
        $labelHeight = $request->input('label_height', 200);
        
        return view('assets.bulk-print-labels', compact('assets', 'labelWidth', 'labelHeight'));
    }

    /**
     * Generate printable labels for all assets.
     */
    public function printAllAssetLabels(Request $request)
    {
        $this->authorize('viewAny', Asset::class);
        
        // Validate label dimensions
        $request->validate([
            'label_width' => 'nullable|integer|min:50|max:800',
            'label_height' => 'nullable|integer|min:50|max:400'
        ]);
        
        // Apply the same filters as the index page
        $query = Asset::with(['category', 'vendor', 'assignedUser', 'department']);
        
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
        
        $assets = $query->get();
        
        if ($assets->isEmpty()) {
            return back()->with('error', 'No assets found to print.');
        }
        
        // Get custom dimensions from request
        $labelWidth = $request->input('label_width', 320);
        $labelHeight = $request->input('label_height', 200);
        
        return view('assets.bulk-print-labels', compact('assets', 'labelWidth', 'labelHeight'));
    }

    /**
     * Generate a unique asset tag
     */
    public function generateUniqueTag(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string'
        ]);

        $categoryName = $request->category_name;
        $categoryPrefix = strtoupper(substr($categoryName, 0, 3));
        
        $date = now();
        $timestamp = $date->format('ymd');
        
        // Generate unique tag by checking database
        $attempts = 0;
        $maxAttempts = 100;
        
        do {
            $random = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
            $assetTag = $categoryPrefix . '-' . $timestamp . '-' . $random;
            $exists = Asset::where('asset_tag', $assetTag)->exists();
            $attempts++;
        } while ($exists && $attempts < $maxAttempts);
        
        if ($attempts >= $maxAttempts) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to generate unique asset tag after multiple attempts'
            ], 500);
        }
        
        return response()->json([
            'success' => true,
            'asset_tag' => $assetTag
        ]);
    }
}
