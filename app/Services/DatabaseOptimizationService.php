<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DatabaseOptimizationService
{
    /**
     * Analyze slow queries and provide recommendations
     */
    public static function analyzeSlowQueries($threshold = 100) // 100ms
    {
        $queries = DB::getQueryLog();
        $slowQueries = [];
        $recommendations = [];

        foreach ($queries as $query) {
            if ($query['time'] > $threshold) {
                $slowQueries[] = [
                    'sql' => $query['query'],
                    'bindings' => $query['bindings'],
                    'time' => $query['time'],
                    'recommendations' => self::getQueryRecommendations($query['query'])
                ];
            }
        }

        return [
            'slow_queries' => $slowQueries,
            'total_queries' => count($queries),
            'slow_count' => count($slowQueries),
            'average_time' => count($queries) > 0 ? array_sum(array_column($queries, 'time')) / count($queries) : 0
        ];
    }

    /**
     * Get query optimization recommendations
     */
    private static function getQueryRecommendations($sql)
    {
        $recommendations = [];
        $sql = strtolower($sql);

        // Check for missing indexes
        if (strpos($sql, 'where') !== false) {
            $recommendations[] = 'Consider adding indexes on WHERE clause columns';
        }

        if (strpos($sql, 'order by') !== false) {
            $recommendations[] = 'Consider adding indexes on ORDER BY columns';
        }

        if (strpos($sql, 'group by') !== false) {
            $recommendations[] = 'Consider adding indexes on GROUP BY columns';
        }

        if (strpos($sql, 'like') !== false && strpos($sql, '%') !== false) {
            $recommendations[] = 'LIKE queries with leading % are slow - consider full-text search';
        }

        if (strpos($sql, 'select *') !== false) {
            $recommendations[] = 'Avoid SELECT * - specify only needed columns';
        }

        if (strpos($sql, 'join') !== false) {
            $recommendations[] = 'Ensure JOIN columns are indexed';
        }

        return $recommendations;
    }

    /**
     * Get database performance metrics
     */
    public static function getDatabaseMetrics()
    {
        return Cache::remember('database_metrics', 300, function() {
            try {
                $startTime = microtime(true);
                
                // Test basic query performance
                $testQuery = DB::select('SELECT 1 as test');
                $queryTime = (microtime(true) - $startTime) * 1000;

                // Get table statistics
                $tables = self::getTableStatistics();
                
                // Get index usage
                $indexUsage = self::getIndexUsage();
                
                // Get connection info
                $connections = DB::getConnections();

                return [
                    'query_performance' => [
                        'test_query_time' => round($queryTime, 2),
                        'status' => $queryTime < 50 ? 'excellent' : ($queryTime < 100 ? 'good' : 'needs_optimization')
                    ],
                    'tables' => $tables,
                    'indexes' => $indexUsage,
                    'connections' => count($connections),
                    'cache_hit_ratio' => self::getCacheHitRatio(),
                    'recommendations' => self::getDatabaseRecommendations($tables, $indexUsage)
                ];
            } catch (\Exception $e) {
                Log::error('Database metrics collection failed', ['error' => $e->getMessage()]);
                return ['error' => $e->getMessage()];
            }
        });
    }

    /**
     * Get table statistics
     */
    private static function getTableStatistics()
    {
        try {
            $tables = [
                'assets', 'users', 'departments', 'vendors', 'asset_categories',
                'computers', 'monitors', 'printers', 'peripherals', 'maintenance',
                'disposals', 'asset_assignments', 'asset_assignment_confirmations',
                'audit_logs', 'notifications'
            ];

            $stats = [];
            foreach ($tables as $table) {
                try {
                    $count = DB::table($table)->count();
                    $stats[$table] = [
                        'rows' => $count,
                        'size' => self::getTableSize($table),
                        'indexes' => self::getTableIndexes($table)
                    ];
                } catch (\Exception $e) {
                    $stats[$table] = ['error' => 'Table not found or inaccessible'];
                }
            }

            return $stats;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get table size (mock implementation)
     */
    private static function getTableSize($table)
    {
        // In a real implementation, you would query information_schema
        // For now, return mock data
        return rand(1024, 10240) . ' KB';
    }

    /**
     * Get table indexes
     */
    private static function getTableIndexes($table)
    {
        try {
            // This would typically query information_schema.tables
            // For now, return mock data
            return rand(2, 8);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get index usage statistics
     */
    private static function getIndexUsage()
    {
        // Mock implementation - in production, query MySQL's performance_schema
        return [
            'total_indexes' => rand(50, 200),
            'used_indexes' => rand(30, 150),
            'unused_indexes' => rand(5, 20),
            'duplicate_indexes' => rand(0, 5)
        ];
    }

    /**
     * Get cache hit ratio
     */
    private static function getCacheHitRatio()
    {
        $cacheStats = CacheService::getStats();
        $total = $cacheStats['hits'] + $cacheStats['misses'];
        
        if ($total === 0) {
            return 0;
        }

        return round(($cacheStats['hits'] / $total) * 100, 2);
    }

    /**
     * Get database optimization recommendations
     */
    private static function getDatabaseRecommendations($tables, $indexUsage)
    {
        $recommendations = [];

        // Check for large tables
        foreach ($tables as $table => $stats) {
            if (isset($stats['rows']) && $stats['rows'] > 10000) {
                $recommendations[] = [
                    'type' => 'table_size',
                    'priority' => 'medium',
                    'message' => "Table '{$table}' has {$stats['rows']} rows - consider partitioning",
                    'action' => 'Implement table partitioning for better performance'
                ];
            }
        }

        // Check index usage
        if ($indexUsage['unused_indexes'] > 10) {
            $recommendations[] = [
                'type' => 'indexes',
                'priority' => 'high',
                'message' => "{$indexUsage['unused_indexes']} unused indexes found",
                'action' => 'Remove unused indexes to improve write performance'
            ];
        }

        if ($indexUsage['duplicate_indexes'] > 0) {
            $recommendations[] = [
                'type' => 'indexes',
                'priority' => 'medium',
                'message' => "{$indexUsage['duplicate_indexes']} duplicate indexes found",
                'action' => 'Remove duplicate indexes to save storage space'
            ];
        }

        // Check cache performance
        $cacheHitRatio = self::getCacheHitRatio();
        if ($cacheHitRatio < 80) {
            $recommendations[] = [
                'type' => 'caching',
                'priority' => 'medium',
                'message' => "Cache hit ratio is {$cacheHitRatio}% - should be above 80%",
                'action' => 'Review cache strategy and increase cache duration'
            ];
        }

        return $recommendations;
    }

    /**
     * Optimize specific queries
     */
    public static function optimizeQuery($query, $bindings = [])
    {
        $startTime = microtime(true);
        
        try {
            $result = DB::select($query, $bindings);
            $executionTime = (microtime(true) - $startTime) * 1000;
            
            return [
                'success' => true,
                'execution_time' => round($executionTime, 2),
                'result_count' => count($result),
                'recommendations' => self::getQueryRecommendations($query)
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'execution_time' => (microtime(true) - $startTime) * 1000
            ];
        }
    }

    /**
     * Get database health score
     */
    public static function getDatabaseHealthScore()
    {
        $metrics = self::getDatabaseMetrics();
        
        if (isset($metrics['error'])) {
            return 0;
        }

        $score = 100;
        
        // Deduct points for slow queries
        if ($metrics['query_performance']['test_query_time'] > 100) {
            $score -= 20;
        } elseif ($metrics['query_performance']['test_query_time'] > 50) {
            $score -= 10;
        }

        // Deduct points for low cache hit ratio
        if ($metrics['cache_hit_ratio'] < 70) {
            $score -= 15;
        } elseif ($metrics['cache_hit_ratio'] < 80) {
            $score -= 10;
        }

        // Deduct points for recommendations
        $recommendationCount = count($metrics['recommendations']);
        $score -= min($recommendationCount * 5, 30);

        return max($score, 0);
    }

    /**
     * Generate database optimization report
     */
    public static function generateOptimizationReport()
    {
        $metrics = self::getDatabaseMetrics();
        $slowQueries = self::analyzeSlowQueries();
        
        return [
            'timestamp' => Carbon::now(),
            'health_score' => self::getDatabaseHealthScore(),
            'metrics' => $metrics,
            'slow_queries' => $slowQueries,
            'summary' => [
                'total_tables' => count($metrics['tables'] ?? []),
                'total_queries' => $slowQueries['total_queries'],
                'slow_queries' => $slowQueries['slow_count'],
                'average_query_time' => $slowQueries['average_time']
            ]
        ];
    }
}
