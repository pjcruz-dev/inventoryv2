<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class RedirectionService
{
    /**
     * Determine the appropriate redirect URL based on user roles and permissions
     *
     * @param User $user
     * @return string
     */
    public function getRedirectUrl(User $user): string
    {
        // Log the redirection attempt for security auditing
        Log::info('User redirection initiated', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role->name ?? 'No Role',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        // Check for system admin - highest priority
        if ($user->can('system_admin')) {
            Log::info('System admin redirected to dashboard', ['user_id' => $user->id]);
            return route('dashboard');
        }

        // Check for role-based redirections
        $roleName = $user->role->name ?? null;
        
        switch ($roleName) {
            case 'IT Administrator':
                return $this->getITAdminRedirect($user);
            
            case 'IT Staff':
                return $this->getITStaffRedirect($user);
            
            case 'Department Manager':
                return $this->getDepartmentManagerRedirect($user);
            
            case 'Employee':
                return $this->getEmployeeRedirect($user);
            
            case 'User':
                return $this->getUserRedirect($user);
            
            default:
                return $this->getDefaultRedirect($user);
        }
    }

    /**
     * Get redirect URL for IT Administrator
     *
     * @param User $user
     * @return string
     */
    private function getITAdminRedirect(User $user): string
    {
        // IT Admins get full dashboard access
        if ($user->can('view_assets') && $user->can('view_users')) {
            Log::info('IT Administrator redirected to dashboard', ['user_id' => $user->id]);
            return route('dashboard');
        }
        
        return $this->getRestrictedRedirect($user);
    }

    /**
     * Get redirect URL for IT Staff
     *
     * @param User $user
     * @return string
     */
    private function getITStaffRedirect(User $user): string
    {
        // IT Staff typically manage assets
        if ($user->can('view_assets')) {
            Log::info('IT Staff redirected to assets', ['user_id' => $user->id]);
            return route('assets.index');
        }
        
        if ($user->can('view_logs')) {
            Log::info('IT Staff redirected to logs', ['user_id' => $user->id]);
            return route('logs.index');
        }
        
        return $this->getRestrictedRedirect($user);
    }

    /**
     * Get redirect URL for Department Manager
     *
     * @param User $user
     * @return string
     */
    private function getDepartmentManagerRedirect(User $user): string
    {
        // Department Managers typically manage users and view assets
        if ($user->can('view_users')) {
            Log::info('Department Manager redirected to users', ['user_id' => $user->id]);
            return route('users.index');
        }
        
        if ($user->can('view_assets')) {
            Log::info('Department Manager redirected to assets', ['user_id' => $user->id]);
            return route('assets.index');
        }
        
        return $this->getRestrictedRedirect($user);
    }

    /**
     * Get redirect URL for Employee
     *
     * @param User $user
     * @return string
     */
    private function getEmployeeRedirect(User $user): string
    {
        // Employees typically have limited access
        if ($user->can('view_assets')) {
            Log::info('Employee redirected to assets view', ['user_id' => $user->id]);
            return route('assets.index');
        }
        
        // If no specific permissions, redirect to a basic dashboard or profile
        Log::info('Employee redirected to basic dashboard', ['user_id' => $user->id]);
        return route('dashboard');
    }

    /**
     * Get redirect URL for User role
     *
     * @param User $user
     * @return string
     */
    private function getUserRedirect(User $user): string
    {
        // User role can only view assets - redirect directly to assets page
        Log::info('User role redirected to assets view (limited access)', ['user_id' => $user->id]);
        return route('assets.index');
    }

    /**
     * Get default redirect URL when role is not recognized
     *
     * @param User $user
     * @return string
     */
    private function getDefaultRedirect(User $user): string
    {
        // Check for any basic permissions
        if ($user->can('view_assets')) {
            Log::info('User with unknown role redirected to assets', ['user_id' => $user->id]);
            return route('assets.index');
        }
        
        if ($user->can('view_users')) {
            Log::info('User with unknown role redirected to users', ['user_id' => $user->id]);
            return route('users.index');
        }
        
        // Last resort - basic dashboard
        Log::warning('User with no recognized permissions redirected to dashboard', [
            'user_id' => $user->id,
            'role' => $user->role->name ?? 'No Role'
        ]);
        
        return route('dashboard');
    }

    /**
     * Get restricted redirect URL for users with insufficient permissions
     *
     * @param User $user
     * @return string
     */
    private function getRestrictedRedirect(User $user): string
    {
        Log::warning('User with insufficient permissions redirected to restricted access', [
            'user_id' => $user->id,
            'role' => $user->role->name ?? 'No Role'
        ]);
        
        // Redirect to a restricted access page or basic dashboard
        return route('dashboard');
    }

    /**
     * Check if user has access to a specific route
     *
     * @param User $user
     * @param string $routeName
     * @return bool
     */
    public function canAccessRoute(User $user, string $routeName): bool
    {
        $routePermissions = [
            'assets.index' => 'view_assets',
            'assets.create' => 'create_assets',
            'assets.edit' => 'edit_assets',
            'users.index' => 'view_users',
            'users.create' => 'create_users',
            'users.edit' => 'edit_users',
            'logs.index' => 'view_logs',
            'roles.index' => 'view_roles',
            'permissions.index' => 'view_permissions',
            'departments.index' => 'view_users',
            'vendors.index' => 'view_assets',
        ];

        $requiredPermission = $routePermissions[$routeName] ?? null;
        
        if (!$requiredPermission) {
            // If no specific permission required, allow access
            return true;
        }
        
        $hasAccess = $user->can($requiredPermission);
        
        Log::info('Route access check', [
            'user_id' => $user->id,
            'route' => $routeName,
            'required_permission' => $requiredPermission,
            'access_granted' => $hasAccess
        ]);
        
        return $hasAccess;
    }
}