<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Asset;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $query = Maintenance::with(['asset', 'vendor']);

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

        $maintenances = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        // Get filter options
        $statuses = ['Scheduled', 'In Progress', 'Completed', 'On Hold', 'Cancelled'];
        
        return view('maintenance.index', compact('maintenances', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $assets = Asset::whereIn('status', ['Available', 'Assigned', 'Under Maintenance', 'Issue Reported'])
                      ->orderBy('name')
                      ->get();
        $vendors = Vendor::orderBy('name')->get();
        
        return view('maintenance.create', compact('assets', 'vendors'));
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

        // Update asset status if maintenance is started
        if ($request->status === 'In Progress') {
            $asset = Asset::find($request->asset_id);
            $asset->update(['status' => 'Maintenance']);
        }

        return redirect()->route('maintenance.index')
                        ->with('success', 'Maintenance record created successfully.');
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

        $assets = Asset::whereIn('status', ['Available', 'Assigned', 'Maintenance'])
                      ->orderBy('name')
                      ->get();
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
            $asset->update(['status' => 'Maintenance']);
        } elseif ($request->status === 'Completed' && $oldStatus !== 'Completed') {
            // Return asset to previous status or Available
            $asset->update(['status' => $asset->assigned_to ? 'Assigned' : 'Available']);
        }

        return redirect()->route('maintenance.index')
                        ->with('success', 'Maintenance record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Maintenance $maintenance)
    {

        // If maintenance was in progress, update asset status
        if ($maintenance->status === 'In Progress') {
            $asset = Asset::find($maintenance->asset_id);
            $asset->update(['status' => $asset->assigned_to ? 'Assigned' : 'Available']);
        }

        $maintenance->delete();
        
        return redirect()->route('maintenance.index')
                        ->with('success', 'Maintenance record deleted successfully.');
    }
}