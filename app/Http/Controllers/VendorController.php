<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VendorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_vendors')->only(['index', 'show']);
        $this->middleware('permission:create_vendors')->only(['create', 'store']);
        $this->middleware('permission:edit_vendors')->only(['edit', 'update']);
        $this->middleware('permission:delete_vendors')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Vendor::with(['assets']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }
        
        $vendors = $query->paginate(10)->appends(request()->query());
        
        return view('vendors.index', compact('vendors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vendors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:vendors',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:vendors',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);
        
        Vendor::create($validated);
        
        return redirect()->route('vendors.index')
                        ->with('success', 'Vendor created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vendor $vendor)
    {
        $vendor->load(['assets.category', 'assets.assignedUser']);
        
        return view('vendors.show', compact('vendor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vendor $vendor)
    {
        return view('vendors.edit', compact('vendor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('vendors')->ignore($vendor->id)],
            'contact_person' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('vendors')->ignore($vendor->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);
        
        $vendor->update($validated);
        
        return redirect()->route('vendors.show', $vendor)
                        ->with('success', 'Vendor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vendor $vendor)
    {
        // Check if vendor has assets
        if ($vendor->assets()->count() > 0) {
            return redirect()->route('vendors.show', $vendor)
                            ->with('error', 'Cannot delete vendor with assigned assets. Please reassign assets first.');
        }
        
        $vendor->delete();
        
        return redirect()->route('vendors.index')
                        ->with('success', 'Vendor deleted successfully.');
    }
}
