<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Asset;
use App\Models\Vendor;
use App\Models\User;
use App\Mail\MaintenanceProgressNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class MaintenanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_maintenance')->only(['index', 'show']);
        $this->middleware('permission:create_maintenance')->only(['create', 'store']);
        $this->middleware('permission:edit_maintenance')->only(['edit', 'update']);
        $this->middleware('permission:delete_maintenance')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = Maintenance::with(['asset.assignedUser', 'asset.department', 'asset.vendor', 'asset.category', 'vendor']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('issue_reported', 'like', "%{$search}%")
                  ->orWhere('repair_action', 'like', "%{$search}%")
                  ->orWhere('remarks', 'like', "%{$search}%")
                  ->orWhereHas('asset', function($assetQuery) use ($search) {
                      $assetQuery->where('name', 'like', "%{$search}%")
                                 ->orWhere('asset_tag', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }
        
        // Vendor filter
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }
        
        // Asset filter
        if ($request->filled('asset_id')) {
            $query->where('asset_id', $request->asset_id);
        }
        
        // Cost range filter
        if ($request->filled('cost_min')) {
            $query->where('cost', '>=', $request->cost_min);
        }
        
        if ($request->filled('cost_max')) {
            $query->where('cost', '<=', $request->cost_max);
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSortFields = ['start_date', 'end_date', 'status', 'cost', 'created_at'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $maintenances = $query->paginate(15)->withQueryString();
        
        // Get filter options
        $statuses = ['Scheduled', 'In Progress', 'Completed', 'On Hold', 'Cancelled'];
        $vendors = Vendor::orderBy('name')->get();
        $assets = Asset::where('status', 'Maintenance')->orderBy('name')->get();
        
        return view('maintenance.index', compact('maintenances', 'statuses', 'vendors', 'assets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $assets = Asset::where('status', 'Maintenance')
                      ->where('movement', 'Return')
                      ->orderBy('name')
                      ->get();
        $vendors = Vendor::orderBy('name')->get();
        
        return view('maintenance.create', compact('assets', 'vendors'));
    }

    /**
     * Show the form for bulk creating maintenance records.
     */
    public function bulkCreate()
    {
        $assets = Asset::where('status', 'Maintenance')
                      ->where('movement', 'Return')
                      ->orderBy('name')
                      ->get();
        $vendors = Vendor::orderBy('name')->get();
        
        return view('maintenance.bulk-create', compact('assets', 'vendors'));
    }

    /**
     * Store bulk created maintenance records.
     */
    public function bulkStore(Request $request)
    {
        // Get only the selected assets from the request
        $selectedAssets = $request->input('selected_assets', []);
        
        if (empty($selectedAssets)) {
            return redirect()->back()
                           ->withErrors(['selected_assets' => 'Please select at least one asset.'])
                           ->withInput();
        }

        // Validate only the selected maintenance records
        $validationRules = [];
        foreach ($selectedAssets as $index => $assetId) {
            $validationRules["maintenance.{$index}.asset_id"] = 'required|exists:assets,id';
            $validationRules["maintenance.{$index}.vendor_id"] = 'nullable|exists:vendors,id';
            $validationRules["maintenance.{$index}.issue_reported"] = 'required|string|max:1000';
            $validationRules["maintenance.{$index}.repair_action"] = 'nullable|string|max:1000';
            $validationRules["maintenance.{$index}.cost"] = 'nullable|numeric|min:0|max:999999.99';
            $validationRules["maintenance.{$index}.start_date"] = 'required|date';
            $validationRules["maintenance.{$index}.end_date"] = 'nullable|date|after_or_equal:maintenance.{$index}.start_date';
            $validationRules["maintenance.{$index}.status"] = 'required|in:Scheduled,In Progress,Completed,On Hold,Cancelled';
            $validationRules["maintenance.{$index}.remarks"] = 'nullable|string|max:1000';
        }

        $request->validate($validationRules);

        $created = 0;
        $errors = [];

        \DB::beginTransaction();
        try {
            $maintenanceRecords = [];
            foreach ($selectedAssets as $index => $assetId) {
                $maintenanceData = $request->input("maintenance.{$index}");
                
                // Create maintenance record
                $maintenance = Maintenance::create($maintenanceData);
                $maintenanceRecords[] = $maintenance;

                // Update asset status to Under Maintenance
                $asset = Asset::find($assetId);
                $asset->update([
                    'status' => 'Under Maintenance',
                    'movement' => 'Transferred'
                ]);

                $created++;
            }

            \DB::commit();

            // Send email notifications for all created maintenance records
            try {
                $processedBy = Auth::user();
                foreach ($maintenanceRecords as $maintenance) {
                    $asset = $maintenance->asset;
                    $assignedUser = $asset->assigned_to ? User::find($asset->assigned_to) : null;
                    
                    // Collect unique email addresses to prevent duplicates
                    $emailRecipients = collect();
                    
                    // Always send to the user who created the maintenance record
                    $emailRecipients->push($processedBy->email);
                    
                    // If there's an assigned user and they're different from the processor, add them too
                    if ($assignedUser && $assignedUser->id !== $processedBy->id) {
                        $emailRecipients->push($assignedUser->email);
                    }
                    
                    // Send to unique recipients only
                    foreach ($emailRecipients->unique() as $email) {
                        Mail::to($email)->send(new MaintenanceProgressNotification($maintenance, $assignedUser, $processedBy, 'created'));
                    }
                }
            } catch (\Exception $e) {
                // Log the error but don't fail the request
                \Log::error('Failed to send bulk maintenance creation notification emails: ' . $e->getMessage());
            }

            $message = "Successfully created {$created} maintenance records. Email notifications sent.";
            if (!empty($errors)) {
                $message .= " " . count($errors) . " records were skipped due to errors.";
                return redirect()->route('maintenance.index')
                               ->with('warning', $message)
                               ->with('errors', $errors);
            }

            return redirect()->route('maintenance.index')
                           ->with('success', $message);

        } catch (\Exception $e) {
            \DB::rollback();
            
            return redirect()->back()
                           ->withErrors(['bulk_create' => 'Failed to create maintenance records: ' . $e->getMessage()])
                           ->withInput();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'issue_reported' => 'required|string|max:1000',
            'repair_action' => 'nullable|string|max:1000',
            'cost' => 'nullable|numeric|min:0|max:999999.99',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:Scheduled,In Progress,Completed,On Hold,Cancelled',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $maintenance = Maintenance::create($request->all());

        // Update asset status and movement when maintenance is created
        $asset = Asset::find($request->asset_id);
        $oldStatus = $asset->status;
        $oldMovement = $asset->movement;
        
        // Set asset to Under Maintenance and movement to Transferred
        $asset->update([
            'status' => 'Under Maintenance',
            'movement' => 'Transferred'
        ]);

        // Create timeline entry for maintenance start
        $asset->createTimelineEntry(
            'maintenance_started',
            null,
            null,
            "Asset sent for maintenance: {$request->issue_reported}",
            ['status' => $oldStatus, 'movement' => $oldMovement],
            ['status' => 'Under Maintenance', 'movement' => 'Transferred']
        );

        // Send email notification
        try {
            $assignedUser = $asset->assigned_to ? User::find($asset->assigned_to) : null;
            $processedBy = Auth::user();
            
            // Collect unique email addresses to prevent duplicates
            $emailRecipients = collect();
            
            // Always send to the user who created the maintenance record
            $emailRecipients->push($processedBy->email);
            
            // If there's an assigned user and they're different from the processor, add them too
            if ($assignedUser && $assignedUser->id !== $processedBy->id) {
                $emailRecipients->push($assignedUser->email);
            }
            
            
            // Send to unique recipients only
            foreach ($emailRecipients->unique() as $email) {
                Mail::to($email)->send(new MaintenanceProgressNotification($maintenance, $assignedUser, $processedBy, 'created'));
            }
        } catch (\Exception $e) {
            // Log the error but don't fail the request
            \Log::error('Failed to send maintenance creation notification email: ' . $e->getMessage());
        }

        return redirect()->route('maintenance.index')
                        ->with('success', 'Maintenance record created successfully. Asset status updated to Under Maintenance. Email notification sent.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Maintenance $maintenance)
    {

        $maintenance->load(['asset', 'vendor']);
        
        return view('maintenance.show', compact('maintenance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Maintenance $maintenance)
    {
        // Get assets that are available for maintenance, plus the current asset
        $assets = Asset::where(function($query) use ($maintenance) {
            $query->whereIn('status', ['Available', 'Assigned', 'Issue Reported', 'Maintenance', 'Under Maintenance'])
                  ->orWhere('id', $maintenance->asset_id); // Always include the current asset
        })->orderBy('name')->get();
        
        $vendors = Vendor::orderBy('name')->get();
        
        return view('maintenance.edit', compact('maintenance', 'assets', 'vendors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Maintenance $maintenance)
    {

        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'issue_reported' => 'required|string|max:1000',
            'repair_action' => 'nullable|string|max:1000',
            'cost' => 'nullable|numeric|min:0|max:999999.99',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:Scheduled,In Progress,Completed,On Hold,Cancelled',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $maintenance->status;
        $maintenance->update($request->all());

        // Update asset status based on maintenance status
        $asset = Asset::find($request->asset_id);
        
        if ($request->status === 'In Progress' && $oldStatus !== 'In Progress') {
            // Start maintenance - set to Under Maintenance
            $oldAssetStatus = $asset->status;
            $oldAssetMovement = $asset->movement;
            
            $asset->update([
                'status' => 'Maintenance',
                'movement' => 'Deployed'
            ]);
            
            // Create timeline entry
            $asset->createTimelineEntry(
                'maintenance_started',
                null,
                null,
                "Maintenance started: {$request->issue_reported}",
                ['status' => $oldAssetStatus, 'movement' => $oldAssetMovement],
                ['status' => 'Maintenance', 'movement' => 'Deployed']
            );
            
        } elseif ($request->status === 'Completed' && $oldStatus !== 'Completed') {
            // Complete maintenance - restore asset to appropriate status
            $oldAssetStatus = $asset->status;
            $oldAssetMovement = $asset->movement;
            
            // Determine appropriate status based on assignment
            $newStatus = $asset->assigned_to ? 'Active' : 'Available';
            $newMovement = 'Deployed';
            
            $asset->update([
                'status' => $newStatus,
                'movement' => $newMovement
            ]);
            
            // Create timeline entry
            $asset->createTimelineEntry(
                'maintenance_completed',
                null,
                null,
                "Maintenance completed: {$request->repair_action}",
                ['status' => $oldAssetStatus, 'movement' => $oldAssetMovement],
                ['status' => $newStatus, 'movement' => $newMovement]
            );
        }

        // Send email notification for maintenance update
        try {
            $assignedUser = $asset->assigned_to ? User::find($asset->assigned_to) : null;
            $processedBy = Auth::user();
            
            // Collect unique email addresses to prevent duplicates
            $emailRecipients = collect();
            
            // Always send to the user who updated the maintenance record
            $emailRecipients->push($processedBy->email);
            
            // If there's an assigned user and they're different from the processor, add them too
            if ($assignedUser && $assignedUser->id !== $processedBy->id) {
                $emailRecipients->push($assignedUser->email);
            }
            
            // Send to unique recipients only
            foreach ($emailRecipients->unique() as $email) {
                Mail::to($email)->send(new MaintenanceProgressNotification($maintenance, $assignedUser, $processedBy, 'updated'));
            }
        } catch (\Exception $e) {
            // Log the error but don't fail the request
            \Log::error('Failed to send maintenance update notification email: ' . $e->getMessage());
        }

        return redirect()->route('maintenance.index')
                        ->with('success', 'Maintenance record updated successfully. Email notification sent.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Maintenance $maintenance)
    {

        // If maintenance was in progress, restore asset status
        if ($maintenance->status === 'In Progress') {
            $asset = Asset::find($maintenance->asset_id);
            $oldAssetStatus = $asset->status;
            $oldAssetMovement = $asset->movement;
            
            // Restore to appropriate status
            $newStatus = $asset->assigned_to ? 'Assigned' : 'Available';
            $newMovement = $asset->assigned_to ? 'Deployed Tagged' : 'Returned';
            
            $asset->update([
                'status' => $newStatus,
                'movement' => $newMovement
            ]);
            
            // Create timeline entry
            $asset->createTimelineEntry(
                'maintenance_cancelled',
                null,
                null,
                "Maintenance record deleted - asset restored",
                ['status' => $oldAssetStatus, 'movement' => $oldAssetMovement],
                ['status' => $newStatus, 'movement' => $newMovement]
            );
        }

        $maintenance->delete();
        
        return redirect()->route('maintenance.index')
                        ->with('success', 'Maintenance record deleted successfully.');
    }
}