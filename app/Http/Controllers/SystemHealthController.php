<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PerformanceService;
use App\Services\CacheService;
use App\Services\ErrorHandlingService;
use App\Services\DatabaseOptimizationService;
use App\Services\LoadBalancingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SystemHealthController extends Controller
{
    /**
     * Display system health dashboard
     */
    public function index()
    {
        $this->authorize('view_system_health');
        
        $healthData = $this->getSystemHealthData();
        
        return view('system.health', compact('healthData'));
    }

    /**
     * Get system health data
     */
    private function getSystemHealthData()
    {
        return [
            'performance' => PerformanceService::getSystemMetrics(),
            'cache' => CacheService::getMemoryUsage(),
            'errors' => ErrorHandlingService::getErrorStatistics(7),
            'recommendations' => PerformanceService::getRecommendations(),
            'database' => $this->getDatabaseHealth(),
            'database_optimization' => DatabaseOptimizationService::getDatabaseMetrics(),
            'load_balancing' => LoadBalancingService::getLoadBalancerMetrics(),
            'system_status' => $this->getSystemStatus()
        ];
    }

    /**
     * Get database health metrics
     */
    private function getDatabaseHealth()
    {
        try {
            $startTime = microtime(true);
            $queryCount = count(DB::getQueryLog());
            
            // Test database connection
            DB::select('SELECT 1');
            
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            
            return [
                'status' => 'healthy',
                'response_time' => $responseTime,
                'queries_count' => $queryCount,
                'connections' => count(DB::getConnections()),
                'slow_queries' => PerformanceService::getSlowQueries()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'response_time' => null,
                'queries_count' => 0
            ];
        }
    }

    /**
     * Get overall system status
     */
    private function getSystemStatus()
    {
        $metrics = PerformanceService::getSystemMetrics();
        $status = 'healthy';
        $issues = [];

        // Check memory usage
        if ($metrics['memory']['usage_percentage'] > 90) {
            $status = 'critical';
            $issues[] = 'Memory usage critically high';
        } elseif ($metrics['memory']['usage_percentage'] > 80) {
            $status = 'warning';
            $issues[] = 'Memory usage high';
        }

        // Check slow queries
        $slowQueries = PerformanceService::getSlowQueries();
        if (count($slowQueries) > 10) {
            $status = $status === 'critical' ? 'critical' : 'warning';
            $issues[] = 'Too many slow queries';
        }

        // Check error rate
        $errorStats = ErrorHandlingService::getErrorStatistics(1);
        $todayErrors = $errorStats[0]['total_errors'] ?? 0;
        if ($todayErrors > 50) {
            $status = $status === 'critical' ? 'critical' : 'warning';
            $issues[] = 'High error rate detected';
        }

        return [
            'status' => $status,
            'issues' => $issues,
            'last_checked' => now()
        ];
    }

    /**
     * Get performance metrics API
     */
    public function metrics()
    {
        $this->authorize('view_system_health');
        
        return response()->json([
            'performance' => PerformanceService::getSystemMetrics(),
            'cache' => CacheService::getMemoryUsage(),
            'errors' => ErrorHandlingService::getErrorStatistics(1),
            'timestamp' => now()
        ]);
    }

    /**
     * Clear cache
     */
    public function clearCache()
    {
        $this->authorize('manage_system_health');
        
        try {
            CacheService::clearAll();
            return response()->json(['success' => true, 'message' => 'Cache cleared successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Warm up cache
     */
    public function warmUpCache()
    {
        $this->authorize('manage_system_health');
        
        try {
            CacheService::warmUp();
            return response()->json(['success' => true, 'message' => 'Cache warmed up successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
