<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\AssetTimeline;
use App\Models\User;
use App\Models\Department;

class AssetTimelineController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_timeline')->only(['index', 'show']);
        $this->middleware('permission:create_timeline')->only(['create', 'store']);
    }

    public function index(Request $request)
    {
        $query = AssetTimeline::with(['asset', 'fromUser', 'toUser', 'fromDepartment', 'toDepartment', 'performedBy']);
        
        // Filter by asset if provided
        if ($request->has('asset_id') && $request->asset_id) {
            $query->where('asset_id', $request->asset_id);
        }
        
        // Filter by action if provided
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('performed_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('performed_at', '<=', $request->date_to);
        }
        
        $timeline = $query->orderBy('performed_at', 'desc')->paginate(20);
        $assets = Asset::orderBy('name')->get();
        $actions = ['created', 'assigned', 'unassigned', 'transferred', 'updated', 'confirmed', 'declined'];
        
        return view('timeline.index', compact('timeline', 'assets', 'actions'));
    }
    
    public function show(Asset $asset)
    {
        $timeline = $asset->timeline()->with(['fromUser', 'toUser', 'fromDepartment', 'toDepartment', 'performedBy'])->paginate(10);
        
        return view('timeline.show', compact('asset', 'timeline'));
    }
    
    public function create(Request $request)
    {
        $assets = Asset::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        
        return view('timeline.create', compact('assets', 'users', 'departments'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'action' => 'required|string',
            'notes' => 'nullable|string',
            'from_user_id' => 'nullable|exists:users,id',
            'to_user_id' => 'nullable|exists:users,id',
        ]);
        
        $asset = Asset::findOrFail($request->asset_id);
        
        $asset->createTimelineEntry(
            $request->action,
            $request->from_user_id,
            $request->to_user_id,
            $request->notes
        );
        
        return redirect()->route('timeline.show', $asset)->with('success', 'Timeline entry created successfully.');
    }
}
