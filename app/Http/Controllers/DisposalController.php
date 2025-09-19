<?php

namespace App\Http\Controllers;

use App\Models\Disposal;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisposalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_disposal')->only(['index', 'show']);
        $this->middleware('permission:create_disposal')->only(['create', 'store']);
        $this->middleware('permission:edit_disposal')->only(['edit', 'update']);
        $this->middleware('permission:delete_disposal')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Permission is checked by middleware

        $query = Disposal::with(['asset']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('disposal_type', 'like', "%{$search}%")
                  ->orWhere('remarks', 'like', "%{$search}%")
                  ->orWhereHas('asset', function($assetQuery) use ($search) {
                      $assetQuery->where('name', 'like', "%{$search}%")
                                 ->orWhere('asset_tag', 'like', "%{$search}%");
                  });
            });
        }

        // Disposal type filter
        if ($request->filled('disposal_type')) {
            $query->where('disposal_type', $request->disposal_type);
        }

        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('disposal_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('disposal_date', '<=', $request->end_date);
        }

        // Value range filter
        if ($request->filled('min_value')) {
            $query->where('disposal_value', '>=', $request->min_value);
        }
        if ($request->filled('max_value')) {
            $query->where('disposal_value', '<=', $request->max_value);
        }

        $disposals = $query->orderBy('disposal_date', 'desc')->paginate(15)->withQueryString();
        
        // Get filter options
        $disposalTypes = ['Sale', 'Donation', 'Recycling', 'Destruction', 'Trade-in', 'Return to Vendor'];
        
        return view('disposal.index', compact('disposals', 'disposalTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Permission is checked by middleware

        $assets = Asset::whereIn('status', ['Available', 'Retired', 'Damaged'])
                      ->where('status', '!=', 'Disposed') // Exclude already disposed assets
                      ->orderBy('name')
                      ->get();
        
        return view('disposal.create', compact('assets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Permission is checked by middleware

        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'disposal_date' => 'required|date',
            'disposal_type' => 'required|in:Sold,Donated,Recycled,Destroyed,Lost,Stolen,Trade-in,Return to Vendor,Upgrade Replacement,Damaged Beyond Repair,End of Life,Security Risk,Theft/Loss,Obsolete Technology,Cost of Repair Exceeds Value,Recycling,Donation',
            'disposal_value' => 'nullable|numeric|min:0|max:999999.99',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $disposal = Disposal::create($request->all());

        // Update asset status to disposed
        $asset = Asset::find($request->asset_id);
        $oldAssetStatus = $asset->status;
        $oldAssetMovement = $asset->movement;
        
        $asset->update([
            'status' => 'Disposed',
            'movement' => 'Disposed',
            'assigned_to' => null // Remove any assignment
        ]);
        
        // Create timeline entry
        $asset->createTimelineEntry(
            'asset_disposed',
            null,
            null,
            "Asset disposed: {$request->disposal_type} - {$request->remarks}",
            ['status' => $oldAssetStatus, 'movement' => $oldAssetMovement],
            ['status' => 'Disposed', 'movement' => 'Disposed']
        );

        return redirect()->route('disposal.index')
                        ->with('success', 'Disposal record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Disposal $disposal)
    {
        // Permission is checked by middleware

        $disposal->load(['asset.maintenance']);
        
        return view('disposal.show', compact('disposal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Disposal $disposal)
    {
        // Permission is checked by middleware

        $assets = Asset::where(function($query) use ($disposal) {
                      $query->whereIn('status', ['Available', 'Retired', 'Damaged', 'Disposed'])
                            ->orWhere('id', $disposal->asset_id);
                  })
                  ->orderBy('name')
                  ->get();
        
        return view('disposal.edit', compact('disposal', 'assets'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Disposal $disposal)
    {
        // Permission is checked by middleware

        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'disposal_date' => 'required|date',
            'disposal_type' => 'required|in:Sold,Donated,Recycled,Destroyed,Lost,Stolen,Trade-in,Return to Vendor,Upgrade Replacement,Damaged Beyond Repair,End of Life,Security Risk,Theft/Loss,Obsolete Technology,Cost of Repair Exceeds Value,Recycling,Donation',
            'disposal_value' => 'nullable|numeric|min:0|max:999999.99',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $disposal->update($request->all());

        // Ensure asset status is disposed
        $asset = Asset::find($request->asset_id);
        if ($asset->status !== 'Disposed') {
            $oldAssetStatus = $asset->status;
            $oldAssetMovement = $asset->movement;
            
            $asset->update([
                'status' => 'Disposed',
                'movement' => 'Disposed',
                'assigned_to' => null
            ]);
            
            // Create timeline entry
            $asset->createTimelineEntry(
                'asset_disposed',
                null,
                null,
                "Asset disposal updated: {$request->disposal_type} - {$request->remarks}",
                ['status' => $oldAssetStatus, 'movement' => $oldAssetMovement],
                ['status' => 'Disposed', 'movement' => 'Disposed']
            );
        }

        return redirect()->route('disposal.index')
                        ->with('success', 'Disposal record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Disposal $disposal)
    {
        // Permission is checked by middleware

        // Revert asset status from disposed
        $asset = Asset::find($disposal->asset_id);
        if ($asset->status === 'Disposed') {
            $oldAssetStatus = $asset->status;
            $oldAssetMovement = $asset->movement;
            
            // Determine appropriate status based on assignment
            $newStatus = $asset->assigned_to ? 'Assigned' : 'Available';
            $newMovement = $asset->assigned_to ? 'Deployed Tagged' : 'Returned';
            
            $asset->update([
                'status' => $newStatus,
                'movement' => $newMovement
            ]);
            
            // Create timeline entry
            $asset->createTimelineEntry(
                'asset_disposal_reverted',
                null,
                null,
                "Asset disposal record deleted - status reverted",
                ['status' => $oldAssetStatus, 'movement' => $oldAssetMovement],
                ['status' => $newStatus, 'movement' => $newMovement]
            );
        }

        $disposal->delete();
        
        return redirect()->route('disposal.index')
                        ->with('success', 'Disposal record deleted successfully. Asset status has been reverted.');
    }
}