<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\CheckPermission;
use App\Http\Requests\AssignRoleRequest;
use App\Http\Requests\BulkAssignRoleRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use App\Http\Middleware\RoleHierarchy;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_users')->only(['index', 'show']);
        $this->middleware('permission:create_users')->only(['create', 'store']);
        $this->middleware('permission:edit_users')->only(['edit', 'update', 'assignRole', 'bulkAssignRole']);
        $this->middleware('permission:delete_users')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['department', 'role']);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('entity')) {
            $query->where('entity', $request->entity);
        }
        
        $users = $query->orderBy('first_name')->paginate(15)->appends(request()->query());
        $departments = Department::orderBy('name')->get();
        $entities = User::distinct()->pluck('entity')->filter()->sort()->values();
        
        return view('users.index', compact('users', 'departments', 'entities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::orderBy('name')->get();
        $roles = \App\Models\Role::orderBy('name')->get();
        return view('users.create', compact('departments', 'roles'));
    }

    /**
     * Show the role management interface
     */
    public function roleManagement(Request $request)
    {
        $currentUser = Auth::user();
        $query = User::with(['roles', 'department']);
        
        // Filter users based on role hierarchy - users can only see users they can manage
        if (!$currentUser->hasRole('Super Admin')) {
            $manageableUserIds = User::all()->filter(function($user) use ($currentUser) {
                return RoleHierarchy::canManageUser($currentUser, $user);
            })->pluck('id');
            
            $query->whereIn('id', $manageableUserIds);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('roles.id', $request->role);
            });
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        $users = $query->orderBy('first_name')->paginate(15);
        
        // Get only roles that current user can assign
        $assignableRoles = RoleHierarchy::getAssignableRoles($currentUser);
        $roles = Role::whereIn('name', $assignableRoles)->get();
        $allRoles = Role::all(); // For filtering purposes
        $departments = Department::all();

        return view('users.role-management', compact('users', 'roles', 'allRoles', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'employee_id' => 'nullable|string|max:255|unique:users,employee_id',
            'department_id' => 'nullable|exists:departments,id',
            'job_title' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'status' => 'required|integer|in:0,1,2',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id',
        ]);
        
        $validated['password'] = Hash::make($validated['password']);
        
        // Remove role_id from validated data as it will be handled separately
        $roleId = $validated['role_id'] ?? null;
        unset($validated['role_id']);
        
        $user = User::create($validated);
        
        // Assign role if provided
        if ($roleId) {
            $role = \App\Models\Role::find($roleId);
            if ($role) {
                $user->assignRole($role->name);
            }
        }
        
        return redirect()->route('users.index')
                        ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('department', 'assignedAssets.category');
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $departments = Department::orderBy('name')->get();
        $roles = \App\Models\Role::orderBy('name')->get();
        return view('users.edit', compact('user', 'departments', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'employee_id' => ['nullable', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'department_id' => 'nullable|exists:departments,id',
            'job_title' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'status' => 'required|integer|in:0,1,2',
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id',
        ]);
        
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        
        // Handle role assignment
        if (isset($validated['role_id'])) {
            $role = \App\Models\Role::find($validated['role_id']);
            if ($role) {
                $user->syncRoles([$role->name]);
            }
            unset($validated['role_id']); // Remove from validated data as it's handled separately
        } else {
            $user->syncRoles([]); // Remove all roles if none selected
        }
        
        $user->update($validated);
        
        return redirect()->route('users.index')
                        ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Check if user has assigned assets
        if ($user->assignedAssets()->count() > 0) {
            return redirect()->route('users.index')
                            ->with('error', 'Cannot delete user with assigned assets. Please reassign assets first.');
        }
        
        $user->delete();
        
        return redirect()->route('users.index')
                        ->with('success', 'User deleted successfully.');
    }



    /**
     * Assign role to a user
     */
    public function assignRole(AssignRoleRequest $request, $userId)
    {
        // Rate limiting
        $key = 'assign-role:' . Auth::id();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many role assignment attempts. Please try again later.'
            ], 429);
        }
        
        RateLimiter::hit($key, 60); // 10 attempts per minute
        
        try {
            DB::beginTransaction();
            
            $user = User::findOrFail($userId);
             $role = Role::find($request->role_id);
             
             // Additional security checks using role hierarchy
             if ($user->id === Auth::id() && !Auth::user()->hasRole('Super Admin')) {
                 DB::rollBack();
                 return response()->json([
                     'success' => false,
                     'message' => 'You cannot modify your own role.'
                 ], 403);
             }
             
             // Check if current user can manage target user
             if (!RoleHierarchy::canManageUser(Auth::user(), $user)) {
                 DB::rollBack();
                 return response()->json([
                     'success' => false,
                     'message' => 'You do not have permission to manage this user.'
                 ], 403);
             }
             
             // Check if current user can assign this role
             if (!RoleHierarchy::canAssignRole(Auth::user(), $role->name)) {
                 DB::rollBack();
                 return response()->json([
                     'success' => false,
                     'message' => 'You do not have permission to assign this role.'
                 ], 403);
             }
            
            // Check if user already has this role
            if ($user->hasRole($role->name)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "User already has the '{$role->name}' role."
                ]);
            }
            
            $oldRoles = $user->roles->pluck('name')->toArray();
            
            $user->syncRoles([$role->name]);
            
            // Clear user permissions cache
            CheckPermission::clearUserPermissionsCache($user->id);
            
            // Log the role assignment
            Log::create([
                'category' => 'System',
                'user_id' => Auth::id(),
                'event_type' => 'assign_role',
                'description' => "Assigned role '{$role->name}' to user '{$user->name}'. Previous roles: " . implode(', ', $oldRoles),
                'remarks' => "Role assignment completed successfully",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Role '{$role->name}' assigned successfully to {$user->name}"
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Role assignment failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'role_id' => $request->role_id,
                'assigned_by' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign role. Please try again.'
            ], 500);
        }
    }

    /**
     * Remove role from a user
     */
    public function removeRole(Request $request, $userId)
    {
        // Rate limiting
        $key = 'remove-role:' . Auth::id();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many role removal attempts. Please try again later.'
            ], 429);
        }
        
        RateLimiter::hit($key, 60); // 10 attempts per minute
        
        // Validate request
        $request->validate([
            'role_id' => 'required|integer|exists:roles,id'
        ]);
        
        try {
            DB::beginTransaction();
            
            $user = User::findOrFail($userId);
             $role = Role::findOrFail($request->role_id);
             
             // Security checks
             if ($user->id === Auth::id()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot remove roles from yourself.'
                ], 403);
            }
            
            // Prevent removing Super Admin role unless done by another Super Admin
            if ($role->name === 'Super Admin' && !Auth::user()->hasRole('Super Admin')) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to remove Super Admin role.'
                ], 403);
            }
            
            // Check if user has this role
            if (!$user->hasRole($role->name)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "User does not have the '{$role->name}' role."
                ]);
            }
            
            $oldRoles = $user->roles->pluck('name')->toArray();
            
            $user->removeRole($role);
            
            // Clear user permissions cache
            CheckPermission::clearUserPermissionsCache($user->id);
            
            // Log the role removal
            Log::create([
                'category' => 'System',
                'user_id' => Auth::id(),
                'event_type' => 'remove_role',
                'description' => "Removed role '{$role->name}' from user '{$user->name}'. Previous roles: " . implode(', ', $oldRoles),
                'remarks' => "Role removal completed successfully",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Role '{$role->name}' removed from {$user->name} successfully."
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Role removal failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'role_id' => $request->role_id,
                'removed_by' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove role. Please try again.'
            ], 500);
        }
    }

    /**
     * Bulk assign roles to multiple users
     */
    public function bulkAssignRoles(BulkAssignRoleRequest $request)
    {
        // Rate limiting
        $key = 'bulk-assign-role:' . Auth::id();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many bulk assignment attempts. Please try again later.'
            ], 429);
        }
        
        RateLimiter::hit($key, 60); // 5 attempts per minute
        
        try {
            DB::beginTransaction();
            
            $role = Role::find($request->role_id);
            $users = User::whereIn('id', $request->user_ids)->get();
            
            // Security check - prevent self-modification
            if ($users->contains('id', Auth::id()) && !Auth::user()->hasRole('Super Admin')) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot modify your own role in bulk operations.'
                ], 403);
            }
            
            $assignedCount = 0;
            $skippedCount = 0;
            
            foreach ($users as $user) {
                // Skip if user already has this role
                if ($user->hasRole($role->name)) {
                    $skippedCount++;
                    continue;
                }
                
                $oldRoles = $user->roles->pluck('name')->toArray();
                $user->syncRoles([$role->name]);
                
                // Clear user permissions cache
                CheckPermission::clearUserPermissionsCache($user->id);
                
                // Log each assignment
                Log::create([
                    'category' => 'System',
                    'user_id' => Auth::id(),
                    'event_type' => 'bulk_assign_role',
                    'description' => "Bulk assigned role '{$role->name}' to user '{$user->name}'. Previous roles: " . implode(', ', $oldRoles),
                    'remarks' => "Bulk role assignment completed successfully",
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]);
                
                $assignedCount++;
            }
            
            DB::commit();
            
            $message = "Role '{$role->name}' assigned to {$assignedCount} users successfully";
            if ($skippedCount > 0) {
                $message .= ". {$skippedCount} users already had this role.";
            }
            
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Bulk role assignment failed', [
                'error' => $e->getMessage(),
                'user_ids' => $request->user_ids,
                'role_id' => $request->role_id,
                'assigned_by' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign roles. Please try again.'
            ], 500);
        }
    }

    /**
     * Get user's current roles (AJAX)
     */
    public function getUserRoles(User $user)
    {
        return response()->json([
            'roles' => $user->roles->map(function($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'description' => $role->description
                ];
            })
        ]);
    }
}
