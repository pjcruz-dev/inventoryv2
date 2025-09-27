<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CacheService
{
    /**
     * Cache duration constants
     */
    const CACHE_SHORT = 300; // 5 minutes
    const CACHE_MEDIUM = 1800; // 30 minutes
    const CACHE_LONG = 3600; // 1 hour
    const CACHE_VERY_LONG = 86400; // 24 hours

    /**
     * Cache statistics
     */
    private static $cacheStats = [
        'hits' => 0,
        'misses' => 0,
        'sets' => 0,
        'deletes' => 0
    ];

    /**
     * Get cache statistics
     */
    public static function getStats()
    {
        return self::$cacheStats;
    }

    /**
     * Remember data with automatic cache invalidation
     */
    public static function remember($key, $duration, $callback, $tags = [])
    {
        try {
            $result = Cache::tags($tags)->remember($key, $duration, $callback);
            self::$cacheStats['hits']++;
            return $result;
        } catch (\Exception $e) {
            self::$cacheStats['misses']++;
            return $callback();
        }
    }

    /**
     * Cache database query results
     */
    public static function rememberQuery($key, $duration, $query, $tags = [])
    {
        return self::remember($key, $duration, function() use ($query) {
            return $query();
        }, $tags);
    }

    /**
     * Cache model counts with automatic invalidation
     */
    public static function rememberCount($model, $conditions = [], $duration = self::CACHE_MEDIUM)
    {
        $key = 'count_' . strtolower(class_basename($model)) . '_' . md5(serialize($conditions));
        $tags = [strtolower(class_basename($model))];
        
        return self::remember($key, $duration, function() use ($model, $conditions) {
            $query = $model::query();
            foreach ($conditions as $field => $value) {
                $query->where($field, $value);
            }
            return $query->count();
        }, $tags);
    }

    /**
     * Cache paginated results
     */
    public static function rememberPaginated($key, $duration, $query, $perPage = 15, $tags = [])
    {
        return self::remember($key, $duration, function() use ($query, $perPage) {
            return $query->paginate($perPage);
        }, $tags);
    }

    /**
     * Cache report data
     */
    public static function rememberReport($reportType, $params = [], $duration = self::CACHE_MEDIUM)
    {
        $key = 'report_' . $reportType . '_' . md5(serialize($params));
        $tags = ['reports', $reportType];
        
        return self::remember($key, $duration, function() use ($reportType, $params) {
            // This will be implemented by the specific report methods
            return null;
        }, $tags);
    }

    /**
     * Invalidate cache by tags
     */
    public static function invalidateByTags($tags)
    {
        try {
            Cache::tags($tags)->flush();
            self::$cacheStats['deletes']++;
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Invalidate model cache
     */
    public static function invalidateModel($model)
    {
        $modelName = strtolower(class_basename($model));
        return self::invalidateByTags([$modelName]);
    }

    /**
     * Invalidate report cache
     */
    public static function invalidateReports($reportType = null)
    {
        if ($reportType) {
            return self::invalidateByTags(['reports', $reportType]);
        }
        return self::invalidateByTags(['reports']);
    }

    /**
     * Warm up cache for frequently accessed data
     */
    public static function warmUp()
    {
        $warmUpTasks = [
            'asset_counts' => function() {
                return [
                    'total' => \App\Models\Asset::count(),
                    'active' => \App\Models\Asset::where('status', 'active')->count(),
                    'inactive' => \App\Models\Asset::where('status', 'inactive')->count(),
                    'disposed' => \App\Models\Asset::where('status', 'disposed')->count(),
                ];
            },
            'user_counts' => function() {
                return [
                    'total' => \App\Models\User::count(),
                    'active' => \App\Models\User::where('status', 'active')->count(),
                ];
            },
            'maintenance_counts' => function() {
                return [
                    'total' => \App\Models\Maintenance::count(),
                    'pending' => \App\Models\Maintenance::where('status', 'pending')->count(),
                    'completed' => \App\Models\Maintenance::where('status', 'completed')->count(),
                ];
            }
        ];

        foreach ($warmUpTasks as $key => $callback) {
            try {
                Cache::put($key, $callback(), self::CACHE_MEDIUM);
                self::$cacheStats['sets']++;
            } catch (\Exception $e) {
                // Log error but continue
                \Log::warning("Cache warm-up failed for {$key}: " . $e->getMessage());
            }
        }
    }

    /**
     * Get cache memory usage
     */
    public static function getMemoryUsage()
    {
        try {
            if (config('cache.default') === 'redis') {
                $info = Redis::info('memory');
                return [
                    'used_memory' => $info['used_memory_human'] ?? 'Unknown',
                    'used_memory_peak' => $info['used_memory_peak_human'] ?? 'Unknown',
                    'connected_clients' => $info['connected_clients'] ?? 0,
                ];
            }
            
            return ['driver' => config('cache.default'), 'memory' => 'Not available'];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Clear all cache
     */
    public static function clearAll()
    {
        try {
            Cache::flush();
            self::$cacheStats['deletes']++;
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get cache keys by pattern
     */
    public static function getKeys($pattern = '*')
    {
        try {
            if (config('cache.default') === 'redis') {
                return Redis::keys($pattern);
            }
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }
}