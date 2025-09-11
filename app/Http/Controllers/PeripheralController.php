<?php

namespace App\Http\Controllers;

use App\Models\Peripheral;
use App\Models\Asset;
use Illuminate\Http\Request;

class PeripheralController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $peripherals = Peripheral::with(['asset.assignedUser', 'asset.department', 'asset.vendor'])
                                ->paginate(10);
        
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
            'type' => 'required|in:Mouse,Keyboard,Webcam,Headset,Speaker,Microphone,USB Hub,External Drive,Other',
            'connectivity' => 'required|in:USB,Bluetooth,Wireless,Wired',
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
        $peripheral->load('asset.assignedUser', 'asset.department', 'asset.vendor');
        
        return view('peripherals.show', compact('peripheral'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Peripheral $peripheral)
    {
        $assets = Asset::where('id', $peripheral->asset_id)
                      ->orWhere(function($query) {
                          $query->whereDoesntHave('peripheral')
                                ->where('category_id', function($subQuery) {
                                    $subQuery->select('id')
                                            ->from('asset_categories')
                                            ->where('name', 'Peripherals')
                                            ->limit(1);
                                });
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
            'asset_id' => 'required|exists:assets,id|unique:peripherals,asset_id,' . $peripheral->id,
            'type' => 'required|in:Mouse,Keyboard,Webcam,Headset,Speaker,Microphone,USB Hub,External Drive,Other',
            'connectivity' => 'required|in:USB,Bluetooth,Wireless,Wired',
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