<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\BreadcrumbService;
use Illuminate\Support\Facades\View;

class SetBreadcrumbs
{
    public function __construct()
    {
        // Don't inject BreadcrumbService to avoid singleton issues
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Create a fresh BreadcrumbService instance for each request
        $breadcrumbService = new BreadcrumbService();
        
        // Get the current route name
        $routeName = $request->route()?->getName();
        $routeParameters = $request->route()?->parameters() ?? [];

        // Set breadcrumbs based on the current route
        if ($routeName) {
            $breadcrumbService->setForRoute($routeName, $routeParameters);
        } else {
            // Fallback for routes without names
            $breadcrumbService->addDashboard()->add('Current Page', null, true);
        }

        // Share breadcrumbs with all views
        View::share('breadcrumbs', $breadcrumbService->getBreadcrumbs());

        return $next($request);
    }
}
