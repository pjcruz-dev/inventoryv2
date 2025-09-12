<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RoleHierarchy
{
    /**
     * Role hierarchy definition
     * Higher index = higher privilege
     */
    private const ROLE_HIERARCHY = [
        'User' => 1,
        'IT Support' => 2,
        'Manager' => 3,
        'Admin' => 4,
        'Super Admin' => 5
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $requiredLevel = null): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        // If no specific level required, just check if user is authenticated
        if (!$requiredLevel) {
            return $next($request);
        }
        
        $userLevel = $this->getUserRoleLevel($user);
        $requiredRoleLevel = self::ROLE_HIERARCHY[$requiredLevel] ?? 0;
        
        if ($userLevel < $requiredRoleLevel) {
            Log::warning('Role hierarchy access denied', [
                'user_id' => $user->id,
                'user_roles' => $user->roles->pluck('name'),
                'required_level' => $requiredLevel,
                'user_level' => $userLevel,
                'ip_address' => $request->ip(),
                'route' => $request->route()->getName()
            ]);
            
            return response()->json([
                'error' => 'Insufficient privileges. Required role level: ' . $requiredLevel
            ], 403);
        }
        
        return $next($request);
    }
    
    /**
     * Get the highest role level for a user
     */
    private function getUserRoleLevel($user): int
    {
        $maxLevel = 0;
        
        foreach ($user->roles as $role) {
            $level = self::ROLE_HIERARCHY[$role->name] ?? 0;
            $maxLevel = max($maxLevel, $level);
        }
        
        return $maxLevel;
    }
    
    /**
     * Check if user can manage another user based on role hierarchy
     */
    public static function canManageUser($currentUser, $targetUser): bool
    {
        $currentUserLevel = (new self())->getUserRoleLevel($currentUser);
        $targetUserLevel = (new self())->getUserRoleLevel($targetUser);
        
        // Super Admin can manage anyone except other Super Admins (unless it's themselves)
        if ($currentUserLevel === 5) {
            return $targetUserLevel < 5 || $currentUser->id === $targetUser->id;
        }
        
        // Users can only manage users with lower role levels
        return $currentUserLevel > $targetUserLevel;
    }
    
    /**
     * Check if user can assign a specific role
     */
    public static function canAssignRole($currentUser, $roleName): bool
    {
        $currentUserLevel = (new self())->getUserRoleLevel($currentUser);
        $roleLevel = self::ROLE_HIERARCHY[$roleName] ?? 0;
        
        // Super Admin can assign any role except Super Admin to others
        if ($currentUserLevel === 5) {
            return $roleLevel < 5;
        }
        
        // Users can only assign roles lower than their own
        return $currentUserLevel > $roleLevel;
    }
    
    /**
     * Get roles that a user can assign
     */
    public static function getAssignableRoles($user): array
    {
        $userLevel = (new self())->getUserRoleLevel($user);
        $assignableRoles = [];
        
        foreach (self::ROLE_HIERARCHY as $roleName => $level) {
            if ($userLevel > $level || ($userLevel === 5 && $level < 5)) {
                $assignableRoles[] = $roleName;
            }
        }
        
        return $assignableRoles;
    }
}