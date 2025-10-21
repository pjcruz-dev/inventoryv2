<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Monitor;
use App\Models\Asset;
use Illuminate\Validation\Rule;

class MonitorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_monitors')->only(['index', 'show']);
        $this->middleware('permission:create_monitors')->only(['create', 'store']);
        $this->middleware('permission:edit_monitors')->only(['edit', 'update']);
        $this->middleware('permission:delete_monitors')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Monitor::with(['asset.assignedUser', 'asset.department', 'asset.vendor', 'asset.category']);
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('asset', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_tag', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            })->orWhere('size', 'like', "%{$search}%")
              ->orWhere('resolution', 'like', "%{$search}%")
              ->orWhere('panel_type', 'like', "%{$search}%");
        }
        
        // Panel type filter
        if ($request->filled('panel_type')) {
            $query->where('panel_type', $request->panel_type);
        }
        
        // Size filter
        if ($request->filled('size')) {
            $query->where('size', 'like', "%{$request->size}%");
        }
        
        // Resolution filter
        if ($request->filled('resolution')) {
            $query->where('resolution', $request->resolution);
        }
        
        // Assignment filter
        if ($request->filled('assigned')) {
            if ($request->assigned === 'yes') {
                $query->whereHas('asset', function($q) {
                    $q->whereNotNull('assigned_to');
                });
            } elseif ($request->assigned === 'no') {
                $query->whereHas('asset', function($q) {
                    $q->whereNull('assigned_to');
                });
            }
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->whereHas('asset', function($q) use ($request) {
                $q->where('status', $request->status);
            });
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSortFields = ['size', 'resolution', 'panel_type', 'created_at'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }
        
        $monitors = $query->paginate(15)->withQueryString();
        
        // Get filter data
        $panelTypes = Monitor::select('panel_type')->distinct()->pluck('panel_type');
        $sizes = Monitor::select('size')->distinct()->orderBy('size')->pluck('size');
        $resolutions = Monitor::select('resolution')->distinct()->pluck('resolution');
        $statuses = Asset::select('status')->distinct()->pluck('status');
        
        return view('monitors.index', compact('monitors', 'panelTypes', 'sizes', 'resolutions', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $assets = Asset::whereDoesntHave('monitor')
                      ->where('category_id', function($query) {
                          $query->select('id')
                                ->from('asset_categories')
                                ->where('name', 'Monitors')
                                ->limit(1);
                      })
                      ->get();
        
        return view('monitors.create', compact('assets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id|unique:monitors,asset_id',
            'size' => 'required|string|max:50',
            'resolution' => 'required|string|max:50',
            'panel_type' => 'required|in:LCD,LED,OLED,CRT,Plasma',
        ]);
        
        Monitor::create($request->all());
        
        return redirect()->route('monitors.index')
                        ->with('success', 'Monitor created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Monitor $monitor)
    {
        $monitor->load('asset.assignedUser', 'asset.department', 'asset.vendor');
        
        return view('monitors.show', compact('monitor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Monitor $monitor)
    {
        $assets = Asset::where('id', $monitor->asset_id)
                      ->orWhere(function($query) {
                          $query->whereDoesntHave('monitor')
                                ->where('category_id', function($subQuery) {
                                    $subQuery->select('id')
                                            ->from('asset_categories')
                                            ->where('name', 'Monitors')
                                            ->limit(1);
                                });
                      })
                      ->get();
        
        return view('monitors.edit', compact('monitor', 'assets'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Monitor $monitor)
    {
        $request->validate([
            'asset_id' => ['required', 'exists:assets,id', Rule::unique('monitors', 'asset_id')->ignore($monitor->id)],
            'size' => 'required|string|max:50',
            'resolution' => 'required|string|max:50',
            'panel_type' => 'required|in:LCD,LED,OLED,CRT,Plasma',
        ]);
        
        $monitor->update($request->all());
        
        return redirect()->route('monitors.index')
                        ->with('success', 'Monitor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Monitor $monitor)
    {
        $monitor->delete();
        
        return redirect()->route('monitors.index')
                        ->with('success', 'Monitor deleted successfully.');
    }

    /**
     * Show the form for bulk creating monitors.
     */
    public function bulkCreate()
    {
        $assets = Asset::whereDoesntHave('monitor')
                      ->where('category_id', function($query) {
                          $query->select('id')
                                ->from('asset_categories')
                                ->where('name', 'Monitors')
                                ->limit(1);
                      })
                      ->get();
        
        return view('monitors.bulk-create', compact('assets'));
    }

    /**
     * Store bulk created monitors.
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'monitors' => 'required|array|min:1',
            'monitors.*.asset_id' => 'required|exists:assets,id|unique:monitors,asset_id',
            'monitors.*.size' => 'required|string|max:50',
            'monitors.*.resolution' => 'required|string|max:50',
            'monitors.*.panel_type' => 'required|in:LCD,LED,OLED,CRT,Plasma',
        ]);

        $created = 0;
        foreach ($request->monitors as $monitorData) {
            Monitor::create($monitorData);
            $created++;
        }

        return redirect()->route('monitors.index')
                        ->with('success', "Successfully created {$created} monitors.");
    }
}
