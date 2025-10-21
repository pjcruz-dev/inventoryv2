<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Computer;
use App\Models\Asset;
use Illuminate\Validation\Rule;

class ComputerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_computers')->only(['index', 'show']);
        $this->middleware('permission:create_computers')->only(['create', 'store']);
        $this->middleware('permission:edit_computers')->only(['edit', 'update']);
        $this->middleware('permission:delete_computers')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Computer::with(['asset.assignedUser', 'asset.department', 'asset.vendor', 'asset.category']);
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('asset', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_tag', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            })->orWhere('processor', 'like', "%{$search}%")
              ->orWhere('memory', 'like', "%{$search}%")
              ->orWhere('storage', 'like', "%{$search}%")
              ->orWhere('operating_system', 'like', "%{$search}%")
              ->orWhere('computer_type', 'like', "%{$search}%");
        }
        
        // Computer type filter
        if ($request->filled('computer_type')) {
            $query->where('computer_type', $request->computer_type);
        }
        
        // Operating system filter
        if ($request->filled('operating_system')) {
            $query->where('operating_system', 'like', "%{$request->operating_system}%");
        }
        
        // Processor filter
        if ($request->filled('processor')) {
            $query->where('processor', 'like', "%{$request->processor}%");
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
        
        $allowedSortFields = ['computer_type', 'processor', 'operating_system', 'created_at'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }
        
        $computers = $query->paginate(15)->withQueryString();
        
        // Get filter data
        $computerTypes = Computer::select('computer_type')->distinct()->pluck('computer_type');
        $operatingSystems = Computer::select('operating_system')->distinct()->pluck('operating_system');
        $statuses = Asset::select('status')->distinct()->pluck('status');
        
        return view('computers.index', compact('computers', 'computerTypes', 'operatingSystems', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $assets = Asset::whereDoesntHave('computer')
                      ->where('category_id', function($query) {
                          $query->select('id')
                                ->from('asset_categories')
                                ->where('name', 'Computer Hardware')
                                ->limit(1);
                      })
                      ->get();
        
        return view('computers.create', compact('assets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id|unique:computers,asset_id',
            'processor' => 'required|string|max:255',
            'memory' => 'required|string|max:100',
            'storage' => 'required|string|max:100',
            'operating_system' => 'required|string|max:255',
            'computer_type' => 'required|in:Desktop,Laptop,Server,Workstation',
        ]);
        
        Computer::create($request->all());
        
        return redirect()->route('computers.index')
                        ->with('success', 'Computer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Computer $computer)
    {
        $computer->load('asset.assignedUser', 'asset.department', 'asset.vendor');
        
        return view('computers.show', compact('computer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Computer $computer)
    {
        $assets = Asset::where('id', $computer->asset_id)
                      ->orWhere(function($query) {
                          $query->whereDoesntHave('computer')
                                ->where('category_id', function($subQuery) {
                                    $subQuery->select('id')
                                            ->from('asset_categories')
                                            ->where('name', 'Computer Hardware')
                                            ->limit(1);
                                });
                      })
                      ->get();
        
        return view('computers.edit', compact('computer', 'assets'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Computer $computer)
    {
        $request->validate([
            'asset_id' => ['required', 'exists:assets,id', Rule::unique('computers', 'asset_id')->ignore($computer->id)],
            'processor' => 'required|string|max:255',
            'memory' => 'required|string|max:100',
            'storage' => 'required|string|max:100',
            'operating_system' => 'required|string|max:255',
            'computer_type' => 'required|in:Desktop,Laptop,Server,Workstation',
        ]);
        
        $computer->update($request->all());
        
        return redirect()->route('computers.index')
                        ->with('success', 'Computer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Computer $computer)
    {
        $computer->delete();
        
        return redirect()->route('computers.index')
                        ->with('success', 'Computer deleted successfully.');
    }

    /**
     * Show the form for bulk creating computers.
     */
    public function bulkCreate()
    {
        $assets = Asset::whereDoesntHave('computer')
                      ->where('category_id', function($query) {
                          $query->select('id')
                                ->from('asset_categories')
                                ->where('name', 'Computer Hardware')
                                ->limit(1);
                      })
                      ->get();
        
        return view('computers.bulk-create', compact('assets'));
    }

    /**
     * Store bulk created computers.
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'computers' => 'required|array|min:1',
            'computers.*.asset_id' => 'required|exists:assets,id|unique:computers,asset_id',
            'computers.*.processor' => 'required|string|max:255',
            'computers.*.memory' => 'required|string|max:100',
            'computers.*.storage' => 'required|string|max:100',
            'computers.*.operating_system' => 'required|string|max:255',
            'computers.*.computer_type' => 'required|in:Desktop,Laptop,Server,Workstation',
        ]);

        $created = 0;
        foreach ($request->computers as $computerData) {
            Computer::create($computerData);
            $created++;
        }

        return redirect()->route('computers.index')
                        ->with('success', "Successfully created {$created} computers.");
    }
}
