<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Services\AuditService;
use App\Services\SecurityService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SecurityAuditController extends Controller
{
    /**
     * Display security dashboard
     */
    public function index()
    {
        $this->authorize('view_security_audit');
        
        // Get security statistics
        $stats = $this->getSecurityStats();
        
        // Get recent security events
        $recentEvents = AuditLog::securityEvents()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
        
        // Get failed login attempts
        $failedLogins = AuditLog::where('action', 'failed_login_attempt')
            ->where('created_at', '>=', now()->subDays(7))
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('security.audit.index', compact('stats', 'recentEvents', 'failedLogins'));
    }
    
    /**
     * Get audit logs with filters
     */
    public function logs(Request $request)
    {
        $this->authorize('view_security_audit');
        
        $query = AuditLog::with('user');
        
        // Apply filters
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', Carbon::parse($request->date_from));
        }
        
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', Carbon::parse($request->date_to));
        }
        
        if ($request->filled('ip_address')) {
            $query->where('ip_address', 'like', '%' . $request->ip_address . '%');
        }
        
        $logs = $query->orderBy('created_at', 'desc')->paginate(50);
        
        return view('security.audit.logs', compact('logs'));
    }
    
    /**
     * Get user activity
     */
    public function userActivity($userId)
    {
        $this->authorize('view_security_audit');
        
        $user = \App\Models\User::findOrFail($userId);
        $activities = AuditService::getUserActivity($userId, 30);
        
        return view('security.audit.user-activity', compact('user', 'activities'));
    }
    
    /**
     * Get security events
     */
    public function securityEvents()
    {
        $this->authorize('view_security_audit');
        
        $events = AuditService::getSecurityEvents(30);
        
        return view('security.audit.security-events', compact('events'));
    }
    
    /**
     * Export audit logs
     */
    public function export(Request $request)
    {
        $this->authorize('export_security_audit');
        
        $query = AuditLog::with('user');
        
        // Apply same filters as logs method
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', Carbon::parse($request->date_from));
        }
        
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', Carbon::parse($request->date_to));
        }
        
        $logs = $query->orderBy('created_at', 'desc')->get();
        
        // Log export action
        AuditService::logDataTransfer('export', 'audit_logs', $logs->count(), [
            'filters' => $request->all()
        ]);
        
        // Generate CSV
        $filename = 'audit_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID', 'User', 'Action', 'Model Type', 'Model ID', 
                'IP Address', 'User Agent', 'Details', 'Timestamp', 'Created At'
            ]);
            
            // CSV data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user ? $log->user->name : 'System',
                    $log->action,
                    $log->model_type,
                    $log->model_id,
                    $log->ip_address,
                    $log->user_agent,
                    json_encode($log->details),
                    $log->timestamp,
                    $log->created_at
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Get security statistics
     */
    private function getSecurityStats()
    {
        $stats = [];
        
        // Total events in last 30 days
        $stats['total_events'] = AuditLog::where('created_at', '>=', now()->subDays(30))->count();
        
        // Failed login attempts
        $stats['failed_logins'] = AuditLog::where('action', 'failed_login_attempt')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        
        // Suspicious activities
        $stats['suspicious_activities'] = AuditLog::where('action', 'suspicious_activity')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        
        // Rate limit exceeded
        $stats['rate_limit_exceeded'] = AuditLog::where('action', 'rate_limit_exceeded')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        
        // Data changes
        $stats['data_changes'] = AuditLog::dataChanges()
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        
        // File operations
        $stats['file_operations'] = AuditLog::fileOperations()
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        
        // Most active users
        $stats['most_active_users'] = AuditLog::where('created_at', '>=', now()->subDays(7))
            ->select('user_id', DB::raw('count(*) as activity_count'))
            ->groupBy('user_id')
            ->with('user')
            ->orderBy('activity_count', 'desc')
            ->limit(5)
            ->get();
        
        // Most common actions
        $stats['most_common_actions'] = AuditLog::where('created_at', '>=', now()->subDays(7))
            ->select('action', DB::raw('count(*) as action_count'))
            ->groupBy('action')
            ->orderBy('action_count', 'desc')
            ->limit(10)
            ->get();
        
        return $stats;
    }
    
    /**
     * Get audit trail for specific model
     */
    public function modelAuditTrail($modelType, $modelId)
    {
        $this->authorize('view_security_audit');
        
        $auditTrail = AuditService::getAuditTrail($modelType, $modelId);
        
        return response()->json($auditTrail);
    }
    
    /**
     * Clear old audit logs
     */
    public function clearOldLogs()
    {
        $this->authorize('manage_security_audit');
        
        $retentionDays = config('security.audit_logging.retention_days', 365);
        $cutoffDate = now()->subDays($retentionDays);
        
        $deletedCount = AuditLog::where('created_at', '<', $cutoffDate)->delete();
        
        // Log the cleanup action
        AuditService::logSystemEvent('audit_logs_cleaned', [
            'deleted_count' => $deletedCount,
            'retention_days' => $retentionDays,
            'cutoff_date' => $cutoffDate
        ]);
        
        return response()->json([
            'message' => "Cleared {$deletedCount} old audit logs.",
            'deleted_count' => $deletedCount
        ]);
    }
    
    /**
     * Get badge color for action type
     */
    public static function getActionBadgeColor($action)
    {
        $colors = [
            'auth_login' => 'success',
            'auth_logout' => 'info',
            'failed_login_attempt' => 'danger',
            'suspicious_activity' => 'warning',
            'rate_limit_exceeded' => 'warning',
            'created' => 'primary',
            'updated' => 'warning',
            'deleted' => 'danger',
            'file_upload' => 'info',
            'file_download' => 'info'
        ];
        
        return $colors[$action] ?? 'secondary';
    }

    /**
     * Get event row CSS class based on action
     */
    public function getEventRowClass($action)
    {
        $classes = [
            'failed_login_attempt' => 'table-danger',
            'suspicious_activity' => 'table-warning',
            'rate_limit_exceeded' => 'table-info',
            'auth_login' => 'table-success',
            'auth_logout' => 'table-light'
        ];
        
        return $classes[$action] ?? '';
    }

    /**
     * Get location from IP address
     */
    public function getLocationFromIP($ip)
    {
        // Simple location detection based on IP ranges
        if (strpos($ip, '192.168.') === 0 || strpos($ip, '10.') === 0 || strpos($ip, '172.') === 0) {
            return 'Local Network';
        }
        
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return 'Localhost';
        }
        
        return 'External';
    }

    /**
     * Get severity level for an action
     */
    public function getSeverityLevel($action)
    {
        $severities = [
            'failed_login_attempt' => 'High',
            'suspicious_activity' => 'High',
            'rate_limit_exceeded' => 'Medium',
            'auth_login' => 'Low',
            'auth_logout' => 'Low'
        ];
        
        return $severities[$action] ?? 'Medium';
    }

    /**
     * Get severity badge color for an action
     */
    public function getSeverityBadgeColor($action)
    {
        $colors = [
            'failed_login_attempt' => 'danger',
            'suspicious_activity' => 'danger',
            'rate_limit_exceeded' => 'warning',
            'auth_login' => 'success',
            'auth_logout' => 'info'
        ];
        
        return $colors[$action] ?? 'secondary';
    }
}
