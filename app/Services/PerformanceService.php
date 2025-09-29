<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PerformanceService
{
    /**
     * Performance metrics storage
     */
    private static $metrics = [];

    /**
     * Start performance monitoring
     */
    public static function startTimer($name)
    {
        self::$metrics[$name] = [
            'start_time' => microtime(true),
            'start_memory' => memory_get_usage(true),
            'queries_start' => DB::getQueryLog()
        ];
    }

    /**
     * End performance monitoring
     */
    public static function endTimer($name)
    {
        if (!isset(self::$metrics[$name])) {
            return null;
        }

        $metric = self::$metrics[$name];
        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);

        $result = [
            'name' => $name,
            'execution_time' => round(($endTime - $metric['start_time']) * 1000, 2), // milliseconds
            'memory_usage' => $endMemory - $metric['start_memory'],
            'memory_peak' => memory_get_peak_usage(true),
            'queries_count' => count(DB::getQueryLog()) - count($metric['queries_start']),
            'queries' => array_slice(DB::getQueryLog(), count($metric['queries_start'])),
            'timestamp' => Carbon::now()
        ];

        // Log slow queries
        if ($result['execution_time'] > 1000) { // > 1 second
            Log::warning("Slow operation detected: {$name}", $result);
        }

        // Log high memory usage
        if ($result['memory_usage'] > 50 * 1024 * 1024) { // > 50MB
            Log::warning("High memory usage detected: {$name}", $result);
        }

        unset(self::$metrics[$name]);
        return $result;
    }

    /**
     * Get system performance metrics
     */
    public static function getSystemMetrics()
    {
        return [
            'memory' => [
                'current' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true),
                'limit' => ini_get('memory_limit'),
                'usage_percentage' => round((memory_get_usage(true) / self::convertToBytes(ini_get('memory_limit'))) * 100, 2)
            ],
            'database' => [
                'connections' => count(DB::getConnections()),
                'queries_count' => count(DB::getQueryLog()),
                'slow_queries' => self::getSlowQueries()
            ],
            'cache' => [
                'driver' => config('cache.default'),
                'stats' => CacheService::getStats(),
                'memory' => CacheService::getMemoryUsage()
            ],
            'server' => [
                'load_average' => self::getLoadAverage(),
                'disk_free' => self::getDiskFreeSpace(),
                'disk_total' => self::getDiskTotalSpace(),
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version()
            ]
        ];
    }

    /**
     * Get slow queries from current session
     */
    public static function getSlowQueries($threshold = 100) // 100ms
    {
        $queries = DB::getQueryLog();
        $slowQueries = [];

        foreach ($queries as $query) {
            if ($query['time'] > $threshold) {
                $slowQueries[] = [
                    'sql' => $query['query'],
                    'bindings' => $query['bindings'],
                    'time' => $query['time']
                ];
            }
        }

        return $slowQueries;
    }

    /**
     * Monitor database performance
     */
    public static function monitorDatabase()
    {
        $startTime = microtime(true);
        $startQueries = count(DB::getQueryLog());

        return function() use ($startTime, $startQueries) {
            $endTime = microtime(true);
            $endQueries = count(DB::getQueryLog());

            return [
                'execution_time' => round(($endTime - $startTime) * 1000, 2),
                'queries_executed' => $endQueries - $startQueries,
                'queries_per_second' => $endQueries - $startQueries > 0 ? 
                    round(($endQueries - $startQueries) / (($endTime - $startTime) / 1000), 2) : 0
            ];
        };
    }

    /**
     * Get performance recommendations
     */
    public static function getRecommendations()
    {
        $recommendations = [];
        $metrics = self::getSystemMetrics();

        // Memory recommendations
        if ($metrics['memory']['usage_percentage'] > 80) {
            $recommendations[] = [
                'type' => 'memory',
                'priority' => 'high',
                'message' => 'Memory usage is high (' . $metrics['memory']['usage_percentage'] . '%). Consider optimizing queries or increasing memory limit.',
                'action' => 'Review memory-intensive operations and consider query optimization.'
            ];
        }

        // Database recommendations
        if ($metrics['database']['queries_count'] > 100) {
            $recommendations[] = [
                'type' => 'database',
                'priority' => 'medium',
                'message' => 'High number of database queries (' . $metrics['database']['queries_count'] . '). Consider eager loading.',
                'action' => 'Review queries and implement eager loading where possible.'
            ];
        }

        // Slow queries recommendations
        $slowQueries = self::getSlowQueries();
        if (count($slowQueries) > 0) {
            $recommendations[] = [
                'type' => 'database',
                'priority' => 'high',
                'message' => count($slowQueries) . ' slow queries detected.',
                'action' => 'Review and optimize slow queries, consider adding database indexes.'
            ];
        }

        // Cache recommendations
        $cacheStats = $metrics['cache']['stats'];
        if ($cacheStats['misses'] > $cacheStats['hits']) {
            $recommendations[] = [
                'type' => 'cache',
                'priority' => 'medium',
                'message' => 'Cache miss rate is high. Consider improving cache strategy.',
                'action' => 'Review cache keys and implement more effective caching.'
            ];
        }

        return $recommendations;
    }

    /**
     * Log performance metrics
     */
    public static function logMetrics($operation, $metrics)
    {
        Log::info("Performance metrics for {$operation}", [
            'operation' => $operation,
            'execution_time' => $metrics['execution_time'] ?? 0,
            'memory_usage' => $metrics['memory_usage'] ?? 0,
            'queries_count' => $metrics['queries_count'] ?? 0,
            'timestamp' => Carbon::now()
        ]);
    }

    /**
     * Get performance dashboard data
     */
    public static function getDashboardData()
    {
        return [
            'system' => self::getSystemMetrics(),
            'recommendations' => self::getRecommendations(),
            'recent_operations' => self::getRecentOperations(),
            'performance_trends' => self::getPerformanceTrends()
        ];
    }

    /**
     * Get recent operations (from cache)
     */
    private static function getRecentOperations()
    {
        return Cache::remember('recent_operations', 300, function() {
            return [
                'last_hour' => self::getOperationsInPeriod(60),
                'last_day' => self::getOperationsInPeriod(1440),
                'last_week' => self::getOperationsInPeriod(10080)
            ];
        });
    }

    /**
     * Get operations in specific period (in minutes)
     */
    private static function getOperationsInPeriod($minutes)
    {
        // This would typically query a performance_logs table
        // For now, return mock data
        return [
            'total_operations' => rand(10, 100),
            'average_time' => rand(50, 500),
            'slow_operations' => rand(0, 5)
        ];
    }

    /**
     * Get performance trends
     */
    private static function getPerformanceTrends()
    {
        return Cache::remember('performance_trends', 1800, function() {
            $trends = [];
            $days = 7;
            
            for ($i = $days; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $trends[] = [
                    'date' => $date->format('Y-m-d'),
                    'avg_response_time' => rand(100, 500),
                    'total_requests' => rand(1000, 5000),
                    'error_rate' => rand(0, 5) / 100
                ];
            }
            
            return $trends;
        });
    }

    /**
     * Get load average (Windows compatible)
     */
    private static function getLoadAverage()
    {
        if (function_exists('sys_getloadavg')) {
            return sys_getloadavg();
        }
        
        // For Windows systems, use mock data or simple calculation
        if (PHP_OS_FAMILY === 'Windows') {
            // Simple mock data for Windows - in production you might want to
            // implement a more sophisticated CPU monitoring solution
            return [0.5, 0.6, 0.7];
        }
        
        // Fallback for other systems
        return [0.0, 0.0, 0.0];
    }

    /**
     * Get disk free space (Windows compatible)
     */
    private static function getDiskFreeSpace()
    {
        $path = PHP_OS_FAMILY === 'Windows' ? 'C:\\' : '/';
        $free = disk_free_space($path);
        return $free ?: 0;
    }

    /**
     * Get disk total space (Windows compatible)
     */
    private static function getDiskTotalSpace()
    {
        $path = PHP_OS_FAMILY === 'Windows' ? 'C:\\' : '/';
        $total = disk_total_space($path);
        return $total ?: 0;
    }

    /**
     * Convert memory limit to bytes
     */
    private static function convertToBytes($memoryLimit)
    {
        $memoryLimit = trim($memoryLimit);
        $last = strtolower($memoryLimit[strlen($memoryLimit) - 1]);
        $memoryLimit = (int) $memoryLimit;

        switch ($last) {
            case 'g':
                $memoryLimit *= 1024;
            case 'm':
                $memoryLimit *= 1024;
            case 'k':
                $memoryLimit *= 1024;
        }

        return $memoryLimit;
    }
}
