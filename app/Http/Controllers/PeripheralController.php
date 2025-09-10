<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peripheral;
use App\Models\Asset;
use Illuminate\Validation\Rule;

class PeripheralController extends Controller
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
        $query = Peripheral::with('asset');
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('asset', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_tag', 'like', "%{$search}%");
            })->orWhere('type', 'like', "%{$search}%")
              ->orWhere('interface', 'like', "%{$search}%");
        }
        
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        if ($request->has('interface') && $request->interface) {
            $query->where('interface', $request->interface);
        }
        
        $peripherals = $query->paginate(10);
        
        return view('peripherals.index', compact('peripherals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $assets = Asset::whereDoesntHave('peripheral')
                      ->where('category_id', function($query) {
                          $query->select('id')
                                ->from('asset_categories')
                                ->where('name', 'Peripherals')
                                ->limit(1);
                      })
                      ->get();
        
        return view('peripherals.create', compact('assets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id|unique:peripherals,asset_id',
            'type' => 'required|string|max:255',
            'interface' => 'required|string|max:255',
        ]);

        Peripheral::create($request->all());

        return redirect()->route('peripherals.index')
                        ->with('success', 'Peripheral created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Peripheral $peripheral)
    {
        $peripheral->load('asset.category', 'asset.vendor', 'asset.assignedUser', 'asset.department');
        return view('peripherals.show', compact('peripheral'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Peripheral $peripheral)
    {
        $assets = Asset::where('id', $peripheral->asset_id)
                      ->orWhereDoesntHave('peripheral')
                      ->where('category_id', function($query) {
                          $query->select('id')
                                ->from('asset_categories')
                                ->where('name', 'Peripherals')
                                ->limit(1);
                      })
                      ->get();
        
        return view('peripherals.edit', compact('peripheral', 'assets'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Peripheral $peripheral)
    {
        $request->validate([
            'asset_id' => [
                'required',
                'exists:assets,id',
                Rule::unique('peripherals')->ignore($peripheral->id)
            ],
            'type' => 'required|string|max:255',
            'interface' => 'required|string|max:255',
        ]);

        $peripheral->update($request->all());

        return redirect()->route('peripherals.index')
                        ->with('success', 'Peripheral updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Peripheral $peripheral)
    {
        $peripheral->delete();

        return redirect()->route('peripherals.index')
                        ->with('success', 'Peripheral deleted successfully.');
    }
}