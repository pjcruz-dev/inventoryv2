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
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Printer::with('asset');
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('asset', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_tag', 'like', "%{$search}%");
            })->orWhere('type', 'like', "%{$search}%");
        }
        
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        if ($request->has('color_support') && $request->color_support !== '') {
            $query->where('color_support', $request->color_support);
        }
        
        $printers = $query->paginate(10);
        
        return view('printers.index', compact('printers'));
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
                                ->where('name', 'Printer')
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
                      ->orWhereDoesntHave('printer')
                      ->where('category_id', function($query) {
                          $query->select('id')
                                ->from('asset_categories')
                                ->where('name', 'Printer')
                                ->limit(1);
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
}
