<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:manage_roles');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Role::with('permissions');
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $roles = $query->paginate(10)->withQueryString();
        
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:500',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);
        
        $role = Role::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'guard_name' => 'web'
        ]);
        
        if (isset($validated['permissions'])) {
            // Convert permission IDs to permission names
            $permissionNames = Permission::whereIn('id', $validated['permissions'])->pluck('name')->toArray();
            $role->syncPermissions($permissionNames);
        }
        
        // Log the role creation
        Log::create([
            'category' => 'System',
            'user_id' => Auth::id(),
            'event_type' => 'create',
            'description' => "Created role '{$role->name}' with " . count($validated['permissions'] ?? []) . " permissions",
            'remarks' => "Role creation completed successfully",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
        
        return redirect()->route('roles.index')
                        ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $role->load('permissions');
        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $role->load('permissions');
        return view('roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'description' => 'nullable|string|max:500',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);
        
        $oldName = $role->name;
        $oldPermissions = $role->permissions->pluck('id')->toArray();
        
        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null
        ]);
        
        if (isset($validated['permissions'])) {
            // Convert permission IDs to permission names
            $permissionNames = Permission::whereIn('id', $validated['permissions'])->pluck('name')->toArray();
            $role->syncPermissions($permissionNames);
        } else {
            $role->syncPermissions([]);
        }
        
        $newPermissions = $validated['permissions'] ?? [];
        
        // Log the role update
        Log::create([
            'category' => 'System',
            'user_id' => Auth::id(),
            'event_type' => 'update',
            'description' => "Updated role from '{$oldName}' to '{$role->name}'. Permissions changed from " . count($oldPermissions) . " to " . count($newPermissions),
            'remarks' => "Role update completed successfully",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
        
        return redirect()->route('roles.index')
                        ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Prevent deletion of system roles
        if (in_array($role->name, ['Super Admin', 'Admin', 'Manager', 'User', 'IT Support'])) {
            return redirect()->route('roles.index')
                            ->with('error', 'Cannot delete system roles.');
        }
        
        $roleName = $role->name;
        $permissionCount = $role->permissions->count();
        
        $role->delete();
        
        // Log the role deletion
        Log::create([
            'category' => 'System',
            'user_id' => Auth::id(),
            'event_type' => 'delete',
            'description' => "Deleted role '{$roleName}' which had {$permissionCount} permissions",
            'remarks' => "Role deletion completed successfully",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
        
        return redirect()->route('roles.index')
                        ->with('success', 'Role deleted successfully.');
    }
}