<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    private const DEFAULT_TTL = 3600; // 1 hour
    private const SHORT_TTL = 300;    // 5 minutes
    private const LONG_TTL = 86400;   // 24 hours

    /**
     * Cache dashboard statistics
     */
    public function cacheDashboardStats(array $stats, string $entity = null): void
    {
        $key = $entity ? "dashboard_stats_{$entity}" : 'dashboard_stats';
        Cache::put($key, $stats, self::SHORT_TTL);
    }

    /**
     * Get cached dashboard statistics
     */
    public function getDashboardStats(string $entity = null): ?array
    {
        $key = $entity ? "dashboard_stats_{$entity}" : 'dashboard_stats';
        return Cache::get($key);
    }

    /**
     * Cache asset counts by category
     */
    public function cacheAssetCounts(): void
    {
        $counts = \App\Models\Asset::selectRaw('category_id, COUNT(*) as count')
            ->groupBy('category_id')
            ->pluck('count', 'category_id')
            ->toArray();
            
        Cache::put('asset_counts_by_category', $counts, self::DEFAULT_TTL);
    }

    /**
     * Get cached asset counts
     */
    public function getAssetCounts(): ?array
    {
        return Cache::get('asset_counts_by_category');
    }

    /**
     * Cache user permissions
     */
    public function cacheUserPermissions(int $userId, array $permissions): void
    {
        $key = "user_permissions_{$userId}";
        Cache::put($key, $permissions, self::LONG_TTL);
    }

    /**
     * Get cached user permissions
     */
    public function getUserPermissions(int $userId): ?array
    {
        $key = "user_permissions_{$userId}";
        return Cache::get($key);
    }

    /**
     * Cache department hierarchy
     */
    public function cacheDepartmentHierarchy(): void
    {
        $hierarchy = \App\Models\Department::with('children')
            ->whereNull('parent_id')
            ->get()
            ->toArray();
            
        Cache::put('department_hierarchy', $hierarchy, self::LONG_TTL);
    }

    /**
     * Get cached department hierarchy
     */
    public function getDepartmentHierarchy(): ?array
    {
        return Cache::get('department_hierarchy');
    }

    /**
     * Cache asset categories
     */
    public function cacheAssetCategories(): void
    {
        $categories = \App\Models\AssetCategory::orderBy('name')->get()->toArray();
        Cache::put('asset_categories', $categories, self::LONG_TTL);
    }

    /**
     * Get cached asset categories
     */
    public function getAssetCategories(): ?array
    {
        return Cache::get('asset_categories');
    }

    /**
     * Cache vendors list
     */
    public function cacheVendors(): void
    {
        $vendors = \App\Models\Vendor::orderBy('name')->get()->toArray();
        Cache::put('vendors_list', $vendors, self::LONG_TTL);
    }

    /**
     * Get cached vendors
     */
    public function getVendors(): ?array
    {
        return Cache::get('vendors_list');
    }

    /**
     * Clear all application caches
     */
    public function clearAllCaches(): void
    {
        $cacheKeys = [
            'dashboard_stats',
            'asset_counts_by_category',
            'department_hierarchy',
            'asset_categories',
            'vendors_list'
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Clear user permission caches
        $this->clearUserPermissionCaches();

        Log::info('All application caches cleared');
    }

    /**
     * Clear user permission caches
     */
    public function clearUserPermissionCaches(): void
    {
        $users = \App\Models\User::pluck('id');
        foreach ($users as $userId) {
            Cache::forget("user_permissions_{$userId}");
        }
    }

    /**
     * Cache search results for better performance
     */
    public function cacheSearchResults(string $query, array $results, string $type): void
    {
        $key = "search_{$type}_" . md5($query);
        Cache::put($key, $results, self::SHORT_TTL);
    }

    /**
     * Get cached search results
     */
    public function getSearchResults(string $query, string $type): ?array
    {
        $key = "search_{$type}_" . md5($query);
        return Cache::get($key);
    }

    /**
     * Warm up essential caches
     */
    public function warmupCaches(): void
    {
        try {
            $this->cacheAssetCategories();
            $this->cacheVendors();
            $this->cacheDepartmentHierarchy();
            $this->cacheAssetCounts();
            
            Log::info('Essential caches warmed up successfully');
        } catch (\Exception $e) {
            Log::error('Failed to warm up caches: ' . $e->getMessage());
        }
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats(): array
    {
        return [
            'driver' => config('cache.default'),
            'store' => config('cache.stores.' . config('cache.default')),
            'permissions_cache' => Cache::get('spatie.permission.cache') ? 'Active' : 'Empty',
            'cache_size' => $this->getCacheSize(),
        ];
    }

    /**
     * Get approximate cache size
     */
    private function getCacheSize(): string
    {
        if (config('cache.default') === 'database') {
            $count = \DB::table('cache')->count();
            return "{$count} entries";
        }
        
        return 'Unknown';
    }
}
