<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Add security headers
        $response->headers->set('X-Frame-Options', config('security.security_headers.x_frame_options', 'DENY'));
        $response->headers->set('X-Content-Type-Options', config('security.security_headers.x_content_type_options', 'nosniff'));
        $response->headers->set('X-XSS-Protection', config('security.security_headers.x_xss_protection', '1; mode=block'));
        $response->headers->set('Referrer-Policy', config('security.security_headers.referrer_policy', 'strict-origin-when-cross-origin'));
        $response->headers->set('Content-Security-Policy', config('security.security_headers.content_security_policy', "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline';"));
        
        // Add HSTS header for HTTPS
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }
        
        // Add Permissions-Policy header
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        
        // Remove server information
        $response->headers->remove('Server');
        $response->headers->remove('X-Powered-By');
        
        return $response;
    }
}
