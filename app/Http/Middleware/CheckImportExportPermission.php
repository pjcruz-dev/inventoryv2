<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CheckImportExportPermission
{
    /**
     * Permission mappings for import/export operations
     */
    private const PERMISSIONS = [
        // Basic access permissions
        'import_export_access' => 'Access import/export interface',
        'template_download' => 'Download import templates',
        'template_preview' => 'Preview template structure',
        'data_export' => 'Export data from system',
        'data_import' => 'Import data into system',
        
        // Advanced permissions
        'bulk_export' => 'Perform bulk export operations',
        'import_validation' => 'Validate import data',
        'import_status' => 'Check import operation status',
        'import_history' => 'View import history',
        'import_details' => 'View detailed import information',
        'error_reports' => 'Access error reports',
        'error_download' => 'Download error reports',
        
        // API permissions
        'serial_generation' => 'Generate serial number suggestions',
        'serial_validation' => 'Validate serial numbers',
        'serial_stats' => 'View serial number statistics',
        'field_validation' => 'Validate individual fields',
        'data_lookup' => 'Lookup reference data',
        'field_mapping' => 'Access field mapping information',
        'import_progress' => 'Monitor import progress',
        
        // Administrative permissions
        'admin_access' => 'Administrative access to import/export',
        'audit_access' => 'Access audit trail information',
        'system_config' => 'Modify system configuration',
        'bulk_operations' => 'Perform bulk administrative operations',
        'performance_monitoring' => 'Monitor system performance',
        
        // Module-specific permissions
        'assets_import' => 'Import assets data',
        'assets_export' => 'Export assets data',
        'users_import' => 'Import users data',
        'users_export' => 'Export users data',
        'computers_import' => 'Import computers data',
        'computers_export' => 'Export computers data',
        'departments_import' => 'Import departments data',
        'departments_export' => 'Export departments data',
        'vendors_import' => 'Import vendors data',
        'vendors_export' => 'Export vendors data',
    ];
    
    /**
     * Role-based permission matrix
     */
    private const ROLE_PERMISSIONS = [
        'super_admin' => '*', // All permissions
        'admin' => [
            'import_export_access', 'template_download', 'template_preview',
            'data_export', 'data_import', 'bulk_export', 'import_validation',
            'import_status', 'import_history', 'import_details', 'error_reports',
            'error_download', 'serial_generation', 'serial_validation',
            'serial_stats', 'field_validation', 'data_lookup', 'field_mapping',
            'import_progress', 'audit_access', 'assets_import', 'assets_export',
            'users_import', 'users_export', 'computers_import', 'computers_export',
            'departments_import', 'departments_export', 'vendors_import', 'vendors_export'
        ],
        'manager' => [
            'import_export_access', 'template_download', 'template_preview',
            'data_export', 'data_import', 'import_validation', 'import_status',
            'import_history', 'error_reports', 'serial_generation', 'serial_validation',
            'field_validation', 'data_lookup', 'field_mapping', 'import_progress',
            'assets_import', 'assets_export', 'users_export', 'computers_import',
            'computers_export', 'departments_export', 'vendors_export'
        ],
        'user' => [
            'import_export_access', 'template_download', 'template_preview',
            'data_export', 'import_validation', 'field_validation', 'data_lookup',
            'assets_export', 'computers_export'
        ],
        'viewer' => [
            'import_export_access', 'template_download', 'template_preview',
            'data_export', 'data_lookup', 'assets_export'
        ]
    ];
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return $this->unauthorizedResponse('Authentication required.');
        }
        
        $user = Auth::user();
        
        // Check if user account is active
        if (!$this->isUserActive($user)) {
            Log::warning('Inactive user attempted import/export access', [
                'user_id' => $user->id,
                'permission' => $permission,
                'ip' => $request->ip()
            ]);
            
            return $this->unauthorizedResponse('Account is inactive.');
        }
        
        // Check permission with caching
        $hasPermission = $this->checkPermissionCached($user, $permission);
        
        if (!$hasPermission) {
            Log::warning('Permission denied for import/export operation', [
                'user_id' => $user->id,
                'user_role' => $user->role ?? 'unknown',
                'permission' => $permission,
                'ip' => $request->ip(),
                'route' => $request->route()->getName()
            ]);
            
            return $this->unauthorizedResponse(
                'Insufficient permissions for this operation.',
                $permission
            );
        }
        
        // Check module-specific permissions if applicable
        $module = $request->route('module');
        if ($module && !$this->checkModulePermission($user, $module, $permission)) {
            Log::warning('Module permission denied', [
                'user_id' => $user->id,
                'module' => $module,
                'permission' => $permission,
                'ip' => $request->ip()
            ]);
            
            return $this->unauthorizedResponse(
                "Insufficient permissions for {$module} module."
            );
        }
        
        // Check rate limiting for sensitive operations
        if ($this->isSensitiveOperation($permission)) {
            $rateLimitResult = $this->checkRateLimit($user, $permission);
            if (!$rateLimitResult['allowed']) {
                Log::warning('Rate limit exceeded for sensitive operation', [
                    'user_id' => $user->id,
                    'permission' => $permission,
                    'attempts' => $rateLimitResult['attempts'],
                    'ip' => $request->ip()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Rate limit exceeded. Please try again later.',
                    'error_code' => 'RATE_LIMIT_EXCEEDED',
                    'retry_after' => $rateLimitResult['retry_after']
                ], 429);
            }
        }
        
        // Log successful permission check
        Log::info('Import/export permission granted', [
            'user_id' => $user->id,
            'permission' => $permission,
            'module' => $module,
            'ip' => $request->ip()
        ]);
        
        // Add permission context to request
        $request->merge([
            'user_permissions' => $this->getUserPermissions($user),
            'granted_permission' => $permission
        ]);
        
        return $next($request);
    }
    
    /**
     * Check if user is active
     */
    private function isUserActive($user): bool
    {
        // Check if user has active status
        if (isset($user->status) && $user->status !== 'active') {
            return false;
        }
        
        // Check if user account is not expired
        if (isset($user->expires_at) && $user->expires_at < now()) {
            return false;
        }
        
        // Check if user is not suspended
        if (isset($user->suspended_at) && $user->suspended_at !== null) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Check permission with caching
     */
    private function checkPermissionCached($user, string $permission): bool
    {
        $cacheKey = "user_permissions:{$user->id}:{$permission}";
        
        return Cache::remember($cacheKey, 300, function () use ($user, $permission) {
            return $this->checkPermission($user, $permission);
        });
    }
    
    /**
     * Check if user has specific permission
     */
    private function checkPermission($user, string $permission): bool
    {
        // Super admin has all permissions
        if ($this->isSuperAdmin($user)) {
            return true;
        }
        
        // Get user role
        $userRole = $user->role ?? 'user';
        
        // Check role-based permissions
        if (!isset(self::ROLE_PERMISSIONS[$userRole])) {
            return false;
        }
        
        $rolePermissions = self::ROLE_PERMISSIONS[$userRole];
        
        // Check if role has all permissions
        if ($rolePermissions === '*') {
            return true;
        }
        
        // Check if permission is in role's permission list
        if (in_array($permission, $rolePermissions)) {
            return true;
        }
        
        // Check custom user permissions if they exist
        if (method_exists($user, 'hasPermission')) {
            return $user->hasPermission($permission);
        }
        
        return false;
    }
    
    /**
     * Check module-specific permissions
     */
    private function checkModulePermission($user, string $module, string $permission): bool
    {
        // Extract operation type from permission
        $isImport = strpos($permission, 'import') !== false;
        $isExport = strpos($permission, 'export') !== false;
        
        if ($isImport) {
            $modulePermission = "{$module}_import";
        } elseif ($isExport) {
            $modulePermission = "{$module}_export";
        } else {
            // For other operations, check general access
            return true;
        }
        
        return $this->checkPermissionCached($user, $modulePermission);
    }
    
    /**
     * Check if user is super admin
     */
    private function isSuperAdmin($user): bool
    {
        return $user->role === 'super_admin' || 
               (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin());
    }
    
    /**
     * Check if operation is sensitive and requires rate limiting
     */
    private function isSensitiveOperation(string $permission): bool
    {
        $sensitiveOperations = [
            'data_import',
            'bulk_export',
            'bulk_operations',
            'system_config',
            'admin_access'
        ];
        
        return in_array($permission, $sensitiveOperations);
    }
    
    /**
     * Check rate limit for sensitive operations
     */
    private function checkRateLimit($user, string $permission): array
    {
        $cacheKey = "rate_limit:{$user->id}:{$permission}";
        $maxAttempts = $this->getMaxAttempts($permission);
        $decayMinutes = $this->getDecayMinutes($permission);
        
        $attempts = Cache::get($cacheKey, 0);
        
        if ($attempts >= $maxAttempts) {
            $retryAfter = Cache::get($cacheKey . ':retry_after', $decayMinutes * 60);
            return [
                'allowed' => false,
                'attempts' => $attempts,
                'retry_after' => $retryAfter
            ];
        }
        
        // Increment attempts
        Cache::put($cacheKey, $attempts + 1, $decayMinutes * 60);
        if ($attempts === 0) {
            Cache::put($cacheKey . ':retry_after', $decayMinutes * 60, $decayMinutes * 60);
        }
        
        return [
            'allowed' => true,
            'attempts' => $attempts + 1
        ];
    }
    
    /**
     * Get max attempts for permission
     */
    private function getMaxAttempts(string $permission): int
    {
        $limits = [
            'data_import' => 10,
            'bulk_export' => 5,
            'bulk_operations' => 3,
            'system_config' => 5,
            'admin_access' => 20
        ];
        
        return $limits[$permission] ?? 15;
    }
    
    /**
     * Get decay minutes for permission
     */
    private function getDecayMinutes(string $permission): int
    {
        $decayTimes = [
            'data_import' => 15,
            'bulk_export' => 30,
            'bulk_operations' => 60,
            'system_config' => 30,
            'admin_access' => 10
        ];
        
        return $decayTimes[$permission] ?? 20;
    }
    
    /**
     * Get all permissions for user
     */
    private function getUserPermissions($user): array
    {
        if ($this->isSuperAdmin($user)) {
            return array_keys(self::PERMISSIONS);
        }
        
        $userRole = $user->role ?? 'user';
        $rolePermissions = self::ROLE_PERMISSIONS[$userRole] ?? [];
        
        if ($rolePermissions === '*') {
            return array_keys(self::PERMISSIONS);
        }
        
        return $rolePermissions;
    }
    
    /**
     * Return unauthorized response
     */
    private function unauthorizedResponse(string $message, string $permission = null)
    {
        $response = [
            'success' => false,
            'message' => $message,
            'error_code' => 'PERMISSION_DENIED'
        ];
        
        if ($permission) {
            $response['required_permission'] = $permission;
            $response['permission_description'] = self::PERMISSIONS[$permission] ?? 'Unknown permission';
        }
        
        return response()->json($response, 403);
    }
}