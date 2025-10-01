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
                  ->orWhere('asset_tag', 'like', "%{$search}%");
            })->orWhere('size', 'like', "%{$search}%")
              ->orWhere('resolution', 'like', "%{$search}%")
              ->orWhere('panel_type', 'like', "%{$search}%");
        }
        
        if ($request->has('panel_type') && $request->panel_type) {
            $query->where('panel_type', $request->panel_type);
        }
        
        $monitors = $query->paginate(10)->appends(request()->query());
        
        return view('monitors.index', compact('monitors'));
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
