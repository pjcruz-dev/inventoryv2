<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
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
        $query = Permission::with('roles');
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $permissions = $query->paginate(10)->withQueryString();
        
        return view('permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'description' => 'nullable|string|max:500'
        ]);
        
        Permission::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'guard_name' => 'web'
        ]);
        
        return redirect()->route('permissions.index')
                        ->with('success', 'Permission created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        $permission->load('roles');
        return view('permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        return view('permissions.edit', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions')->ignore($permission->id)],
            'description' => 'nullable|string|max:500'
        ]);
        
        $permission->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null
        ]);
        
        return redirect()->route('permissions.index')
                        ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        // Prevent deletion of system permissions
        $systemPermissions = [
            'view_assets', 'create_assets', 'edit_assets', 'delete_assets',
            'view_users', 'create_users', 'edit_users', 'delete_users',
            'view_reports', 'manage_transfers', 'manage_maintenance', 'manage_disposals', 'view_logs'
        ];
        
        if (in_array($permission->name, $systemPermissions)) {
            return redirect()->route('permissions.index')
                            ->with('error', 'Cannot delete system permissions.');
        }
        
        $permission->delete();
        
        return redirect()->route('permissions.index')
                        ->with('success', 'Permission deleted successfully.');
    }
}