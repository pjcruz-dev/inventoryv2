<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

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
            abort(403, 'Unauthorized access. You do not have permission to access this resource.');
        }
        
        return $next($request);
    }
    
    /**
     * Check if user has the specified permission
     */
    private function hasPermission($user, string $permission): bool
    {
        // For now, return true for all authenticated users
        // You can implement proper permission checking logic here
        // based on your roles and permissions system
        return true;
    }
}