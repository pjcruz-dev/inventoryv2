<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:manage_permissions');
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
        $roles = \App\Models\Role::orderBy('name')->get();
        return view('permissions.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'description' => 'nullable|string|max:500',
            'guard_name' => 'required|string|in:web,api'
        ]);
        
        $permission = Permission::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'guard_name' => $validated['guard_name']
        ]);
        
        // Log the permission creation
        Log::create([
            'category' => 'System',
            'user_id' => Auth::id(),
            'event_type' => 'create',
            'description' => "Created permission '{$permission->name}' with guard '{$permission->guard_name}'",
            'remarks' => "Permission creation completed successfully",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
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
        $roles = \App\Models\Role::orderBy('name')->get();
        return view('permissions.edit', compact('permission', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions')->ignore($permission->id)],
            'description' => 'nullable|string|max:500',
            'guard_name' => 'required|string|in:web,api'
        ]);
        
        $oldName = $permission->name;
        $oldGuard = $permission->guard_name;
        
        $permission->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'guard_name' => $validated['guard_name']
        ]);
        
        // Log the permission update
        Log::create([
            'category' => 'System',
            'user_id' => Auth::id(),
            'event_type' => 'update',
            'description' => "Updated permission from '{$oldName}' to '{$permission->name}', guard changed from '{$oldGuard}' to '{$permission->guard_name}'",
            'remarks' => "Permission update completed successfully",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
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
        
        $permissionName = $permission->name;
        $guardName = $permission->guard_name;
        $roleCount = $permission->roles->count();
        
        $permission->delete();
        
        // Log the permission deletion
        Log::create([
            'category' => 'System',
            'user_id' => Auth::id(),
            'event_type' => 'delete',
            'description' => "Deleted permission '{$permissionName}' with guard '{$guardName}' that was assigned to {$roleCount} roles",
            'remarks' => "Permission deletion completed successfully",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
        
        return redirect()->route('permissions.index')
                        ->with('success', 'Permission deleted successfully.');
    }
}