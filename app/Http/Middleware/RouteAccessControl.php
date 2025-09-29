<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\RedirectionService;
use App\Services\SecurityAuditService;
use Symfony\Component\HttpFoundation\Response;

class RouteAccessControl
{
    /**
     * The redirection service instance.
     *
     * @var RedirectionService
     */
    protected $redirectionService;

    /**
     * The security audit service instance.
     *
     * @var SecurityAuditService
     */
    protected $securityAuditService;

    /**
     * Create a new middleware instance.
     *
     * @param RedirectionService $redirectionService
     * @param SecurityAuditService $securityAuditService
     */
    public function __construct(RedirectionService $redirectionService, SecurityAuditService $securityAuditService)
    {
        $this->redirectionService = $redirectionService;
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
        $routeName = $request->route()->getName();
        
        // Skip access control for certain routes
        $exemptRoutes = [
            'dashboard',
            'home',
            'logout',
            'password.request',
            'password.email',
            'password.reset',
            'password.update',
            'password.change',
            'verification.notice',
            'verification.verify',
            'verification.send'
        ];

        if (in_array($routeName, $exemptRoutes)) {
            return $next($request);
        }

        // Check if user has access to the requested route
        if (!$this->redirectionService->canAccessRoute($user, $routeName)) {
            // Log unauthorized access attempt using SecurityAuditService
            $this->securityAuditService->logUnauthorizedAccess($user, $request, $routeName);

            // Handle AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. You do not have permission to access this resource.',
                    'error_code' => 'INSUFFICIENT_PERMISSIONS'
                ], 403);
            }

            // Redirect to appropriate page with error message
            $redirectUrl = $this->redirectionService->getRedirectUrl($user);
            
            return redirect($redirectUrl)
                ->with('error', 'Access denied. You do not have permission to access the requested page.')
                ->with('error_code', 'INSUFFICIENT_PERMISSIONS');
        }

        // Log successful route access using SecurityAuditService
        $this->securityAuditService->logAuthorizedAccess($user, $request, $routeName);

        return $next($request);
    }
}