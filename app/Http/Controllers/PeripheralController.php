<?php

namespace App\Http\Controllers;

use App\Models\Peripheral;
use App\Models\Asset;
use Illuminate\Http\Request;

class PeripheralController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_peripherals')->only(['index', 'show']);
        $this->middleware('permission:create_peripherals')->only(['create', 'store']);
        $this->middleware('permission:edit_peripherals')->only(['edit', 'update']);
        $this->middleware('permission:delete_peripherals')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Peripheral::with(['asset.assignedUser', 'asset.department', 'asset.vendor', 'asset.category']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('asset', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_tag', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            })->orWhere('type', 'like', "%{$search}%")
              ->orWhere('interface', 'like', "%{$search}%");
        }
        
        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Filter by interface
        if ($request->filled('interface')) {
            $query->where('interface', $request->interface);
        }
        
        // Filter by assignment status
        if ($request->filled('assignment_status')) {
            if ($request->assignment_status === 'assigned') {
                $query->whereHas('asset.assignedUser');
            } elseif ($request->assignment_status === 'unassigned') {
                $query->whereDoesntHave('asset.assignedUser');
            }
        }
        
        // Filter by department
        if ($request->filled('department')) {
            $query->whereHas('asset.department', function($q) use ($request) {
                $q->where('name', $request->department);
            });
        }
        
        $peripherals = $query->paginate(10)->appends($request->query());
        
        // Get filter options
        $types = Peripheral::distinct()->pluck('type')->filter()->sort()->values();
        $interfaces = Peripheral::distinct()->pluck('interface')->filter()->sort()->values();
        $departments = \App\Models\Department::whereHas('assets.peripheral')->pluck('name')->sort()->values();
        
        return view('peripherals.index', compact('peripherals', 'types', 'interfaces', 'departments'));
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
            'interface' => 'required|in:USB,Bluetooth,Wireless,Wired,USB-C,Lightning',
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
            'interface' => 'required|in:USB,Bluetooth,Wireless,Wired,USB-C,Lightning',
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

    /**
     * Show the form for bulk creating peripherals.
     */
    public function bulkCreate()
    {
        $assets = Asset::whereDoesntHave('peripheral')
                      ->where('category_id', function($query) {
                          $query->select('id')
                                ->from('asset_categories')
                                ->where('name', 'Peripherals')
                                ->limit(1);
                      })
                      ->get();
        
        return view('peripherals.bulk-create', compact('assets'));
    }

    /**
     * Store bulk created peripherals.
     */
    public function bulkStore(Request $request)
    {
        // Get only the selected assets from the request
        $selectedAssets = $request->input('selected_assets', []);
        
        if (empty($selectedAssets)) {
            return redirect()->back()
                           ->withErrors(['selected_assets' => 'Please select at least one asset.'])
                           ->withInput();
        }

        // Validate only the selected peripherals
        $validationRules = [];
        foreach ($selectedAssets as $index => $assetId) {
            $validationRules["peripherals.{$index}.asset_id"] = 'required|exists:assets,id|unique:peripherals,asset_id';
            $validationRules["peripherals.{$index}.type"] = 'required|in:Mouse,Keyboard,Webcam,Headset,Speaker,Microphone,USB Hub,External Drive,Other';
            $validationRules["peripherals.{$index}.interface"] = 'required|in:USB,Bluetooth,Wireless,Wired,USB-C,Lightning';
        }

        $request->validate($validationRules);

        $created = 0;
        $errors = [];

        \DB::beginTransaction();
        try {
            foreach ($selectedAssets as $index => $assetId) {
                $peripheralData = $request->input("peripherals.{$index}");
                
                // Double-check that this asset doesn't already have a peripheral
                if (Peripheral::where('asset_id', $assetId)->exists()) {
                    $errors[] = "Asset ID {$assetId} already has a peripheral record.";
                    continue;
                }

                Peripheral::create($peripheralData);
                $created++;
            }

            \DB::commit();

            $message = "Successfully created {$created} peripherals.";
            if (!empty($errors)) {
                $message .= " " . count($errors) . " records were skipped due to errors.";
                return redirect()->route('peripherals.index')
                               ->with('warning', $message)
                               ->with('errors', $errors);
            }

            return redirect()->route('peripherals.index')
                           ->with('success', $message);

        } catch (\Exception $e) {
            \DB::rollback();
            
            return redirect()->back()
                           ->withErrors(['bulk_create' => 'Failed to create peripherals: ' . $e->getMessage()])
                           ->withInput();
        }
    }
}