<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Services\SecurityAuditService;
use Symfony\Component\HttpFoundation\Response;

class SessionSecurity
{
    /**
     * The security audit service instance.
     *
     * @var SecurityAuditService
     */
    protected $securityAuditService;

    /**
     * Create a new middleware instance.
     *
     * @param SecurityAuditService $securityAuditService
     */
    public function __construct(SecurityAuditService $securityAuditService)
    {
        $this->securityAuditService = $securityAuditService;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip if user is not authenticated
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        
        // Check if user account is still active
        if ($user->status !== 1) {
            $this->securityAuditService->logSessionEvent($user, $request, 'SESSION_TERMINATED_INACTIVE_USER', [
                'user_status' => $user->status
            ]);
            
            Auth::logout();
            Session::invalidate();
            Session::regenerateToken();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account has been deactivated. Please contact the administrator.',
                    'error_code' => 'ACCOUNT_DEACTIVATED'
                ], 401);
            }
            
            return redirect()->route('login')
                ->with('error', 'Your account has been deactivated. Please contact the administrator.');
        }

        // Validate session integrity
        $sessionUserId = Session::get('login.web.' . sha1('web'));
        if ($sessionUserId && $sessionUserId != $user->id) {
            $this->securityAuditService->logSessionEvent($user, $request, 'SESSION_INTEGRITY_VIOLATION', [
                'authenticated_user_id' => $user->id,
                'session_user_id' => $sessionUserId
            ]);
            
            Auth::logout();
            Session::invalidate();
            Session::regenerateToken();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session security violation detected. Please log in again.',
                    'error_code' => 'SESSION_VIOLATION'
                ], 401);
            }
            
            return redirect()->route('login')
                ->with('error', 'Session security violation detected. Please log in again.');
        }

        // Check for suspicious IP changes (optional - can be configured)
        $lastKnownIp = Session::get('user.last_ip');
        $currentIp = $request->ip();
        
        if ($lastKnownIp && $lastKnownIp !== $currentIp) {
            $this->securityAuditService->logSessionEvent($user, $request, 'IP_ADDRESS_CHANGE', [
                'previous_ip' => $lastKnownIp,
                'current_ip' => $currentIp
            ]);
            
            // Update the session with new IP
            Session::put('user.last_ip', $currentIp);
        } else {
            // Set IP for first time
            Session::put('user.last_ip', $currentIp);
        }

        // Update last activity timestamp
        Session::put('user.last_activity', now());
        
        // Regenerate session ID periodically for security
        $lastRegeneration = Session::get('session.last_regeneration', 0);
        if (time() - $lastRegeneration > 1800) { // 30 minutes
            Session::regenerate();
            Session::put('session.last_regeneration', time());
            
            $this->securityAuditService->logSessionEvent($user, $request, 'SESSION_ID_REGENERATED', [
                'regeneration_interval_minutes' => 30
            ]);
        }

        return $next($request);
    }
}