<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Log;
use App\Models\AssetTimeline;
use App\Models\Asset;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Check if user has the required permission
        if (!$this->hasPermission($user, $permission)) {
            // Log unauthorized access attempt
            $this->logUnauthorizedAccess($user, $permission, $request);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access. You do not have permission to access this resource.'
                ], 403);
            }
            
            abort(403, 'Unauthorized access. You do not have permission to access this resource.');
        }
        
        return $next($request);
    }
    
    /**
     * Check if user has the specified permission
     */
    private function hasPermission($user, string $permission): bool
    {
        // Cache key for user permissions
        $cacheKey = "user_permissions_{$user->id}";
        
        // Check if user has Super Admin role (bypass all permission checks)
        if ($user->hasRole('Super Admin')) {
            return true;
        }
        
        // Check if user has Admin role (bypass most permission checks except system-level)
        if ($user->hasRole('Admin') && !in_array($permission, ['system_admin', 'manage_system_settings'])) {
            return true;
        }
        
        // Get cached permissions or fetch from database
        $userPermissions = Cache::remember($cacheKey, 3600, function () use ($user) {
            if (method_exists($user, 'getAllPermissions')) {
                return $user->getAllPermissions()->pluck('name')->toArray();
            }
            return [];
        });
        
        // Check if user has the specific permission
        if (in_array($permission, $userPermissions)) {
            return true;
        }
        
        // Check if user has the specific permission using Spatie method
        if (method_exists($user, 'hasPermissionTo')) {
            try {
                return $user->hasPermissionTo($permission);
            } catch (\Exception $e) {
                // Permission doesn't exist, continue to other checks
            }
        }
        
        // Dynamic permission checking for role management
        if ($this->checkDynamicPermissions($user, $permission)) {
            return true;
        }
        
        // Default deny if no permission system is available
        return false;
    }
    
    /**
     * Check dynamic permissions for role management
     */
    private function checkDynamicPermissions($user, string $permission): bool
    {
        // Role management permissions
        $roleManagementPermissions = [
            'manage_user_roles',
            'assign_roles',
            'remove_roles',
            'bulk_assign_roles',
            'view_user_roles'
        ];
        
        if (in_array($permission, $roleManagementPermissions)) {
            // Check if user has any role management permission
            return $user->hasAnyPermission([
                'manage_users',
                'edit_users',
                'manage_roles',
                'admin_access'
            ]);
        }
        
        // User management permissions
        if (str_starts_with($permission, 'user_')) {
            return $user->hasAnyPermission(['manage_users', 'edit_users', 'admin_access']);
        }
        
        // Role permissions
        if (str_starts_with($permission, 'role_')) {
            return $user->hasAnyPermission(['manage_roles', 'admin_access']);
        }
        
        // Permission permissions
        if (str_starts_with($permission, 'permission_')) {
            return $user->hasAnyPermission(['manage_permissions', 'admin_access']);
        }
        
        return false;
    }
    
    /**
     * Log unauthorized access attempts
     */
    private function logUnauthorizedAccess($user, string $permission, Request $request): void
    {
        try {
            // Log to activity logs
            Log::create([
                'category' => 'Security',
                'user_id' => $user->id,
                'event_type' => 'unauthorized_access_attempt',
                'description' => "User attempted to access resource requiring '{$permission}' permission",
                'remarks' => "Unauthorized access attempt blocked",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            // Log to asset timeline for maintenance and disposal operations
            $this->logToAssetTimeline($user, $permission, $request);
            
        } catch (\Exception $e) {
            // Silently fail if logging fails
        }
    }
    
    /**
     * Log unauthorized access attempts to asset timeline for maintenance and disposal
     */
    private function logToAssetTimeline($user, string $permission, Request $request): void
    {
        try {
            // Check if this is a maintenance or disposal related permission
            $maintenancePermissions = ['view_maintenance', 'create_maintenance', 'edit_maintenance', 'delete_maintenance'];
            $disposalPermissions = ['view_disposal', 'create_disposal', 'edit_disposal', 'delete_disposal'];
            
            if (in_array($permission, $maintenancePermissions) || in_array($permission, $disposalPermissions)) {
                $assetId = null;
                $action = '';
                $notes = '';
                
                // Try to extract asset ID from the request
                if ($request->route('maintenance')) {
                    $maintenance = $request->route('maintenance');
                    $assetId = $maintenance->asset_id ?? null;
                    $action = 'unauthorized_maintenance_access';
                    $notes = "Unauthorized attempt to access maintenance record (Permission: {$permission})";
                } elseif ($request->route('disposal')) {
                    $disposal = $request->route('disposal');
                    $assetId = $disposal->asset_id ?? null;
                    $action = 'unauthorized_disposal_access';
                    $notes = "Unauthorized attempt to access disposal record (Permission: {$permission})";
                } elseif ($request->has('asset_id')) {
                    $assetId = $request->input('asset_id');
                    $action = in_array($permission, $maintenancePermissions) ? 'unauthorized_maintenance_access' : 'unauthorized_disposal_access';
                    $notes = "Unauthorized attempt to access " . (in_array($permission, $maintenancePermissions) ? 'maintenance' : 'disposal') . " operation (Permission: {$permission})";
                } else {
                    // General unauthorized access for maintenance/disposal modules
                    $action = in_array($permission, $maintenancePermissions) ? 'unauthorized_maintenance_access' : 'unauthorized_disposal_access';
                    $notes = "Unauthorized attempt to access " . (in_array($permission, $maintenancePermissions) ? 'maintenance' : 'disposal') . " module (Permission: {$permission})";
                }
                
                // If we have a specific asset, log to its timeline
                if ($assetId && Asset::find($assetId)) {
                    AssetTimeline::create([
                        'asset_id' => $assetId,
                        'action' => $action,
                        'from_user_id' => null,
                        'to_user_id' => null,
                        'from_department_id' => null,
                        'to_department_id' => null,
                        'notes' => $notes,
                        'old_values' => null,
                        'new_values' => json_encode([
                            'blocked_permission' => $permission,
                            'ip_address' => $request->ip(),
                            'user_agent' => $request->userAgent()
                        ]),
                        'performed_by' => $user->id,
                        'performed_at' => now()
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Silently fail if asset timeline logging fails
        }
    }
    
    /**
     * Clear user permissions cache
     */
    public static function clearUserPermissionsCache($userId): void
    {
        Cache::forget("user_permissions_{$userId}");
    }
}