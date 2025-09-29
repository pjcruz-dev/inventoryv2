<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LoadBalancingService;
use App\Services\PerformanceService;
use App\Services\DatabaseOptimizationService;

class HealthCheckController extends Controller
{
    /**
     * Basic health check endpoint
     */
    public function index()
    {
        $healthData = LoadBalancingService::getHealthCheckData();
        
        $statusCode = $healthData['status'] === 'healthy' ? 200 : 503;
        
        return response()->json($healthData, $statusCode);
    }

    /**
     * Detailed health check with metrics
     */
    public function detailed()
    {
        $healthData = LoadBalancingService::getHealthCheckData();
        $performanceMetrics = PerformanceService::getSystemMetrics();
        $databaseMetrics = DatabaseOptimizationService::getDatabaseMetrics();
        $loadBalancerMetrics = LoadBalancingService::getLoadBalancerMetrics();
        
        $detailedHealth = array_merge($healthData, [
            'performance' => $performanceMetrics,
            'database' => $databaseMetrics,
            'load_balancer' => $loadBalancerMetrics,
            'timestamp' => now()->toISOString()
        ]);
        
        $statusCode = $healthData['status'] === 'healthy' ? 200 : 503;
        
        return response()->json($detailedHealth, $statusCode);
    }

    /**
     * Readiness probe for Kubernetes
     */
    public function readiness()
    {
        try {
            // Check if application is ready to serve traffic
            $dbHealthy = $this->checkDatabase();
            $cacheHealthy = $this->checkCache();
            $storageHealthy = $this->checkStorage();
            
            $ready = $dbHealthy && $cacheHealthy && $storageHealthy;
            
            return response()->json([
                'ready' => $ready,
                'checks' => [
                    'database' => $dbHealthy,
                    'cache' => $cacheHealthy,
                    'storage' => $storageHealthy
                ],
                'timestamp' => now()->toISOString()
            ], $ready ? 200 : 503);
            
        } catch (\Exception $e) {
            return response()->json([
                'ready' => false,
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 503);
        }
    }

    /**
     * Liveness probe for Kubernetes
     */
    public function liveness()
    {
        try {
            // Check if application is alive (basic checks)
            $memoryUsage = memory_get_usage(true);
            $memoryLimit = $this->getMemoryLimit();
            $memoryHealthy = $memoryUsage < ($memoryLimit * 0.9); // Less than 90% memory usage
            
            $alive = $memoryHealthy;
            
            return response()->json([
                'alive' => $alive,
                'memory_usage' => $memoryUsage,
                'memory_limit' => $memoryLimit,
                'memory_percentage' => round(($memoryUsage / $memoryLimit) * 100, 2),
                'timestamp' => now()->toISOString()
            ], $alive ? 200 : 503);
            
        } catch (\Exception $e) {
            return response()->json([
                'alive' => false,
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 503);
        }
    }

    /**
     * Load balancer metrics endpoint
     */
    public function metrics()
    {
        $metrics = LoadBalancingService::getLoadBalancerMetrics();
        
        return response()->json([
            'metrics' => $metrics,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Check database connectivity
     */
    private function checkDatabase()
    {
        try {
            \DB::select('SELECT 1');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check cache connectivity
     */
    private function checkCache()
    {
        try {
            \Cache::put('health_check', 'ok', 60);
            return \Cache::get('health_check') === 'ok';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check storage accessibility
     */
    private function checkStorage()
    {
        try {
            $path = storage_path('app');
            return is_writable($path) && is_readable($path);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get memory limit in bytes
     */
    private function getMemoryLimit()
    {
        $limit = ini_get('memory_limit');
        
        if ($limit === '-1') {
            return PHP_INT_MAX;
        }
        
        $unit = strtolower(substr($limit, -1));
        $value = (int) $limit;
        
        switch ($unit) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }
        
        return $value;
    }
}
