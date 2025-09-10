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
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Computer::with('asset');
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('asset', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_tag', 'like', "%{$search}%");
            })->orWhere('processor', 'like', "%{$search}%")
              ->orWhere('operating_system', 'like', "%{$search}%")
              ->orWhere('computer_type', 'like', "%{$search}%");
        }
        
        if ($request->has('type') && $request->type) {
            $query->where('computer_type', $request->type);
        }
        
        $computers = $query->paginate(10);
        
        return view('computers.index', compact('computers'));
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
                                ->where('name', 'Computer')
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
                      ->orWhereDoesntHave('computer')
                      ->where('category_id', function($query) {
                          $query->select('id')
                                ->from('asset_categories')
                                ->where('name', 'Computer')
                                ->limit(1);
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
}
