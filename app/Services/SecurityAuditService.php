<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\AuditLog;

class SecurityAuditService
{
    /**
     * Log successful login attempt.
     *
     * @param mixed $user
     * @param Request $request
     * @param string $redirectUrl
     * @return void
     */
    public function logSuccessfulLogin($user, Request $request, string $redirectUrl = null): void
    {
        $logData = [
            'event_type' => 'LOGIN_SUCCESS',
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role->name ?? 'No Role',
            'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            'redirect_url' => $redirectUrl,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => $request->hasSession() ? $request->session()->getId() : null,
            'timestamp' => now(),
            'login_method' => 'web'
        ];

        // Log to Laravel log
        Log::info('Successful login', $logData);

        // Also save to audit_logs table
        try {
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'login_success',
                'model_type' => 'User',
                'model_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'details' => json_encode([
                    'email' => $user->email,
                    'role' => $user->role->name ?? 'No Role',
                    'redirect_url' => $redirectUrl,
                    'login_method' => 'web'
                ]),
                'timestamp' => now(),
                'created_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save audit log: ' . $e->getMessage());
        }
    }

    /**
     * Log failed login attempt.
     *
     * @param string $email
     * @param Request $request
     * @param string $reason
     * @return void
     */
    public function logFailedLogin(string $email, Request $request, string $reason = 'Invalid credentials'): void
    {
        $logData = [
            'event_type' => 'LOGIN_FAILED',
            'email' => $email,
            'reason' => $reason,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
            'login_method' => 'web'
        ];

        // Log to Laravel log
        Log::warning('Failed login attempt', $logData);

        // Also save to audit_logs table
        try {
            // Try to find user by email
            $user = \App\Models\User::where('email', $email)->first();
            
            AuditLog::create([
                'user_id' => $user ? $user->id : null,
                'action' => 'login_failed',
                'model_type' => 'User',
                'model_id' => $user ? $user->id : null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'details' => json_encode([
                    'email' => $email,
                    'reason' => $reason,
                    'login_method' => 'web'
                ]),
                'timestamp' => now(),
                'created_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save audit log: ' . $e->getMessage());
        }
    }

    /**
     * Log successful logout.
     *
     * @param mixed $user
     * @param Request $request
     * @return void
     */
    public function logLogout($user, Request $request): void
    {
        $logData = [
            'event_type' => 'LOGOUT',
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role->name ?? 'No Role',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => $request->hasSession() ? $request->session()->getId() : null,
            'timestamp' => now()
        ];

        // Log to Laravel log
        Log::info('User logout', $logData);

        // Also save to audit_logs table
        try {
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'logout',
                'model_type' => 'User',
                'model_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'details' => json_encode([
                    'email' => $user->email,
                    'role' => $user->role->name ?? 'No Role',
                    'login_method' => 'web'
                ]),
                'timestamp' => now(),
                'created_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save audit log: ' . $e->getMessage());
        }
    }

    /**
     * Log unauthorized access attempt.
     *
     * @param mixed $user
     * @param Request $request
     * @param string $resource
     * @param string $permission
     * @return void
     */
    public function logUnauthorizedAccess($user, Request $request, string $resource, string $permission = null): void
    {
        Log::warning('Unauthorized access attempt', [
            'event_type' => 'ACCESS_DENIED',
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role->name ?? 'No Role',
            'user_permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            'requested_resource' => $resource,
            'required_permission' => $permission,
            'url' => $request->fullUrl(),
            'route_name' => $request->route()->getName(),
            'method' => $request->method(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()
        ]);
    }

    /**
     * Log successful route access.
     *
     * @param mixed $user
     * @param Request $request
     * @param string $resource
     * @return void
     */
    public function logAuthorizedAccess($user, Request $request, string $resource): void
    {
        Log::info('Authorized access', [
            'event_type' => 'ACCESS_GRANTED',
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role->name ?? 'No Role',
            'resource' => $resource,
            'url' => $request->fullUrl(),
            'route_name' => $request->route()->getName(),
            'method' => $request->method(),
            'ip_address' => $request->ip(),
            'timestamp' => now()
        ]);
    }

    /**
     * Log session security events.
     *
     * @param mixed $user
     * @param Request $request
     * @param string $eventType
     * @param array $context
     * @return void
     */
    public function logSessionEvent($user, Request $request, string $eventType, array $context = []): void
    {
        Log::warning('Session security event', array_merge([
            'event_type' => $eventType,
            'user_id' => $user->id,
            'email' => $user->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => $request->hasSession() ? $request->session()->getId() : null,
            'timestamp' => now()
        ], $context));
    }

    /**
     * Log permission changes.
     *
     * @param mixed $user
     * @param mixed $targetUser
     * @param string $action
     * @param array $permissions
     * @param Request $request
     * @return void
     */
    public function logPermissionChange($user, $targetUser, string $action, array $permissions, Request $request): void
    {
        Log::info('Permission change', [
            'event_type' => 'PERMISSION_CHANGE',
            'admin_user_id' => $user->id,
            'admin_email' => $user->email,
            'target_user_id' => $targetUser->id,
            'target_email' => $targetUser->email,
            'action' => $action, // 'granted', 'revoked', 'updated'
            'permissions' => $permissions,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()
        ]);
    }

    /**
     * Log role changes.
     *
     * @param mixed $user
     * @param mixed $targetUser
     * @param string $action
     * @param string $role
     * @param Request $request
     * @return void
     */
    public function logRoleChange($user, $targetUser, string $action, string $role, Request $request): void
    {
        Log::info('Role change', [
            'event_type' => 'ROLE_CHANGE',
            'admin_user_id' => $user->id,
            'admin_email' => $user->email,
            'target_user_id' => $targetUser->id,
            'target_email' => $targetUser->email,
            'action' => $action, // 'assigned', 'removed', 'updated'
            'role' => $role,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()
        ]);
    }

    /**
     * Log suspicious activity.
     *
     * @param mixed $user
     * @param Request $request
     * @param string $activity
     * @param array $context
     * @return void
     */
    public function logSuspiciousActivity($user, Request $request, string $activity, array $context = []): void
    {
        Log::warning('Suspicious activity detected', array_merge([
            'event_type' => 'SUSPICIOUS_ACTIVITY',
            'user_id' => $user ? $user->id : null,
            'email' => $user ? $user->email : 'Unknown',
            'activity' => $activity,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'timestamp' => now()
        ], $context));
    }

    /**
     * Get security audit summary for a user.
     *
     * @param int $userId
     * @param int $days
     * @return array
     */
    public function getAuditSummary(int $userId, int $days = 30): array
    {
        // This would typically query a dedicated audit log table
        // For now, we'll return a basic structure
        return [
            'user_id' => $userId,
            'period_days' => $days,
            'login_attempts' => 0, // Would be calculated from logs
            'successful_logins' => 0,
            'failed_logins' => 0,
            'access_violations' => 0,
            'last_login' => null,
            'unique_ips' => [],
            'generated_at' => now()
        ];
    }

    /**
     * Check for brute force attempts.
     *
     * @param string $email
     * @param Request $request
     * @param int $maxAttempts
     * @param int $timeWindow
     * @return bool
     */
    public function checkBruteForce(string $email, Request $request, int $maxAttempts = 5, int $timeWindow = 15): bool
    {
        // This would typically check a cache or database for recent failed attempts
        // For now, we'll log the check and return false (no brute force detected)
        Log::info('Brute force check', [
            'email' => $email,
            'ip_address' => $request->ip(),
            'max_attempts' => $maxAttempts,
            'time_window_minutes' => $timeWindow,
            'timestamp' => now()
        ]);
        
        return false;
    }
}