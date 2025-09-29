<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SecurityMonitoringService;
use App\Services\AuditService;
use App\Models\AuditLog;
use Carbon\Carbon;

class SecurityMonitoringController extends Controller
{
    /**
     * Security monitoring dashboard
     */
    public function index()
    {
        $this->authorize('view_security_monitoring');
        
        $dashboardData = SecurityMonitoringService::getSecurityDashboardData();
        
        return view('security.monitoring.index', compact('dashboardData'));
    }

    /**
     * Get security threats
     */
    public function threats(Request $request)
    {
        try {
            $this->authorize('view_security_monitoring');
            
            // Simple test first
            $threats = collect([
                [
                    'id' => 1,
                    'type' => 'test_threat',
                    'severity' => 'low',
                    'message' => 'Test threat for debugging',
                    'count' => 1,
                    'ip_address' => '127.0.0.1',
                    'timestamp' => now()->toISOString(),
                    'user_agent' => 'Test Agent'
                ]
            ]);
            
            return response()->json([
                'threats' => $threats,
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            \Log::error('Security Monitoring Threats Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'threats' => [],
                'error' => 'Failed to load threats: ' . $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Get security events
     */
    public function events(Request $request)
    {
        $query = AuditLog::with('user')
            ->whereIn('action', [
                'login_failed', 'suspicious_activity', 'unauthorized_access',
                'password_change', 'permission_denied', 'file_upload',
                'data_export', 'bulk_operation', 'admin_action'
            ]);

        // Apply filters
        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('ip_address')) {
            $query->where('ip_address', 'like', '%' . $request->ip_address . '%');
        }

        if ($request->has('date_from')) {
            $query->where('created_at', '>=', Carbon::parse($request->date_from));
        }

        if ($request->has('date_to')) {
            $query->where('created_at', '<=', Carbon::parse($request->date_to));
        }

        $events = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'events' => $events,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Get security statistics
     */
    public function statistics(Request $request)
    {
        $period = $request->get('period', 'week');
        
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
            ->get();

        $statistics = [
            'total_events' => $events->count(),
            'events_by_type' => $events->groupBy('action')->map->count(),
            'events_by_day' => $events->groupBy(function($event) {
                return $event->created_at->format('Y-m-d');
            })->map->count(),
            'top_users' => $events->groupBy('user.email')->map->count()->sortDesc()->take(10),
            'top_ips' => $events->groupBy('ip_address')->map->count()->sortDesc()->take(10),
            'threats_detected' => SecurityMonitoringService::getThreatsCount($period),
            'security_score' => SecurityMonitoringService::calculateSecurityScore()
        ];

        return response()->json([
            'statistics' => $statistics,
            'period' => $period,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Generate security report
     */
    public function report(Request $request)
    {
        $period = $request->get('period', 'week');
        $format = $request->get('format', 'json');
        
        $report = SecurityMonitoringService::generateSecurityReport($period);
        
        if ($format === 'csv') {
            return $this->exportSecurityReport($report);
        }
        
        return response()->json([
            'report' => $report,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Export security report as CSV
     */
    private function exportSecurityReport($report)
    {
        $filename = 'security_report_' . $report['period'] . '_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($report) {
            $file = fopen('php://output', 'w');
            
            // Write CSV headers
            fputcsv($file, ['Security Report', 'Generated: ' . now()->format('Y-m-d H:i:s')]);
            fputcsv($file, ['Period: ' . $report['start_date'] . ' to ' . $report['end_date']]);
            fputcsv($file, ['']);
            
            // Summary
            fputcsv($file, ['SUMMARY']);
            fputcsv($file, ['Metric', 'Value']);
            fputcsv($file, ['Total Events', $report['total_events']]);
            fputcsv($file, ['Threats Detected', $report['threats_detected']]);
            fputcsv($file, ['Security Score', $report['security_score']]);
            fputcsv($file, ['']);
            
            // Events by type
            fputcsv($file, ['EVENTS BY TYPE']);
            fputcsv($file, ['Event Type', 'Count']);
            foreach ($report['events_by_type'] as $type => $count) {
                fputcsv($file, [$type, $count]);
            }
            fputcsv($file, ['']);
            
            // Events by user
            fputcsv($file, ['EVENTS BY USER']);
            fputcsv($file, ['User Email', 'Count']);
            foreach ($report['events_by_user'] as $email => $count) {
                fputcsv($file, [$email, $count]);
            }
            fputcsv($file, ['']);
            
            // Events by IP
            fputcsv($file, ['EVENTS BY IP ADDRESS']);
            fputcsv($file, ['IP Address', 'Count']);
            foreach ($report['events_by_ip'] as $ip => $count) {
                fputcsv($file, [$ip, $count]);
            }
            fputcsv($file, ['']);
            
            // Threats
            fputcsv($file, ['DETECTED THREATS']);
            fputcsv($file, ['Type', 'Severity', 'Description', 'Count']);
            foreach ($report['threats'] as $threat) {
                fputcsv($file, [
                    $threat['type'],
                    $threat['severity'],
                    $threat['description'],
                    $threat['count']
                ]);
            }
            fputcsv($file, ['']);
            
            // Recommendations
            fputcsv($file, ['SECURITY RECOMMENDATIONS']);
            fputcsv($file, ['Type', 'Priority', 'Message', 'Action']);
            foreach ($report['recommendations'] as $recommendation) {
                fputcsv($file, [
                    $recommendation['type'],
                    $recommendation['priority'],
                    $recommendation['message'],
                    $recommendation['action']
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Clear security blocks
     */
    public function clearBlocks()
    {
        SecurityMonitoringService::clearSecurityBlocks();
        
        return response()->json([
            'message' => 'Security blocks cleared successfully',
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Check IP status
     */
    public function checkIP(Request $request)
    {
        $ip = $request->get('ip');
        
        if (!$ip) {
            return response()->json([
                'error' => 'IP address is required'
            ], 400);
        }
        
        $isBlocked = SecurityMonitoringService::isIPBlocked($ip);
        
        return response()->json([
            'ip' => $ip,
            'blocked' => $isBlocked,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Check user status
     */
    public function checkUser(Request $request)
    {
        $userId = $request->get('user_id');
        
        if (!$userId) {
            return response()->json([
                'error' => 'User ID is required'
            ], 400);
        }
        
        $isDisabled = SecurityMonitoringService::isUserDisabled($userId);
        
        return response()->json([
            'user_id' => $userId,
            'disabled' => $isDisabled,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Run security monitoring
     */
    public function runMonitoring()
    {
        $result = SecurityMonitoringService::monitorSecurityEvents();
        
        return response()->json([
            'result' => $result,
            'timestamp' => now()->toISOString()
        ]);
    }
}
