<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SecurityService
{
    /**
     * Sanitize input data to prevent XSS attacks
     */
    public static function sanitizeInput($input)
    {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        
        if (is_string($input)) {
            // Remove null bytes
            $input = str_replace("\0", '', $input);
            
            // Trim whitespace
            $input = trim($input);
            
            // Convert special characters to HTML entities
            $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            
            return $input;
        }
        
        return $input;
    }
    
    /**
     * Validate and sanitize file uploads
     */
    public static function validateFileUpload($file, $allowedTypes = [], $maxSize = 10240)
    {
        $errors = [];
        
        // Check if file exists
        if (!$file || !$file->isValid()) {
            $errors[] = 'Invalid file upload.';
            return ['valid' => false, 'errors' => $errors];
        }
        
        // Check file size (in KB)
        if ($file->getSize() > $maxSize * 1024) {
            $errors[] = "File size exceeds maximum allowed size of {$maxSize}KB.";
        }
        
        // Check file type
        if (!empty($allowedTypes)) {
            $mimeType = $file->getMimeType();
            $extension = strtolower($file->getClientOriginalExtension());
            
            if (!in_array($mimeType, $allowedTypes) && !in_array($extension, $allowedTypes)) {
                $errors[] = 'File type not allowed.';
            }
        }
        
        // Check for malicious file extensions
        $dangerousExtensions = ['php', 'phtml', 'php3', 'php4', 'php5', 'pl', 'py', 'jsp', 'asp', 'sh', 'cgi'];
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (in_array($extension, $dangerousExtensions)) {
            $errors[] = 'Dangerous file type detected.';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'sanitized_name' => self::sanitizeFileName($file->getClientOriginalName())
        ];
    }
    
    /**
     * Sanitize file name to prevent directory traversal
     */
    public static function sanitizeFileName($filename)
    {
        // Remove path traversal attempts
        $filename = basename($filename);
        
        // Remove null bytes
        $filename = str_replace("\0", '', $filename);
        
        // Replace dangerous characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        // Ensure filename is not empty
        if (empty($filename)) {
            $filename = 'file_' . time();
        }
        
        return $filename;
    }
    
    /**
     * Validate email address with additional security checks
     */
    public static function validateEmail($email)
    {
        // Basic email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        // Check for suspicious patterns
        $suspiciousPatterns = [
            '/<script/i',
            '/javascript:/i',
            '/data:/i',
            '/vbscript:/i',
            '/onload/i',
            '/onerror/i'
        ];
        
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $email)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Validate password strength
     */
    public static function validatePasswordStrength($password)
    {
        $errors = [];
        
        // Minimum length
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long.';
        }
        
        // Check for uppercase letter
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter.';
        }
        
        // Check for lowercase letter
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter.';
        }
        
        // Check for number
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number.';
        }
        
        // Check for special character
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special character.';
        }
        
        // Check for common passwords
        $commonPasswords = [
            'password', '123456', '123456789', 'qwerty', 'abc123',
            'password123', 'admin', 'letmein', 'welcome', 'monkey'
        ];
        
        if (in_array(strtolower($password), $commonPasswords)) {
            $errors[] = 'Password is too common. Please choose a more secure password.';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Log security events
     */
    public static function logSecurityEvent($event, $details = [], $userId = null)
    {
        $logData = [
            'event' => $event,
            'user_id' => $userId ?? auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'details' => $details,
            'timestamp' => now()
        ];
        
        Log::channel('security')->info('Security Event', $logData);
    }
    
    /**
     * Check for suspicious activity
     */
    public static function checkSuspiciousActivity($userId = null)
    {
        $userId = $userId ?? auth()->id();
        $ip = request()->ip();
        
        // Check for multiple failed login attempts
        $failedAttempts = Log::channel('security')
            ->where('context.user_id', $userId)
            ->where('context.event', 'failed_login')
            ->where('created_at', '>=', now()->subMinutes(15))
            ->count();
        
        if ($failedAttempts >= 5) {
            self::logSecurityEvent('suspicious_activity', [
                'type' => 'multiple_failed_logins',
                'failed_attempts' => $failedAttempts
            ], $userId);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Generate secure random token
     */
    public static function generateSecureToken($length = 32)
    {
        return bin2hex(random_bytes($length));
    }
    
    /**
     * Validate CSRF token
     */
    public static function validateCsrfToken($token)
    {
        return hash_equals(session()->token(), $token);
    }
    
    /**
     * Sanitize SQL query parameters
     */
    public static function sanitizeSqlParameter($parameter)
    {
        // Remove SQL injection patterns
        $dangerousPatterns = [
            '/(\b(SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER|EXEC|UNION|SCRIPT)\b)/i',
            '/(\b(OR|AND)\s+\d+\s*=\s*\d+)/i',
            '/(\b(OR|AND)\s+\'\s*=\s*\')/i',
            '/(\b(OR|AND)\s+"\s*=\s*")/i',
            '/(\b(OR|AND)\s+1\s*=\s*1)/i',
            '/(\b(OR|AND)\s+\'\s*=\s*\'\s*--)/i',
            '/(\b(OR|AND)\s+"\s*=\s*"\s*--)/i',
            '/(\b(OR|AND)\s+1\s*=\s*1\s*--)/i',
            '/(\b(OR|AND)\s+\'\s*=\s*\'\s*#)/i',
            '/(\b(OR|AND)\s+"\s*=\s*"\s*#)/i',
            '/(\b(OR|AND)\s+1\s*=\s*1\s*#)/i'
        ];
        
        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $parameter)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Rate limiting check
     */
    public static function checkRateLimit($key, $maxAttempts = 60, $decayMinutes = 1)
    {
        $key = 'rate_limit:' . $key . ':' . request()->ip();
        
        $attempts = cache()->get($key, 0);
        
        if ($attempts >= $maxAttempts) {
            self::logSecurityEvent('rate_limit_exceeded', [
                'key' => $key,
                'attempts' => $attempts,
                'max_attempts' => $maxAttempts
            ]);
            
            return false;
        }
        
        cache()->put($key, $attempts + 1, now()->addMinutes($decayMinutes));
        
        return true;
    }
}
