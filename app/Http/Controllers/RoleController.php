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
        $allPermissions = Permission::all();
        $role->load('permissions');
        
        // Group permissions by module
        $permissionsByModule = $allPermissions->groupBy(function ($permission) {
            // Extract module name from permission name
            $parts = explode('_', $permission->name);
            if (count($parts) >= 2) {
                $module = $parts[1]; // Second part is usually the module
                
                // Map specific modules to user-friendly names
                $moduleMap = [
                    'assets' => 'Asset Management',
                    'asset' => 'Asset Management',
                    'assignment' => 'Asset Assignments',
                    'confirmations' => 'Asset Confirmations',
                    'categories' => 'Asset Categories',
                    'computers' => 'Computer Management',
                    'monitors' => 'Monitor Management',
                    'printers' => 'Printer Management',
                    'peripherals' => 'Peripheral Management',
                    'users' => 'User Management',
                    'departments' => 'Department Management',
                    'vendors' => 'Vendor Management',
                    'roles' => 'Role Management',
                    'permissions' => 'Permission Management',
                    'logs' => 'System Logs',
                    'timeline' => 'Asset Timeline',
                    'notifications' => 'Notifications',
                    'maintenance' => 'Maintenance',
                    'disposal' => 'Disposal',
                    'dashboard' => 'Dashboard',
                    'accountability' => 'Accountability Forms',
                    'import' => 'Import/Export',
                    'export' => 'Import/Export',
                    'template' => 'Import/Export',
                    'data' => 'Import/Export',
                    'serial' => 'Serial Management',
                    'field' => 'Field Management',
                    'error' => 'Error Management',
                    'admin' => 'System Administration',
                    'system' => 'System Administration',
                    'backup' => 'System Administration',
                    'audit' => 'System Administration',
                    'monitoring' => 'System Administration',
                    'settings' => 'System Administration',
                    'transfers' => 'Asset Transfers',
                    'reports' => 'Reports',
                    'manage' => 'General Management'
                ];
                
                return $moduleMap[$module] ?? ucfirst($module);
            }
            return 'General';
        })->sortKeys();
        
        return view('roles.edit', compact('role', 'allPermissions', 'permissionsByModule'));
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