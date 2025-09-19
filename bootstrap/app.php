<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
        
        // Register custom middleware aliases
        $middleware->alias([
            'validate.file.upload' => \App\Http\Middleware\ValidateFileUpload::class,
            'check.import.export.permission' => \App\Http\Middleware\CheckImportExportPermission::class,
            'check.permission' => \App\Http\Middleware\CheckPermission::class,
            'validate.csrf' => \App\Http\Middleware\ValidateCSRF::class,
            'role.hierarchy' => \App\Http\Middleware\RoleHierarchy::class,
            'route.access.control' => \App\Http\Middleware\RouteAccessControl::class,
            'session.security' => \App\Http\Middleware\SessionSecurity::class,
            'prevent.maintenance.edit' => \App\Http\Middleware\PreventMaintenanceAssetEdit::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
        
        // Apply global middleware for authenticated routes
        $middleware->web(append: [
            \App\Http\Middleware\SessionSecurity::class,
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Register custom exception handling
        $exceptions->render(function (\App\Exceptions\AccessDeniedException $e, \Illuminate\Http\Request $request) {
            return $e->render($request);
        });
        
        // Handle authentication exceptions with proper logging
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            \Illuminate\Support\Facades\Log::info('Authentication required', [
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required',
                    'error_code' => 'AUTHENTICATION_REQUIRED'
                ], 401);
            }
            
            return redirect()->guest(route('login'));
        });
    })->create();
