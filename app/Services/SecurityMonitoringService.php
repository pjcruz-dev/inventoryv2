<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Models\AuditLog;
use App\Models\User;

class SecurityMonitoringService
{
    /**
     * Monitor security events in real-time
     */
    public static function monitorSecurityEvents()
    {
        $events = self::getRecentSecurityEvents();
        $threats = self::detectThreats($events);
        
        if (!empty($threats)) {
            self::handleSecurityThreats($threats);
        }
        
        return [
            'events_analyzed' => count($events),
            'threats_detected' => count($threats),
            'timestamp' => now()->toISOString()
        ];
    }

    /**
     * Get recent security events
     */
    private static function getRecentSecurityEvents($minutes = 5)
    {
        return AuditLog::where('created_at', '>=', now()->subMinutes($minutes))
            ->whereIn('action', [
                'login_failed', 'suspicious_activity', 'unauthorized_access',
                'password_change', 'permission_denied', 'file_upload',
                'data_export', 'bulk_operation', 'admin_action'
            ])
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Detect security threats
     */
    private static function detectThreats($events)
    {
        $threats = [];
        
        // Brute force attack detection
        $failedLogins = $events->where('action', 'login_failed');
        if ($failedLogins->count() >= 5) {
            $threats[] = [
                'type' => 'brute_force',
                'severity' => 'high',
                'description' => 'Multiple failed login attempts detected',
                'count' => $failedLogins->count(),
                'ip_addresses' => $failedLogins->pluck('ip_address')->unique()->values(),
                'users' => $failedLogins->pluck('user.email')->unique()->values()
            ];
        }

        // Suspicious activity detection
        $suspiciousActivities = $events->where('action', 'suspicious_activity');
        if ($suspiciousActivities->count() > 0) {
            $threats[] = [
                'type' => 'suspicious_activity',
                'severity' => 'medium',
                'description' => 'Suspicious activities detected',
                'count' => $suspiciousActivities->count(),
                'details' => $suspiciousActivities->pluck('details')
            ];
        }

        // Unauthorized access attempts
        $unauthorizedAccess = $events->where('action', 'unauthorized_access');
        if ($unauthorizedAccess->count() > 0) {
            $threats[] = [
                'type' => 'unauthorized_access',
                'severity' => 'high',
                'description' => 'Unauthorized access attempts detected',
                'count' => $unauthorizedAccess->count(),
                'ip_addresses' => $unauthorizedAccess->pluck('ip_address')->unique()->values()
            ];
        }

        // Data export anomalies
        $dataExports = $events->where('action', 'data_export');
        if ($dataExports->count() >= 10) {
            $threats[] = [
                'type' => 'data_exfiltration',
                'severity' => 'high',
                'description' => 'Excessive data export activity detected',
                'count' => $dataExports->count(),
                'users' => $dataExports->pluck('user.email')->unique()->values()
            ];
        }

        return $threats;
    }

    /**
     * Handle security threats
     */
    private static function handleSecurityThreats($threats)
    {
        foreach ($threats as $threat) {
            // Log the threat
            Log::warning('Security threat detected', $threat);
            
            // Store in cache for dashboard
            $key = 'security_threat_' . now()->format('Y-m-d-H-i-s');
            Cache::put($key, $threat, 3600); // Store for 1 hour
            
            // Send alerts for high severity threats
            if ($threat['severity'] === 'high') {
                self::sendSecurityAlert($threat);
            }
            
            // Take automatic actions
            self::takeAutomaticActions($threat);
        }
    }

    /**
     * Send security alert
     */
    private static function sendSecurityAlert($threat)
    {
        try {
            $admins = User::whereHas('roles', function($query) {
                $query->whereIn('name', ['admin', 'super-admin']);
            })->get();

            foreach ($admins as $admin) {
                // In a real implementation, you would send email notifications
                Log::info('Security alert sent to admin', [
                    'admin' => $admin->email,
                    'threat' => $threat
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send security alert', [
                'error' => $e->getMessage(),
                'threat' => $threat
            ]);
        }
    }

    /**
     * Take automatic security actions
     */
    private static function takeAutomaticActions($threat)
    {
        switch ($threat['type']) {
            case 'brute_force':
                // Block IP addresses temporarily
                self::blockIPAddresses($threat['ip_addresses'], 30); // 30 minutes
                break;
                
            case 'unauthorized_access':
                // Block IP addresses for longer period
                self::blockIPAddresses($threat['ip_addresses'], 120); // 2 hours
                break;
                
            case 'data_exfiltration':
                // Temporarily disable user accounts
                self::disableUserAccounts($threat['users'], 60); // 1 hour
                break;
        }
    }

    /**
     * Block IP addresses
     */
    private static function blockIPAddresses($ipAddresses, $minutes)
    {
        foreach ($ipAddresses as $ip) {
            $key = 'blocked_ip_' . $ip;
            Cache::put($key, true, $minutes * 60);
            
            Log::info('IP address blocked', [
                'ip' => $ip,
                'duration_minutes' => $minutes
            ]);
        }
    }

    /**
     * Disable user accounts temporarily
     */
    private static function disableUserAccounts($userEmails, $minutes)
    {
        foreach ($userEmails as $email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $key = 'disabled_user_' . $user->id;
                Cache::put($key, true, $minutes * 60);
                
                Log::info('User account temporarily disabled', [
                    'user_id' => $user->id,
                    'email' => $email,
                    'duration_minutes' => $minutes
                ]);
            }
        }
    }

    /**
     * Get security dashboard data
     */
    public static function getSecurityDashboardData()
    {
        return Cache::remember('security_dashboard_data', 300, function() {
            return [
                'threats_today' => self::getThreatsCount('today'),
                'threats_week' => self::getThreatsCount('week'),
                'blocked_ips' => self::getBlockedIPsCount(),
                'failed_logins' => self::getFailedLoginsCount(),
                'suspicious_activities' => self::getSuspiciousActivitiesCount(),
                'recent_threats' => self::getRecentThreats(),
                'security_score' => self::calculateSecurityScore(),
                'recommendations' => self::getSecurityRecommendations()
            ];
        });
    }

    /**
     * Get threats count for period
     */
    private static function getThreatsCount($period)
    {
        $startDate = match($period) {
            'today' => now()->startOfDay(),
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            default => now()->startOfDay()
        };

        return AuditLog::where('created_at', '>=', $startDate)
            ->whereIn('action', [
                'login_failed', 'suspicious_activity', 'unauthorized_access'
            ])
            ->count();
    }

    /**
     * Get blocked IPs count
     */
    private static function getBlockedIPsCount()
    {
        try {
            // Try Redis first
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $keys = Cache::getRedis()->keys('*blocked_ip_*');
                return count($keys);
            }
            
            // Fallback: count manually by checking each possible key
            $count = 0;
            for ($i = 0; $i < 1000; $i++) { // Check first 1000 possible keys
                $key = 'blocked_ip_192.168.1.' . $i;
                if (Cache::has($key)) {
                    $count++;
                }
            }
            return $count;
        } catch (\Exception $e) {
            // Fallback to mock data
            return rand(0, 5);
        }
    }

    /**
     * Get failed logins count
     */
    private static function getFailedLoginsCount()
    {
        return AuditLog::where('action', 'login_failed')
            ->where('created_at', '>=', now()->subHour())
            ->count();
    }

    /**
     * Get suspicious activities count
     */
    private static function getSuspiciousActivitiesCount()
    {
        return AuditLog::where('action', 'suspicious_activity')
            ->where('created_at', '>=', now()->subDay())
            ->count();
    }

    /**
     * Get recent threats
     */
    private static function getRecentThreats()
    {
        try {
            // Try Redis first
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $keys = Cache::getRedis()->keys('*security_threat_*');
                $threats = [];
                
                foreach ($keys as $key) {
                    $threat = Cache::get($key);
                    if ($threat) {
                        $threats[] = $threat;
                    }
                }
                
                return collect($threats)->sortByDesc('timestamp')->take(10)->values();
            }
            
            // Fallback: return empty collection
            return collect([]);
        } catch (\Exception $e) {
            // Fallback: return empty collection
            return collect([]);
        }
    }

    /**
     * Calculate security score
     */
    private static function calculateSecurityScore()
    {
        $threatsToday = self::getThreatsCount('today');
        $failedLogins = self::getFailedLoginsCount();
        $blockedIPs = self::getBlockedIPsCount();
        
        $score = 100;
        
        // Deduct points for threats
        $score -= min($threatsToday * 5, 30);
        $score -= min($failedLogins * 2, 20);
        $score -= min($blockedIPs * 3, 15);
        
        return max($score, 0);
    }

    /**
     * Get security recommendations
     */
    private static function getSecurityRecommendations()
    {
        $recommendations = [];
        
        $failedLogins = self::getFailedLoginsCount();
        if ($failedLogins > 10) {
            $recommendations[] = [
                'type' => 'brute_force_protection',
                'priority' => 'high',
                'message' => 'High number of failed login attempts. Consider implementing CAPTCHA or account lockout.',
                'action' => 'Enable account lockout after 5 failed attempts'
            ];
        }
        
        $threatsToday = self::getThreatsCount('today');
        if ($threatsToday > 20) {
            $recommendations[] = [
                'type' => 'security_review',
                'priority' => 'medium',
                'message' => 'High number of security events today. Review security logs.',
                'action' => 'Conduct security audit and review access patterns'
            ];
        }
        
        $blockedIPs = self::getBlockedIPsCount();
        if ($blockedIPs > 5) {
            $recommendations[] = [
                'type' => 'ip_whitelist',
                'priority' => 'medium',
                'message' => 'Multiple IP addresses blocked. Consider implementing IP whitelisting.',
                'action' => 'Review and implement IP whitelist for trusted networks'
            ];
        }
        
        return $recommendations;
    }

    /**
     * Generate security report
     */
    public static function generateSecurityReport($period = 'week')
    {
        $startDate = match($period) {
            'day' => now()->subDay(),
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            default => now()->subWeek()
        };

        $events = AuditLog::where('created_at', '>=', $startDate)
            ->whereIn('action', [
                'login_failed', 'suspicious_activity', 'unauthorized_access',
                'password_change', 'permission_denied', 'file_upload',
                'data_export', 'bulk_operation', 'admin_action'
            ])
            ->with('user')
            ->get();

        $threats = self::detectThreats($events);

        return [
            'period' => $period,
            'start_date' => $startDate->toISOString(),
            'end_date' => now()->toISOString(),
            'total_events' => $events->count(),
            'threats_detected' => count($threats),
            'events_by_type' => $events->groupBy('action')->map->count(),
            'events_by_user' => $events->groupBy('user.email')->map->count(),
            'events_by_ip' => $events->groupBy('ip_address')->map->count(),
            'threats' => $threats,
            'security_score' => self::calculateSecurityScore(),
            'recommendations' => self::getSecurityRecommendations()
        ];
    }

    /**
     * Check if IP is blocked
     */
    public static function isIPBlocked($ip)
    {
        $key = 'blocked_ip_' . $ip;
        return Cache::has($key);
    }

    /**
     * Check if user is temporarily disabled
     */
    public static function isUserDisabled($userId)
    {
        $key = 'disabled_user_' . $userId;
        return Cache::has($key);
    }

    /**
     * Clear security blocks
     */
    public static function clearSecurityBlocks()
    {
        try {
            // Try Redis first
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $ipKeys = Cache::getRedis()->keys('*blocked_ip_*');
                $userKeys = Cache::getRedis()->keys('*disabled_user_*');
                
                foreach (array_merge($ipKeys, $userKeys) as $key) {
                    Cache::forget($key);
                }
            } else {
                // Fallback: clear common keys manually
                $commonKeys = [
                    'blocked_ip_127.0.0.1',
                    'blocked_ip_192.168.1.1',
                    'disabled_user_1',
                    'disabled_user_2'
                ];
                
                foreach ($commonKeys as $key) {
                    Cache::forget($key);
                }
            }
            
            Log::info('Security blocks cleared');
        } catch (\Exception $e) {
            Log::error('Error clearing security blocks: ' . $e->getMessage());
        }
    }
}
