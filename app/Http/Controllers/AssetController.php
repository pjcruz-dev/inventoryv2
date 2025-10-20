<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Vendor;
use App\Models\User;
use App\Models\AssetAssignmentConfirmation;
use App\Mail\AssetAssignmentConfirmation as AssetAssignmentConfirmationMail;
use App\Mail\MaintenanceNotification;
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
        $query = Asset::with(['category', 'vendor', 'assignedUser', 'department']);
        
        // Filter assets based on user role
        $user = auth()->user();
        
        // Temporarily comment out filtering to show all assets for testing
        // Exclude assets with Maintenance/For Disposal status and Return movement
        // $query->where(function($q) {
        //     $q->where('status', '!=', 'Maintenance')
        //       ->orWhere(function($subQ) {
        //           $subQ->where('status', 'Maintenance')
        //                ->where('movement', '!=', 'Return');
        //       });
        // })
        // ->where(function($q) {
        //     $q->where('status', '!=', 'For Disposal')
        //       ->orWhere(function($subQ) {
        //           $subQ->where('status', 'For Disposal')
        //                ->where('movement', '!=', 'Return');
        //       });
        // });
        
        // If user has only 'User' role, show only their assigned assets
        if ($user->hasRole('User') && !$user->hasAnyRole(['Admin', 'Super Admin', 'Manager', 'IT Support'])) {
            $query->where('assigned_to', $user->id);
        }
        
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
        
        // Check creation mode
        $creationMode = $request->input('creation_mode', 'single');
        
        if ($creationMode === 'bulk') {
            return $this->storeBulkAssets($request);
        } elseif ($creationMode === 'bulk_serial') {
            return $this->storeBulkAssetsWithSerials($request);
        }
        
        // Single asset creation
        $rules = Asset::validationRules();
        
        // Only validate mobile_number if category is Mobile Devices
        $category = \App\Models\AssetCategory::find($request->category_id);
        if (!$category || strtolower($category->name) !== 'mobile devices') {
            // Remove mobile_number validation if not a mobile device
            unset($rules['mobile_number']);
        }
        
        $validated = $request->validate($rules);

        // Set status and movement based on assignment
        if (!empty($validated['assigned_to'])) {
            // If asset is being assigned during creation
            $validated['status'] = 'Active';
            $validated['movement'] = 'Deployed';
            $validated['assigned_date'] = now();
        } else {
            // Default for unassigned assets - ensure movement is always set
            $validated['status'] = 'Available';
            $validated['movement'] = 'New Arrival';
        }
        
        // Ensure movement is always set to New Arrival for new assets if not already set
        if (empty($validated['movement'])) {
            $validated['movement'] = 'New Arrival';
        }

        // Handle empty vendor_id (convert empty string to null)
        if (isset($validated['vendor_id']) && $validated['vendor_id'] === '') {
            $validated['vendor_id'] = null;
        }

        $asset = Asset::create($validated);
        
        return redirect()->route('assets.index')->with('success', 'Asset created successfully.');
    }
    
    /**
     * Store multiple assets for bulk creation
     */
    private function storeBulkAssets(Request $request)
    {
        // Custom validation rules for bulk creation (serial number not required)
        $rules = Asset::validationRules();
        $rules['serial_number'] = 'nullable|string|max:100'; // Remove unique constraint for bulk
        $rules['quantity'] = 'required|integer|min:1|max:20';
        $rules['creation_mode'] = 'required|string|in:bulk';
        
        // Only validate mobile_number if category is Mobile Devices
        $category = \App\Models\AssetCategory::find($request->category_id);
        if (!$category || strtolower($category->name) !== 'mobile devices') {
            // Remove mobile_number validation if not a mobile device
            unset($rules['mobile_number']);
        }
        
        $validated = $request->validate($rules);
        $quantity = $validated['quantity'];
        
        // Remove bulk-specific fields from asset data
        unset($validated['quantity'], $validated['creation_mode']);
        
        // Set status and movement based on assignment
        if (!empty($validated['assigned_to'])) {
            $validated['status'] = 'Active';
            $validated['movement'] = 'Deployed';
            $validated['assigned_date'] = now();
        } else {
            $validated['status'] = 'Available';
            $validated['movement'] = 'New Arrival';
        }
        
        // Remove serial number for bulk creation
        $validated['serial_number'] = null;
        
        $createdAssets = [];
        $categoryName = \App\Models\AssetCategory::find($validated['category_id'])->name;
        
        // Create multiple assets with unique asset tags
        for ($i = 1; $i <= $quantity; $i++) {
            // Generate unique asset tag for each asset
            $assetTag = $this->generateUniqueAssetTag($categoryName);
            $validated['asset_tag'] = $assetTag;
            
            // Use the original name without sequence numbering
            $validated['name'] = $validated['name'];
            
            $asset = Asset::create($validated);
            $createdAssets[] = $asset;
        }
        
        $message = count($createdAssets) . ' assets created successfully.';
        return redirect()->route('assets.index')->with('success', $message);
    }
    
    /**
     * Generate unique asset tag for bulk creation
     */
    private function generateUniqueAssetTag($categoryName)
    {
        $categoryPrefix = strtoupper(substr($categoryName, 0, 3));
        $date = now();
        $timestamp = $date->format('ymd');
        
        $attempts = 0;
        $maxAttempts = 1000;
        
        do {
            $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $assetTag = $categoryPrefix . '-' . $timestamp . '-' . $random;
            $exists = Asset::where('asset_tag', $assetTag)->exists();
            $attempts++;
        } while ($exists && $attempts < $maxAttempts);
        
        if ($attempts >= $maxAttempts) {
            throw new \Exception('Unable to generate unique asset tag after multiple attempts');
        }
        
        return $assetTag;
    }

    /**
     * Store multiple assets for bulk creation with manual serial numbers
     */
    private function storeBulkAssetsWithSerials(Request $request)
    {
        // Custom validation rules for bulk creation with serial numbers
        $rules = Asset::validationRules();
        // Override required fields to be optional for bulk creation
        $rules['serial_number'] = 'nullable|string|max:100'; // Will be set individually
        $rules['purchase_date'] = 'nullable|date|before_or_equal:today';
        $rules['cost'] = 'nullable|numeric|min:0|max:999999.99';
        $rules['po_number'] = 'nullable|string|max:100';
        $rules['quantity'] = 'required|integer|min:1|max:20';
        $rules['creation_mode'] = 'required|string|in:bulk_serial';
        $rules['serial_numbers'] = 'required|array';
        $rules['serial_numbers.*'] = 'nullable|string|max:100'; // Removed distinct rule
        
        // Only validate mobile_number if category is Mobile Devices
        $category = \App\Models\AssetCategory::find($request->category_id);
        if (!$category || strtolower($category->name) !== 'mobile devices') {
            // Remove mobile_number validation if not a mobile device
            unset($rules['mobile_number']);
        }
        
        $validated = $request->validate($rules);
        $quantity = $validated['quantity'];
        $serialNumbers = $validated['serial_numbers'] ?? [];
        
        // Ensure we have an array and filter out empty values
        if (!is_array($serialNumbers)) {
            return back()->withErrors([
                'serial_numbers' => 'Serial numbers must be provided as an array.'
            ])->withInput();
        }
        
        // Filter and clean serial numbers
        $filteredSerials = [];
        foreach ($serialNumbers as $serial) {
            $trimmedSerial = trim($serial ?? '');
            if ($trimmedSerial !== '') {
                $filteredSerials[] = $trimmedSerial;
            }
        }
        
        // Check for duplicate serial numbers within the submitted array
        $duplicateSerials = [];
        $uniqueSerials = [];
        foreach ($filteredSerials as $serial) {
            if (in_array($serial, $uniqueSerials)) {
                if (!in_array($serial, $duplicateSerials)) {
                    $duplicateSerials[] = $serial;
                }
            } else {
                $uniqueSerials[] = $serial;
            }
        }
        
        if (!empty($duplicateSerials)) {
            return back()->withErrors([
                'serial_numbers' => 'Duplicate serial numbers found: ' . implode(', ', $duplicateSerials) . '. Each serial number must be unique.'
            ])->withInput();
        }
        
        // Convert to integers for proper comparison
        $actualCount = count($uniqueSerials);
        $requiredQuantity = (int) $quantity;
        
        // Validate serial number count
        if ($actualCount !== $requiredQuantity) {
            $message = "You must provide exactly {$requiredQuantity} serial number" . ($requiredQuantity > 1 ? 's' : '') . ". ";
            if ($actualCount === 0) {
                $message .= "No serial numbers were provided.";
            } elseif ($actualCount < $requiredQuantity) {
                $message .= "Only {$actualCount} serial number" . ($actualCount > 1 ? 's were' : ' was') . " provided.";
            } else {
                $message .= "Too many serial numbers provided ({$actualCount}). Please provide exactly {$requiredQuantity}.";
            }
            
            return back()->withErrors([
                'serial_numbers' => $message
            ])->withInput();
        }
        
        // Use the unique serial numbers (after duplicate checking)
        $serialNumbers = $uniqueSerials;
        

        
        // Check for duplicate serial numbers in database
        $existingSerials = Asset::whereIn('serial_number', $serialNumbers)->pluck('serial_number')->toArray();
        if (!empty($existingSerials)) {
            return back()->withErrors(['serial_numbers' => 'Serial numbers already exist: ' . implode(', ', $existingSerials)])->withInput();
        }
        
        // Remove bulk-specific fields from asset data
        unset($validated['quantity'], $validated['creation_mode'], $validated['serial_numbers']);
        
        // Set status and movement based on assignment
        if (!empty($validated['assigned_to'])) {
            $validated['status'] = 'Active';
            $validated['movement'] = 'Deployed';
            $validated['assigned_date'] = now();
        } else {
            $validated['status'] = 'Available';
            $validated['movement'] = 'New Arrival';
        }
        
        $createdAssets = [];
        $categoryName = \App\Models\AssetCategory::find($validated['category_id'])->name;
        
        // Create multiple assets with unique asset tags and serial numbers
        for ($i = 0; $i < $quantity; $i++) {
            // Generate unique asset tag for each asset
            $assetTag = $this->generateUniqueAssetTag($categoryName);
            $validated['asset_tag'] = $assetTag;
            
            // Keep the original name without numbering
            $validated['name'] = $validated['name'];
            
            // Set the serial number for this asset
            $validated['serial_number'] = $serialNumbers[$i];
            
            $asset = Asset::create($validated);
            $createdAssets[] = $asset;
        }
        
        $message = count($createdAssets) . ' assets created successfully with serial numbers.';
        return redirect()->route('assets.index')->with('success', $message);
    }

    /**
     * Check if asset tag is unique (for real-time validation)
     */
    public function checkAssetTagUniqueness(Request $request)
    {
        $request->validate([
            'asset_tag' => 'required|string|max:50',
            'asset_id' => 'nullable|integer' // For edit mode
        ]);
        
        $assetTag = $request->asset_tag;
        $assetId = $request->asset_id;
        
        $query = Asset::where('asset_tag', $assetTag);
        
        // Exclude current asset if editing
        if ($assetId) {
            $query->where('id', '!=', $assetId);
        }
        
        $exists = $query->exists();
        
        return response()->json([
            'unique' => !$exists,
            'message' => $exists ? 'Asset tag already exists' : 'Asset tag is available'
        ]);
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
        
        // Get validation rules
        $rules = Asset::updateValidationRules($asset->id);
        
        // Only validate mobile_number if category is Mobile Devices
        $category = \App\Models\AssetCategory::find($request->category_id);
        if (!$category || strtolower($category->name) !== 'mobile devices') {
            // Remove mobile_number validation if not a mobile device
            unset($rules['mobile_number']);
        }
        
        $validated = $request->validate($rules);

        $asset->update($validated);
        
        // Log the update for debugging
        \Log::info('Asset updated successfully', [
            'asset_id' => $asset->id,
            'asset_tag' => $asset->asset_tag,
            'updated_by' => auth()->user()->email
        ]);
        
        return redirect()->route('assets.index')
            ->with('success', 'Asset updated successfully.')
            ->with('asset_updated', true);
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
        
        // Update asset status to Pending Confirmation and movement to New Arrival
        $asset->update([
            'assigned_to' => $validated['assigned_to'],
            'assigned_date' => $validated['assigned_date'],
            'status' => 'Pending Confirmation',
            'movement' => 'New Arrival'
        ]);

        // Create AssetAssignmentConfirmation record
        $confirmation = AssetAssignmentConfirmation::create([
            'asset_id' => $asset->id,
            'user_id' => $validated['assigned_to'],
            'confirmation_token' => AssetAssignmentConfirmation::generateToken(),
            'status' => 'pending',
            'assigned_at' => $validated['assigned_date'],
            'notes' => $validated['notes'],
            'reminder_count' => 0
        ]);

        // Send confirmation email
        try {
            Mail::to($user->email)->send(new AssetAssignmentConfirmationMail(
                $asset,
                $user,
                $confirmation->confirmation_token,
                false
            ));
        } catch (\Exception $e) {
            \Log::error('Failed to send assignment confirmation email', [
                'asset_id' => $asset->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }

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
        
        // Update asset status to Available and movement to Return
        $asset->update([
            'assigned_to' => null,
            'assigned_date' => null,
            'status' => 'Available',
            'movement' => 'Return'
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
            'Asset returned from ' . ($previousUser ? $previousUser->first_name . ' ' . $previousUser->last_name : 'unknown user') . '. Status updated to Available, movement to Return.',
            $asset->getOriginal(), // old values
            $asset->getAttributes(), // new values
            [
                'previous_user_id' => $previousUser ? $previousUser->id : null,
                'previous_user_name' => $previousUser ? $previousUser->first_name . ' ' . $previousUser->last_name : 'unknown user',
                'previous_status' => $asset->getOriginal('status'),
                'new_status' => 'Available',
                'previous_movement' => $asset->getOriginal('movement'),
                'new_movement' => 'Return',
                'return_processed_by' => auth()->user()->first_name . ' ' . auth()->user()->last_name
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Asset returned successfully. Status updated to Available.'
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
        
        // Update asset status to Pending Confirmation for new assignment
        $asset->update([
            'assigned_to' => $validated['new_assigned_to'],
            'assigned_date' => $validated['assigned_date'],
            'status' => 'Pending Confirmation'
        ]);

        // Create new AssetAssignmentConfirmation record for the new user
        $confirmation = AssetAssignmentConfirmation::create([
            'asset_id' => $asset->id,
            'user_id' => $validated['new_assigned_to'],
            'confirmation_token' => AssetAssignmentConfirmation::generateToken(),
            'status' => 'pending',
            'assigned_at' => $validated['assigned_date'],
            'notes' => $validated['notes'],
            'reminder_count' => 0
        ]);

        // Send confirmation email to new user
        try {
            Mail::to($newUser->email)->send(new AssetAssignmentConfirmationMail(
                $asset,
                $newUser,
                $confirmation->confirmation_token,
                false
            ));
        } catch (\Exception $e) {
            \Log::error('Failed to send reassignment confirmation email', [
                'asset_id' => $asset->id,
                'user_id' => $newUser->id,
                'error' => $e->getMessage()
            ]);
        }

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
        
        $totalValue = $users->flatMap(function($user) {
            return $user->assignedAssets;
        })->sum('cost');
        
        $assetsByCategory = $users->flatMap(function($user) {
            return $user->assignedAssets;
        })->groupBy('category.name')->map(function($assets) {
            return $assets->count();
        });

        return view('assets.print-employee-assets', compact('users', 'totalUsers', 'totalAssets', 'totalValue', 'assetsByCategory'));
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
        $totalValue = $user->assignedAssets->sum('cost');
        
        $assetsByCategory = $user->assignedAssets->groupBy('category.name')->map(function($assets) {
            return $assets->count();
        });

        return view('assets.print-single-employee-assets', compact('user', 'totalAssets', 'totalValue', 'assetsByCategory'));
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

    /**
     * Get vendor information for a specific asset
     */
    public function getAssetVendor(Asset $asset)
    {
        $asset->load('vendor');
        
        return response()->json([
            'success' => true,
            'vendor' => $asset->vendor ? [
                'id' => $asset->vendor->id,
                'name' => $asset->vendor->name,
                'contact_person' => $asset->vendor->contact_person,
                'phone' => $asset->vendor->phone,
                'email' => $asset->vendor->email
            ] : null
        ]);
    }

    /**
     * Send asset to maintenance
     */
    public function sendToMaintenance(Asset $asset)
    {
        $this->authorize('maintenance', $asset);
        
        // Update asset status to Maintenance and movement to Return while retaining assigned user
        $asset->update([
            'status' => 'Maintenance',
            'movement' => 'Return'
        ]);

        // Create enhanced audit log
        $this->activityLogService->logActivity(
            $asset,
            'maintenance_sent',
            'Asset sent to maintenance. Status changed to Maintenance, movement to Return.',
            $asset->getOriginal(), // old values
            $asset->getAttributes(), // new values
            [
                'previous_status' => $asset->getOriginal('status'),
                'new_status' => 'Maintenance',
                'previous_movement' => $asset->getOriginal('movement'),
                'new_movement' => 'Return',
                'assigned_user_retained' => $asset->assigned_to ? true : false,
                'maintenance_processed_by' => auth()->user()->first_name . ' ' . auth()->user()->last_name
            ]
        );

        // Send email notification
        try {
            $assignedUser = $asset->assigned_to ? User::find($asset->assigned_to) : null;
            $processedBy = auth()->user();
            
            // Collect unique email addresses to prevent duplicates
            $emailRecipients = collect();
            
            // Always send to the user who processed the maintenance
            $emailRecipients->push($processedBy->email);
            
            // If there's an assigned user and they're different from the processor, add them too
            if ($assignedUser && $assignedUser->id !== $processedBy->id) {
                $emailRecipients->push($assignedUser->email);
            }
            
            // Send to unique recipients only
            foreach ($emailRecipients->unique() as $email) {
                Mail::to($email)->send(new MaintenanceNotification($asset, $assignedUser, $processedBy));
            }
        } catch (\Exception $e) {
            // Log the error but don't fail the request
            \Log::error('Failed to send maintenance notification email: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Asset sent to maintenance successfully. Status updated to Maintenance. Email notification sent.'
        ]);
    }

    /**
     * Send asset to disposal
     */
    public function sendToDisposal(Asset $asset)
    {
        $this->authorize('dispose', $asset);
        
        // Update asset status to For Disposal and movement to Return
        $asset->update([
            'status' => 'For Disposal',
            'movement' => 'Return'
        ]);

        // Create enhanced audit log
        $this->activityLogService->logActivity(
            $asset,
            'disposal_sent',
            'Asset sent to disposal. Status changed to For Disposal, movement to Return.',
            $asset->getOriginal(), // old values
            $asset->getAttributes(), // new values
            [
                'previous_status' => $asset->getOriginal('status'),
                'new_status' => 'For Disposal',
                'previous_movement' => $asset->getOriginal('movement'),
                'new_movement' => 'Return',
                'disposal_processed_by' => auth()->user()->first_name . ' ' . auth()->user()->last_name
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Asset sent to disposal successfully. Status updated to For Disposal.'
        ]);
    }

    /**
     * Export comprehensive asset data to Excel
     */
    public function exportComprehensive(Request $request)
    {
        $this->authorize('viewAny', Asset::class);
        
        $filters = $request->only(['search', 'category', 'status', 'movement', 'assignment']);
        
        $fileName = 'comprehensive_assets_export_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        return (new \App\Exports\ComprehensiveAssetExport($filters))->download($fileName);
    }
}
