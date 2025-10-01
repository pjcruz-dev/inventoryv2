<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AssetCategoryExport;
use App\Imports\AssetCategoryImport;
use Illuminate\Support\Facades\Storage;

class AssetCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_asset_categories')->only(['index', 'show']);
        $this->middleware('permission:create_asset_categories')->only(['create', 'store']);
        $this->middleware('permission:edit_asset_categories')->only(['edit', 'update']);
        $this->middleware('permission:delete_asset_categories')->only(['destroy']);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AssetCategory::withCount('assets');
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $categories = $query->orderBy('name')->paginate(10)->appends(request()->query());
        
        // Log activity
        Log::info('Asset categories viewed', [
            'user_id' => Auth::id(),
            'search' => $request->search,
            'total_categories' => $categories->total()
        ]);
        
        return view('asset-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Log activity
        Log::info('Asset category create form accessed', [
            'user_id' => Auth::id()
        ]);
        
        return view('asset-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:asset_categories,name',
            'description' => 'nullable|string|max:1000'
        ]);

        $category = AssetCategory::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        // Log activity
        Log::info('Asset category created', [
            'user_id' => Auth::id(),
            'category_id' => $category->id,
            'category_name' => $category->name
        ]);

        return redirect()->route('asset-categories.index')
                        ->with('success', 'Asset category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AssetCategory $assetCategory)
    {
        $assetCategory->load(['assets' => function($query) {
            $query->with(['vendor', 'assignedUser'])->latest();
        }]);
        
        // Log activity
        Log::info('Asset category viewed', [
            'user_id' => Auth::id(),
            'category_id' => $assetCategory->id,
            'category_name' => $assetCategory->name
        ]);
        
        return view('asset-categories.show', compact('assetCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AssetCategory $assetCategory)
    {
        // Log activity
        Log::info('Asset category edit form accessed', [
            'user_id' => Auth::id(),
            'category_id' => $assetCategory->id,
            'category_name' => $assetCategory->name
        ]);
        
        return view('asset-categories.edit', compact('assetCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AssetCategory $assetCategory)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:asset_categories,name,' . $assetCategory->id,
            'description' => 'nullable|string|max:1000'
        ]);

        $oldData = $assetCategory->toArray();
        
        $assetCategory->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        // Log activity
        Log::info('Asset category updated', [
            'user_id' => Auth::id(),
            'category_id' => $assetCategory->id,
            'old_data' => $oldData,
            'new_data' => $assetCategory->fresh()->toArray()
        ]);

        return redirect()->route('asset-categories.index')
                        ->with('success', 'Asset category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AssetCategory $assetCategory)
    {
        // Check if category has assets
        if ($assetCategory->assets()->count() > 0) {
            return redirect()->route('asset-categories.index')
                           ->with('error', 'Cannot delete category that has assets assigned to it.');
        }

        $categoryData = $assetCategory->toArray();
        $assetCategory->delete();

        // Log activity
        Log::info('Asset category deleted', [
            'user_id' => Auth::id(),
            'deleted_category' => $categoryData
        ]);

        return redirect()->route('asset-categories.index')
                        ->with('success', 'Asset category deleted successfully.');
    }

    /**
     * Export asset categories to Excel
     */
    public function export()
    {
        // Log activity
        Log::info('Asset categories export initiated', [
            'user_id' => Auth::id()
        ]);
        
        return Excel::download(new AssetCategoryExport, 'asset-categories-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        // Log activity
        Log::info('Asset categories import form accessed', [
            'user_id' => Auth::id()
        ]);
        
        return view('asset-categories.import');
    }

    /**
     * Import asset categories from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            $import = new AssetCategoryImport;
            Excel::import($import, $request->file('file'));
            
            // Log activity
            Log::info('Asset categories import completed', [
                'user_id' => Auth::id(),
                'imported_count' => $import->getRowCount(),
                'filename' => $request->file('file')->getClientOriginalName()
            ]);
            
            return redirect()->route('asset-categories.index')
                           ->with('success', 'Asset categories imported successfully. ' . $import->getRowCount() . ' categories processed.');
        } catch (\Exception $e) {
            // Log error
            Log::error('Asset categories import failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'filename' => $request->file('file')->getClientOriginalName()
            ]);
            
            return redirect()->back()
                           ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Download import template
     */
    public function downloadTemplate()
    {
        // Log activity
        Log::info('Asset categories template downloaded', [
            'user_id' => Auth::id()
        ]);
        
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="asset-categories-template.xlsx"'
        ];
        
        // Create template file path
        $templatePath = storage_path('app/templates/asset-categories-template.xlsx');
        
        if (file_exists($templatePath)) {
            return response()->download($templatePath, 'asset-categories-template.xlsx', $headers);
        }
        
        // If template doesn't exist, create a simple one
        return Excel::download(new AssetCategoryExport(true), 'asset-categories-template.xlsx');
    }
}