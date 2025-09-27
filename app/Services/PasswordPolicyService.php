<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Services\AuditService;

class PasswordPolicyService
{
    /**
     * Password policy configuration
     */
    private static $policy = [
        'min_length' => 8,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_symbols' => true,
        'max_age_days' => 90,
        'prevent_reuse_count' => 5,
        'lockout_attempts' => 5,
        'lockout_duration_minutes' => 15
    ];
    
    /**
     * Validate password against policy
     */
    public static function validatePassword($password, $userId = null)
    {
        $errors = [];
        
        // Length check
        if (strlen($password) < self::$policy['min_length']) {
            $errors[] = "Password must be at least " . self::$policy['min_length'] . " characters long.";
        }
        
        // Uppercase check
        if (self::$policy['require_uppercase'] && !preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter.";
        }
        
        // Lowercase check
        if (self::$policy['require_lowercase'] && !preg_match('/[a-z]/', $password)) {
            $errors[] = "Password must contain at least one lowercase letter.";
        }
        
        // Numbers check
        if (self::$policy['require_numbers'] && !preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one number.";
        }
        
        // Symbols check
        if (self::$policy['require_symbols'] && !preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = "Password must contain at least one special character.";
        }
        
        // Common password check
        if (self::isCommonPassword($password)) {
            $errors[] = "Password is too common. Please choose a more secure password.";
        }
        
        // Reuse check
        if ($userId && self::isPasswordReused($password, $userId)) {
            $errors[] = "Password cannot be reused. Please choose a different password.";
        }
        
        // Log validation attempt
        if (!empty($errors)) {
            AuditService::logSecurityEvent('password_validation_failed', [
                'user_id' => $userId,
                'errors' => $errors
            ]);
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Check if password is common
     */
    private static function isCommonPassword($password)
    {
        $commonPasswords = [
            'password', '123456', '123456789', 'qwerty', 'abc123',
            'password123', 'admin', 'letmein', 'welcome', 'monkey',
            '1234567890', 'password1', 'qwerty123', 'admin123',
            'welcome123', 'password1234', '12345678', 'qwertyuiop',
            'password12', 'admin1234', 'welcome1', 'password1!',
            '123456789', 'qwerty1234', 'admin123!', 'welcome123!'
        ];
        
        return in_array(strtolower($password), $commonPasswords);
    }
    
    /**
     * Check if password is being reused
     */
    private static function isPasswordReused($password, $userId)
    {
        $passwordHistory = Cache::get("password_history_{$userId}", []);
        
        foreach ($passwordHistory as $hashedPassword) {
            if (Hash::check($password, $hashedPassword)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Add password to history
     */
    public static function addToPasswordHistory($password, $userId)
    {
        $passwordHistory = Cache::get("password_history_{$userId}", []);
        
        // Add new password hash
        array_unshift($passwordHistory, Hash::make($password));
        
        // Keep only the last N passwords
        $passwordHistory = array_slice($passwordHistory, 0, self::$policy['prevent_reuse_count']);
        
        // Store for 1 year
        Cache::put("password_history_{$userId}", $passwordHistory, now()->addYear());
        
        // Log password change
        AuditService::logSecurityEvent('password_changed', [
            'user_id' => $userId
        ]);
    }
    
    /**
     * Check if password is expired
     */
    public static function isPasswordExpired($lastPasswordChange)
    {
        if (!$lastPasswordChange) {
            return true;
        }
        
        $expiryDate = $lastPasswordChange->addDays(self::$policy['max_age_days']);
        
        return now()->isAfter($expiryDate);
    }
    
    /**
     * Get password expiry date
     */
    public static function getPasswordExpiryDate($lastPasswordChange)
    {
        if (!$lastPasswordChange) {
            return now();
        }
        
        return $lastPasswordChange->addDays(self::$policy['max_age_days']);
    }
    
    /**
     * Check failed login attempts
     */
    public static function checkFailedAttempts($userId, $ip = null)
    {
        $key = "failed_attempts_{$userId}_" . ($ip ?? request()->ip());
        $attempts = Cache::get($key, 0);
        
        return [
            'attempts' => $attempts,
            'is_locked' => $attempts >= self::$policy['lockout_attempts'],
            'remaining_attempts' => max(0, self::$policy['lockout_attempts'] - $attempts)
        ];
    }
    
    /**
     * Record failed login attempt
     */
    public static function recordFailedAttempt($userId, $ip = null)
    {
        $key = "failed_attempts_{$userId}_" . ($ip ?? request()->ip());
        $attempts = Cache::get($key, 0) + 1;
        
        Cache::put($key, $attempts, now()->addMinutes(self::$policy['lockout_duration_minutes']));
        
        // Log failed attempt
        AuditService::logSecurityEvent('failed_login_attempt', [
            'user_id' => $userId,
            'ip' => $ip ?? request()->ip(),
            'attempts' => $attempts
        ]);
        
        // Check if account should be locked
        if ($attempts >= self::$policy['lockout_attempts']) {
            AuditService::logSecurityEvent('account_locked', [
                'user_id' => $userId,
                'ip' => $ip ?? request()->ip(),
                'reason' => 'too_many_failed_attempts'
            ]);
        }
    }
    
    /**
     * Clear failed attempts
     */
    public static function clearFailedAttempts($userId, $ip = null)
    {
        $key = "failed_attempts_{$userId}_" . ($ip ?? request()->ip());
        Cache::forget($key);
    }
    
    /**
     * Generate secure password
     */
    public static function generateSecurePassword($length = 12)
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        
        $password = '';
        
        // Ensure at least one character from each required set
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];
        
        // Fill the rest with random characters
        $allChars = $uppercase . $lowercase . $numbers . $symbols;
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }
        
        // Shuffle the password
        return str_shuffle($password);
    }
    
    /**
     * Get password policy requirements
     */
    public static function getPolicyRequirements()
    {
        return [
            'min_length' => self::$policy['min_length'],
            'require_uppercase' => self::$policy['require_uppercase'],
            'require_lowercase' => self::$policy['require_lowercase'],
            'require_numbers' => self::$policy['require_numbers'],
            'require_symbols' => self::$policy['require_symbols'],
            'max_age_days' => self::$policy['max_age_days'],
            'prevent_reuse_count' => self::$policy['prevent_reuse_count']
        ];
    }
}
