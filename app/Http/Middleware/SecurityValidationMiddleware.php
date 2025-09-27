<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\SecurityService;
use App\Services\AuditService;

class SecurityValidationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Rate limiting check
        if (!SecurityService::checkRateLimit('api_requests', 100, 1)) {
            AuditService::logSystemEvent('rate_limit_exceeded', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl()
            ]);
            
            return response()->json([
                'error' => 'Too many requests. Please try again later.'
            ], 429);
        }
        
        // Sanitize all input data
        $this->sanitizeInputs($request);
        
        // Check for suspicious patterns in input
        if ($this->detectSuspiciousInput($request)) {
            AuditService::logSecurityEvent('suspicious_input_detected', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'inputs' => $request->all()
            ]);
            
            return response()->json([
                'error' => 'Invalid input detected.'
            ], 400);
        }
        
        // Validate file uploads if present
        if ($request->hasFile('csv_file') || $request->hasFile('file')) {
            $file = $request->file('csv_file') ?? $request->file('file');
            $validation = SecurityService::validateFileUpload($file, [
                'text/csv',
                'application/csv',
                'application/vnd.ms-excel',
                'csv'
            ], 10240); // 10MB max
            
            if (!$validation['valid']) {
                AuditService::logFileOperation('upload_rejected', $file->getClientOriginalName(), [
                    'errors' => $validation['errors'],
                    'ip' => $request->ip()
                ]);
                
                return response()->json([
                    'error' => 'File upload validation failed.',
                    'details' => $validation['errors']
                ], 400);
            }
        }
        
        return $next($request);
    }
    
    /**
     * Sanitize all input data
     */
    private function sanitizeInputs(Request $request)
    {
        $inputs = $request->all();
        $sanitized = SecurityService::sanitizeInput($inputs);
        
        // Replace the request data with sanitized version
        $request->replace($sanitized);
    }
    
    /**
     * Detect suspicious input patterns
     */
    private function detectSuspiciousInput(Request $request)
    {
        $suspiciousPatterns = [
            // SQL Injection patterns
            '/(\b(SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER|EXEC|UNION|SCRIPT)\b)/i',
            '/(\b(OR|AND)\s+\d+\s*=\s*\d+)/i',
            '/(\b(OR|AND)\s+\'\s*=\s*\')/i',
            '/(\b(OR|AND)\s+"\s*=\s*")/i',
            '/(\b(OR|AND)\s+1\s*=\s*1)/i',
            '/(\b(OR|AND)\s+\'\s*=\s*\'\s*--)/i',
            '/(\b(OR|AND)\s+"\s*=\s*"\s*--)/i',
            '/(\b(OR|AND)\s+1\s*=\s*1\s*--)/i',
            
            // XSS patterns
            '/<script[^>]*>.*?<\/script>/i',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload\s*=/i',
            '/onerror\s*=/i',
            '/onclick\s*=/i',
            '/onmouseover\s*=/i',
            
            // Path traversal patterns
            '/\.\.\//',
            '/\.\.\\\\/',
            '/%2e%2e%2f/i',
            '/%2e%2e%5c/i',
            
            // Command injection patterns
            '/[;&|`$]/',
            '/\b(cat|ls|dir|type|more|less|head|tail|grep|find|awk|sed|perl|python|ruby|php|sh|bash|cmd|powershell)\b/i'
        ];
        
        $allInputs = json_encode($request->all());
        
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $allInputs)) {
                return true;
            }
        }
        
        return false;
    }
}
