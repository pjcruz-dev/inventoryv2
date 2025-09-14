<?php

namespace App\Http\Controllers;

use App\Models\AssetAssignment;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AssetAssignmentExport;
use App\Imports\AssetAssignmentImport;

class AssetAssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_asset_assignments')->only(['index', 'show']);
        $this->middleware('permission:create_asset_assignments')->only(['create', 'store']);
        $this->middleware('permission:edit_asset_assignments')->only(['edit', 'update']);
        $this->middleware('permission:delete_asset_assignments')->only(['destroy']);
        $this->middleware('permission:manage_asset_assignments')->only(['markAsReturned', 'sendReminder', 'export', 'import']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AssetAssignment::with(['asset', 'user', 'assignedBy']);
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('asset', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_tag', 'like', "%{$search}%");
            })->orWhereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Asset category filter
        if ($request->has('asset_category') && $request->asset_category) {
            $query->whereHas('asset', function($q) use ($request) {
                $q->where('category_id', $request->asset_category);
            });
        }
        
        $assignments = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get categories for filter dropdown
        $categories = AssetCategory::orderBy('name')->get();
        
        // Log activity
        Log::info('Asset assignments index accessed', [
            'user_id' => Auth::id(),
            'search' => $request->search,
            'status_filter' => $request->status
        ]);
        
        return view('asset-assignments.index', compact('assignments', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $assets = Asset::where('status', 'Available')->get();
        $users = User::where('status', 1)->get();
        
        // Log activity
        Log::info('Asset assignment create form accessed', [
            'user_id' => Auth::id()
        ]);
        
        return view('asset-assignments.create', compact('assets', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'user_id' => 'required|exists:users,id',
            'assigned_date' => 'required|date',
            'return_date' => 'nullable|date|after:assigned_date',
            'notes' => 'nullable|string|max:1000'
        ]);
        
        // Check if asset is available
        $asset = Asset::findOrFail($request->asset_id);
        if ($asset->status !== 'Available') {
            return redirect()->back()
                           ->with('error', 'Asset is not available for assignment.')
                           ->withInput();
        }
        
        $assignment = AssetAssignment::create([
            'asset_id' => $request->asset_id,
            'user_id' => $request->user_id,
            'assigned_by' => Auth::id(),
            'assigned_date' => $request->assigned_date,
            'return_date' => $request->return_date,
            'status' => 'pending',
            'notes' => $request->notes
        ]);
        
        // Update asset status
        $asset->update([
            'status' => 'Assigned',
            'assigned_to' => $request->user_id,
            'assigned_date' => $request->assigned_date
        ]);
        
        // Log activity
        Log::info('Asset assignment created', [
            'user_id' => Auth::id(),
            'assignment_id' => $assignment->id,
            'asset_id' => $assignment->asset_id,
            'assigned_to' => $assignment->user_id
        ]);
        
        return redirect()->route('asset-assignments.index')
                        ->with('success', 'Asset assignment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AssetAssignment $assetAssignment)
    {
        $assetAssignment->load(['asset', 'user', 'assignedBy', 'confirmation']);
        
        // Log activity
        Log::info('Asset assignment viewed', [
            'user_id' => Auth::id(),
            'assignment_id' => $assetAssignment->id
        ]);
        
        return view('asset-assignments.show', compact('assetAssignment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AssetAssignment $assetAssignment)
    {
        $assets = Asset::all();
        $users = User::where('status', 1)->get();
        
        // Log activity
        Log::info('Asset assignment edit form accessed', [
            'user_id' => Auth::id(),
            'assignment_id' => $assetAssignment->id
        ]);
        
        return view('asset-assignments.edit', compact('assetAssignment', 'assets', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AssetAssignment $assetAssignment)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'user_id' => 'required|exists:users,id',
            'assigned_date' => 'required|date',
            'return_date' => 'nullable|date|after:assigned_date',
            'status' => 'required|in:pending,confirmed,declined,returned',
            'notes' => 'nullable|string|max:1000'
        ]);
        
        $oldData = $assetAssignment->toArray();
        
        $assetAssignment->update([
            'asset_id' => $request->asset_id,
            'user_id' => $request->user_id,
            'assigned_date' => $request->assigned_date,
            'return_date' => $request->return_date,
            'status' => $request->status,
            'notes' => $request->notes
        ]);
        
        // Update asset status if assignment status changed
        if ($request->status === 'returned') {
            $assetAssignment->asset->update([
                'status' => 'Available',
                'assigned_to' => null,
                'assigned_date' => null
            ]);
        }
        
        // Log activity
        Log::info('Asset assignment updated', [
            'user_id' => Auth::id(),
            'assignment_id' => $assetAssignment->id,
            'old_data' => $oldData,
            'new_data' => $assetAssignment->fresh()->toArray()
        ]);
        
        return redirect()->route('asset-assignments.index')
                        ->with('success', 'Asset assignment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AssetAssignment $assetAssignment)
    {
        $assignmentData = $assetAssignment->toArray();
        
        // Update asset status if assignment is being deleted
        if ($assetAssignment->status !== 'returned') {
            $assetAssignment->asset->update([
                'status' => 'Available',
                'assigned_to' => null,
                'assigned_date' => null
            ]);
        }
        
        $assetAssignment->delete();
        
        // Log activity
        Log::info('Asset assignment deleted', [
            'user_id' => Auth::id(),
            'deleted_assignment' => $assignmentData
        ]);
        
        return redirect()->route('asset-assignments.index')
                        ->with('success', 'Asset assignment deleted successfully.');
    }
    
    /**
     * Export asset assignments to Excel
     */
    public function export()
    {
        // Log activity
        Log::info('Asset assignments export initiated', [
            'user_id' => Auth::id()
        ]);
        
        return Excel::download(new AssetAssignmentExport, 'asset-assignments-' . date('Y-m-d') . '.xlsx');
    }
    
    /**
     * Download Excel template for import
     */
    public function downloadTemplate()
    {
        // Log activity
        Log::info('Asset assignments template downloaded', [
            'user_id' => Auth::id()
        ]);
        
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="asset-assignments-template.xlsx"'
        ];
        
        // Create a simple template with headers
        $templateData = [
            ['Asset Tag', 'User Email', 'Assigned Date', 'Return Date', 'Notes'],
            ['AST001', 'user@example.com', '2024-01-01', '2024-12-31', 'Sample assignment']
        ];
        
        return Excel::download(new class($templateData) implements \Maatwebsite\Excel\Concerns\FromArray {
            private $data;
            public function __construct($data) { $this->data = $data; }
            public function array(): array { return $this->data; }
        }, 'asset-assignments-template.xlsx', null, $headers);
    }
    
    /**
     * Show import form
     */
    public function importForm()
    {
        // Log activity
        Log::info('Asset assignments import form accessed', [
            'user_id' => Auth::id()
        ]);
        
        return view('asset-assignments.import');
    }
    
    /**
     * Import asset assignments from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);
        
        try {
            $import = new AssetAssignmentImport;
            Excel::import($import, $request->file('file'));
            
            // Log activity
            Log::info('Asset assignments import completed', [
                'user_id' => Auth::id(),
                'imported_count' => $import->getRowCount(),
                'filename' => $request->file('file')->getClientOriginalName()
            ]);
            
            return redirect()->route('asset-assignments.index')
                           ->with('success', 'Asset assignments imported successfully. ' . $import->getRowCount() . ' assignments processed.');
        } catch (\Exception $e) {
            // Log error
            Log::error('Asset assignments import failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'filename' => $request->file('file')->getClientOriginalName()
            ]);
            
            return redirect()->back()
                           ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}