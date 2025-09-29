<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\SecurityService;
use App\Services\AuditService;

trait SecureValidation
{
    /**
     * Validate request with enhanced security checks
     */
    protected function validateWithSecurity(Request $request, array $rules, array $messages = [])
    {
        // Sanitize all inputs first
        $sanitizedData = SecurityService::sanitizeInput($request->all());
        $request->replace($sanitizedData);
        
        // Perform validation
        $validator = Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            // Log validation failures for security monitoring
            AuditService::logSecurityEvent('validation_failed', [
                'errors' => $validator->errors()->toArray(),
                'inputs' => $request->all(),
                'url' => $request->fullUrl()
            ]);
        }
        
        return $validator;
    }
    
    /**
     * Validate email with security checks
     */
    protected function validateEmail($email)
    {
        if (!SecurityService::validateEmail($email)) {
            return [
                'valid' => false,
                'error' => 'Invalid or suspicious email address.'
            ];
        }
        
        return ['valid' => true];
    }
    
    /**
     * Validate password strength
     */
    protected function validatePassword($password)
    {
        return SecurityService::validatePasswordStrength($password);
    }
    
    /**
     * Validate file upload with security checks
     */
    protected function validateFileUpload($file, $allowedTypes = [], $maxSize = 10240)
    {
        return SecurityService::validateFileUpload($file, $allowedTypes, $maxSize);
    }
    
    /**
     * Validate and sanitize search input
     */
    protected function validateSearchInput($searchTerm)
    {
        // Check for SQL injection patterns
        if (!SecurityService::sanitizeSqlParameter($searchTerm)) {
            AuditService::logSecurityEvent('sql_injection_attempt', [
                'search_term' => $searchTerm,
                'ip' => request()->ip()
            ]);
            
            return [
                'valid' => false,
                'error' => 'Invalid search term.'
            ];
        }
        
        // Sanitize the search term
        $sanitized = SecurityService::sanitizeInput($searchTerm);
        
        return [
            'valid' => true,
            'sanitized' => $sanitized
        ];
    }
    
    /**
     * Validate numeric input with range checks
     */
    protected function validateNumericInput($value, $min = null, $max = null)
    {
        if (!is_numeric($value)) {
            return [
                'valid' => false,
                'error' => 'Value must be numeric.'
            ];
        }
        
        $numericValue = (float) $value;
        
        if ($min !== null && $numericValue < $min) {
            return [
                'valid' => false,
                'error' => "Value must be at least {$min}."
            ];
        }
        
        if ($max !== null && $numericValue > $max) {
            return [
                'valid' => false,
                'error' => "Value must not exceed {$max}."
            ];
        }
        
        return [
            'valid' => true,
            'value' => $numericValue
        ];
    }
    
    /**
     * Validate date input with security checks
     */
    protected function validateDateInput($date, $format = 'Y-m-d')
    {
        if (empty($date)) {
            return ['valid' => true, 'value' => null];
        }
        
        // Check for suspicious patterns in date
        $suspiciousPatterns = [
            '/<script/i',
            '/javascript:/i',
            '/onload/i',
            '/onerror/i'
        ];
        
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $date)) {
                AuditService::logSecurityEvent('suspicious_date_input', [
                    'date' => $date,
                    'ip' => request()->ip()
                ]);
                
                return [
                    'valid' => false,
                    'error' => 'Invalid date format.'
                ];
            }
        }
        
        $parsedDate = \DateTime::createFromFormat($format, $date);
        
        if (!$parsedDate || $parsedDate->format($format) !== $date) {
            return [
                'valid' => false,
                'error' => 'Invalid date format.'
            ];
        }
        
        return [
            'valid' => true,
            'value' => $parsedDate
        ];
    }
    
    /**
     * Validate and sanitize text input
     */
    protected function validateTextInput($text, $maxLength = 255)
    {
        if (strlen($text) > $maxLength) {
            return [
                'valid' => false,
                'error' => "Text must not exceed {$maxLength} characters."
            ];
        }
        
        // Check for XSS patterns
        $xssPatterns = [
            '/<script[^>]*>.*?<\/script>/i',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload\s*=/i',
            '/onerror\s*=/i'
        ];
        
        foreach ($xssPatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                AuditService::logSecurityEvent('xss_attempt', [
                    'text' => $text,
                    'ip' => request()->ip()
                ]);
                
                return [
                    'valid' => false,
                    'error' => 'Invalid text content detected.'
                ];
            }
        }
        
        $sanitized = SecurityService::sanitizeInput($text);
        
        return [
            'valid' => true,
            'sanitized' => $sanitized
        ];
    }
    
    /**
     * Log data access for audit trail
     */
    protected function logDataAccess($action, $model = null, $modelId = null, $details = [])
    {
        AuditService::logAction($action, $model, $modelId, $details);
    }
    
    /**
     * Log data changes for audit trail
     */
    protected function logDataChange($model, $action, $oldData = null, $newData = null)
    {
        AuditService::logDataChange($model, $action, $oldData, $newData);
    }
}
