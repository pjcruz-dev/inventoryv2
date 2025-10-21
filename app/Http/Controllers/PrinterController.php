<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Printer;
use App\Models\Asset;
use Illuminate\Validation\Rule;

class PrinterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_printers')->only(['index', 'show']);
        $this->middleware('permission:create_printers')->only(['create', 'store']);
        $this->middleware('permission:edit_printers')->only(['edit', 'update']);
        $this->middleware('permission:delete_printers')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Printer::with(['asset.assignedUser', 'asset.department', 'asset.vendor', 'asset.category']);
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('asset', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_tag', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            })->orWhere('type', 'like', "%{$search}%");
        }
        
        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Color support filter
        if ($request->filled('color_support')) {
            $query->where('color_support', $request->color_support);
        }
        
        // Duplex filter
        if ($request->filled('duplex')) {
            $query->where('duplex', $request->duplex);
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
        
        $allowedSortFields = ['type', 'color_support', 'duplex', 'created_at'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }
        
        $printers = $query->paginate(15)->withQueryString();
        
        // Get filter data
        $printerTypes = Printer::select('type')->distinct()->pluck('type');
        $statuses = Asset::select('status')->distinct()->pluck('status');
        
        return view('printers.index', compact('printers', 'printerTypes', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $assets = Asset::whereDoesntHave('printer')
                      ->where('category_id', function($query) {
                          $query->select('id')
                                ->from('asset_categories')
                                ->where('name', 'Printers')
                                ->limit(1);
                      })
                      ->get();
        
        return view('printers.create', compact('assets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id|unique:printers,asset_id',
            'type' => 'required|in:Inkjet,Laser,Dot Matrix,Thermal,3D',
            'color_support' => 'required|boolean',
            'duplex' => 'required|boolean',
        ]);
        
        Printer::create($request->all());
        
        return redirect()->route('printers.index')
                        ->with('success', 'Printer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Printer $printer)
    {
        $printer->load('asset.assignedUser', 'asset.department', 'asset.vendor');
        
        return view('printers.show', compact('printer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Printer $printer)
    {
        $assets = Asset::where('id', $printer->asset_id)
                      ->orWhere(function($query) {
                          $query->whereDoesntHave('printer')
                                ->where('category_id', function($subQuery) {
                                    $subQuery->select('id')
                                            ->from('asset_categories')
                                            ->where('name', 'Printers')
                                            ->limit(1);
                                });
                      })
                      ->get();
        
        return view('printers.edit', compact('printer', 'assets'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Printer $printer)
    {
        $request->validate([
            'asset_id' => ['required', 'exists:assets,id', Rule::unique('printers', 'asset_id')->ignore($printer->id)],
            'type' => 'required|in:Inkjet,Laser,Dot Matrix,Thermal,3D',
            'color_support' => 'required|boolean',
            'duplex' => 'required|boolean',
        ]);
        
        $printer->update($request->all());
        
        return redirect()->route('printers.index')
                        ->with('success', 'Printer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Printer $printer)
    {
        $printer->delete();
        
        return redirect()->route('printers.index')
                        ->with('success', 'Printer deleted successfully.');
    }

    /**
     * Show the form for bulk creating printers.
     */
    public function bulkCreate()
    {
        $assets = Asset::whereDoesntHave('printer')
                      ->where('category_id', function($query) {
                          $query->select('id')
                                ->from('asset_categories')
                                ->where('name', 'Printers')
                                ->limit(1);
                      })
                      ->get();
        
        return view('printers.bulk-create', compact('assets'));
    }

    /**
     * Store bulk created printers.
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'printers' => 'required|array|min:1',
            'printers.*.asset_id' => 'required|exists:assets,id|unique:printers,asset_id',
            'printers.*.type' => 'required|in:Inkjet,Laser,Dot Matrix,Thermal,3D',
            'printers.*.color_support' => 'required|boolean',
            'printers.*.duplex' => 'required|boolean',
        ]);

        $created = 0;
        foreach ($request->printers as $printerData) {
            Printer::create($printerData);
            $created++;
        }

        return redirect()->route('printers.index')
                        ->with('success', "Successfully created {$created} printers.");
    }
}
