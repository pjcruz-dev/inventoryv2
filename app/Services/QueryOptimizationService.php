<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;

class QueryOptimizationService
{
    /**
     * Optimize asset queries with proper eager loading
     */
    public static function optimizeAssetQueries()
    {
        return Cache::remember('optimized_asset_queries', 3600, function() {
            return [
                'with_relationships' => [
                    'category',
                    'vendor', 
                    'assignedUser.department',
                    'department',
                    'maintenances' => function($query) {
                        $query->latest()->limit(5);
                    }
                ],
                'select_columns' => [
                    'id', 'asset_tag', 'name', 'status', 'cost', 'purchase_date',
                    'category_id', 'vendor_id', 'assigned_to', 'department_id',
                    'created_at', 'updated_at'
                ],
                'common_filters' => [
                    'status' => ['active', 'inactive', 'disposed'],
                    'category_id' => 'integer',
                    'vendor_id' => 'integer',
                    'assigned_to' => 'integer',
                    'department_id' => 'integer'
                ]
            ];
        });
    }

    /**
     * Optimize user queries
     */
    public static function optimizeUserQueries()
    {
        return Cache::remember('optimized_user_queries', 3600, function() {
            return [
                'with_relationships' => [
                    'department',
                    'role',
                    'auditLogs' => function($query) {
                        $query->latest()->limit(10);
                    }
                ],
                'select_columns' => [
                    'id', 'name', 'email', 'status', 'department_id', 'role_id',
                    'created_at', 'updated_at', 'last_login_at'
                ]
            ];
        });
    }

    /**
     * Optimize maintenance queries
     */
    public static function optimizeMaintenanceQueries()
    {
        return Cache::remember('optimized_maintenance_queries', 3600, function() {
            return [
                'with_relationships' => [
                    'asset.category',
                    'vendor'
                ],
                'select_columns' => [
                    'id', 'asset_id', 'vendor_id', 'issue_reported', 'repair_action',
                    'cost', 'status', 'start_date', 'end_date', 'created_at'
                ]
            ];
        });
    }

    /**
     * Get optimized query builder for assets
     */
    public static function getOptimizedAssetQuery()
    {
        $config = self::optimizeAssetQueries();
        
        return DB::table('assets')
            ->select($config['select_columns'])
            ->with($config['with_relationships']);
    }

    /**
     * Get optimized query builder for users
     */
    public static function getOptimizedUserQuery()
    {
        $config = self::optimizeUserQueries();
        
        return DB::table('users')
            ->select($config['select_columns'])
            ->with($config['with_relationships']);
    }

    /**
     * Get optimized query builder for maintenance
     */
    public static function getOptimizedMaintenanceQuery()
    {
        $config = self::optimizeMaintenanceQueries();
        
        return DB::table('maintenance')
            ->select($config['select_columns'])
            ->with($config['with_relationships']);
    }

    /**
     * Optimize report queries with caching
     */
    public static function getOptimizedReportQuery($reportType, $filters = [])
    {
        $cacheKey = "optimized_report_{$reportType}_" . md5(serialize($filters));
        
        return Cache::remember($cacheKey, 1800, function() use ($reportType, $filters) {
            switch ($reportType) {
                case 'asset_analytics':
                    return self::optimizeAssetAnalyticsQuery($filters);
                case 'user_activity':
                    return self::optimizeUserActivityQuery($filters);
                case 'financial':
                    return self::optimizeFinancialQuery($filters);
                case 'maintenance':
                    return self::optimizeMaintenanceReportQuery($filters);
                default:
                    return null;
            }
        });
    }

    /**
     * Optimize asset analytics query
     */
    private static function optimizeAssetAnalyticsQuery($filters)
    {
        $query = DB::table('assets')
            ->leftJoin('asset_categories', 'assets.category_id', '=', 'asset_categories.id')
            ->leftJoin('departments', 'assets.department_id', '=', 'departments.id')
            ->leftJoin('vendors', 'assets.vendor_id', '=', 'vendors.id')
            ->select([
                'assets.id',
                'assets.name',
                'assets.status',
                'assets.cost',
                'assets.purchase_date',
                'asset_categories.name as category_name',
                'departments.name as department_name',
                'vendors.name as vendor_name'
            ]);

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('assets.status', $filters['status']);
        }

        if (isset($filters['category_id'])) {
            $query->where('assets.category_id', $filters['category_id']);
        }

        if (isset($filters['date_from'])) {
            $query->where('assets.purchase_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('assets.purchase_date', '<=', $filters['date_to']);
        }

        return $query;
    }

    /**
     * Optimize user activity query
     */
    private static function optimizeUserActivityQuery($filters)
    {
        $query = DB::table('audit_logs')
            ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
            ->select([
                'audit_logs.id',
                'audit_logs.action',
                'audit_logs.created_at',
                'users.name as user_name',
                'users.email',
                'departments.name as department_name'
            ]);

        // Apply filters
        if (isset($filters['user_id'])) {
            $query->where('audit_logs.user_id', $filters['user_id']);
        }

        if (isset($filters['action'])) {
            $query->where('audit_logs.action', $filters['action']);
        }

        if (isset($filters['date_from'])) {
            $query->where('audit_logs.created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('audit_logs.created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('audit_logs.created_at', 'desc');
    }

    /**
     * Optimize financial query
     */
    private static function optimizeFinancialQuery($filters)
    {
        $query = DB::table('assets')
            ->leftJoin('asset_categories', 'assets.category_id', '=', 'asset_categories.id')
            ->leftJoin('departments', 'assets.department_id', '=', 'departments.id')
            ->leftJoin('maintenance', 'assets.id', '=', 'maintenance.asset_id')
            ->select([
                'assets.id',
                'assets.name',
                'assets.cost',
                'assets.purchase_date',
                'asset_categories.name as category_name',
                'departments.name as department_name',
                DB::raw('SUM(maintenance.cost) as maintenance_cost'),
                DB::raw('COUNT(maintenance.id) as maintenance_count')
            ])
            ->groupBy('assets.id', 'assets.name', 'assets.cost', 'assets.purchase_date', 'asset_categories.name', 'departments.name');

        // Apply filters
        if (isset($filters['date_from'])) {
            $query->where('assets.purchase_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('assets.purchase_date', '<=', $filters['date_to']);
        }

        return $query;
    }

    /**
     * Optimize maintenance report query
     */
    private static function optimizeMaintenanceReportQuery($filters)
    {
        $query = DB::table('maintenance')
            ->leftJoin('assets', 'maintenance.asset_id', '=', 'assets.id')
            ->leftJoin('asset_categories', 'assets.category_id', '=', 'asset_categories.id')
            ->leftJoin('vendors', 'maintenance.vendor_id', '=', 'vendors.id')
            ->select([
                'maintenance.id',
                'maintenance.issue_reported',
                'maintenance.repair_action',
                'maintenance.cost',
                'maintenance.status',
                'maintenance.start_date',
                'maintenance.end_date',
                'assets.name as asset_name',
                'asset_categories.name as category_name',
                'vendors.name as vendor_name'
            ]);

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('maintenance.status', $filters['status']);
        }

        if (isset($filters['vendor_id'])) {
            $query->where('maintenance.vendor_id', $filters['vendor_id']);
        }

        if (isset($filters['date_from'])) {
            $query->where('maintenance.created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('maintenance.created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('maintenance.created_at', 'desc');
    }

    /**
     * Get query performance statistics
     */
    public static function getQueryPerformanceStats()
    {
        $queries = DB::getQueryLog();
        
        if (empty($queries)) {
            return [
                'total_queries' => 0,
                'average_time' => 0,
                'slow_queries' => 0,
                'fastest_query' => 0,
                'slowest_query' => 0
            ];
        }

        $times = array_column($queries, 'time');
        
        return [
            'total_queries' => count($queries),
            'average_time' => round(array_sum($times) / count($times), 2),
            'slow_queries' => count(array_filter($times, function($time) { return $time > 100; })),
            'fastest_query' => round(min($times), 2),
            'slowest_query' => round(max($times), 2),
            'total_time' => round(array_sum($times), 2)
        ];
    }

    /**
     * Clear query optimization cache
     */
    public static function clearOptimizationCache()
    {
        Cache::forget('optimized_asset_queries');
        Cache::forget('optimized_user_queries');
        Cache::forget('optimized_maintenance_queries');
        
        // Clear report caches
        $patterns = [
            'optimized_report_asset_analytics_*',
            'optimized_report_user_activity_*',
            'optimized_report_financial_*',
            'optimized_report_maintenance_*'
        ];
        
        foreach ($patterns as $pattern) {
            // In a real implementation, you would use Redis SCAN or similar
            // For now, we'll just clear the cache
        }
    }
}
