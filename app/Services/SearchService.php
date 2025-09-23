<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\User;
use App\Models\AssetCategory;
use App\Models\Department;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchService
{
    /**
     * Perform global search across all entities
     */
    public function globalSearch($query, $limit = 20)
    {
        $results = [
            'assets' => $this->searchAssets($query, $limit),
            'users' => $this->searchUsers($query, $limit),
            'categories' => $this->searchCategories($query, $limit),
            'departments' => $this->searchDepartments($query, $limit),
            'vendors' => $this->searchVendors($query, $limit),
        ];

        return $results;
    }

    /**
     * Search assets with advanced filters
     */
    public function searchAssets($query, $limit = 20, $filters = [])
    {
        $assets = Asset::with(['category', 'assignedUser', 'department', 'vendor'])
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('asset_tag', 'like', "%{$query}%")
                  ->orWhere('serial_number', 'like', "%{$query}%")
                  ->orWhere('model', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('location', 'like', "%{$query}%")
                  ->orWhere('notes', 'like', "%{$query}%");
            });

        // Apply filters
        if (!empty($filters['status'])) {
            $assets->whereIn('status', $filters['status']);
        }

        if (!empty($filters['category_id'])) {
            $assets->whereIn('category_id', $filters['category_id']);
        }

        if (!empty($filters['department_id'])) {
            $assets->whereIn('department_id', $filters['department_id']);
        }

        if (!empty($filters['assigned_to'])) {
            $assets->whereIn('assigned_to', $filters['assigned_to']);
        }

        if (!empty($filters['vendor_id'])) {
            $assets->whereIn('vendor_id', $filters['vendor_id']);
        }

        if (!empty($filters['date_from'])) {
            $assets->where('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $assets->where('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['cost_min'])) {
            $assets->where('cost', '>=', $filters['cost_min']);
        }

        if (!empty($filters['cost_max'])) {
            $assets->where('cost', '<=', $filters['cost_max']);
        }

        return $assets->orderBy('name')->paginate($limit);
    }

    /**
     * Search users
     */
    public function searchUsers($query, $limit = 20)
    {
        return User::with('department')
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('employee_id', 'like', "%{$query}%")
                  ->orWhere('position', 'like', "%{$query}%");
            })
            ->orderBy('first_name')
            ->paginate($limit);
    }

    /**
     * Search categories
     */
    public function searchCategories($query, $limit = 20)
    {
        return AssetCategory::where('name', 'like', "%{$query}%")
            ->orderBy('name')
            ->paginate($limit);
    }

    /**
     * Search departments
     */
    public function searchDepartments($query, $limit = 20)
    {
        return Department::where('name', 'like', "%{$query}%")
            ->orderBy('name')
            ->paginate($limit);
    }

    /**
     * Search vendors
     */
    public function searchVendors($query, $limit = 20)
    {
        return Vendor::where('name', 'like', "%{$query}%")
            ->orWhere('contact_person', 'like', "%{$query}%")
            ->orderBy('name')
            ->paginate($limit);
    }

    /**
     * Get search suggestions for autocomplete
     */
    public function getSearchSuggestions($query, $limit = 10)
    {
        $suggestions = [];

        // Asset suggestions
        $assets = Asset::where('name', 'like', "%{$query}%")
            ->orWhere('asset_tag', 'like', "%{$query}%")
            ->select('name', 'asset_tag', 'id')
            ->limit($limit)
            ->get();

        foreach ($assets as $asset) {
            $suggestions[] = [
                'type' => 'asset',
                'id' => $asset->id,
                'text' => $asset->name,
                'subtext' => "Asset Tag: {$asset->asset_tag}",
                'url' => route('assets.show', $asset->id),
                'icon' => 'fas fa-desktop'
            ];
        }

        // User suggestions
        $users = User::where('first_name', 'like', "%{$query}%")
            ->orWhere('last_name', 'like', "%{$query}%")
            ->orWhere('employee_id', 'like', "%{$query}%")
            ->select('first_name', 'last_name', 'employee_id', 'id')
            ->limit($limit)
            ->get();

        foreach ($users as $user) {
            $suggestions[] = [
                'type' => 'user',
                'id' => $user->id,
                'text' => "{$user->first_name} {$user->last_name}",
                'subtext' => "Employee ID: {$user->employee_id}",
                'url' => route('users.show', $user->id),
                'icon' => 'fas fa-user'
            ];
        }

        // Category suggestions
        $categories = AssetCategory::where('name', 'like', "%{$query}%")
            ->select('name', 'id')
            ->limit($limit)
            ->get();

        foreach ($categories as $category) {
            $suggestions[] = [
                'type' => 'category',
                'id' => $category->id,
                'text' => $category->name,
                'subtext' => 'Asset Category',
                'url' => route('asset-categories.show', $category->id),
                'icon' => 'fas fa-tags'
            ];
        }

        return array_slice($suggestions, 0, $limit);
    }

    /**
     * Get filter options for advanced search
     */
    public function getFilterOptions()
    {
        return [
            'statuses' => Asset::select('status')->distinct()->pluck('status')->filter()->values(),
            'categories' => AssetCategory::select('id', 'name')->orderBy('name')->get(),
            'departments' => Department::select('id', 'name')->orderBy('name')->get(),
            'vendors' => Vendor::select('id', 'name')->orderBy('name')->get(),
            'assigned_users' => User::whereHas('assignedAssets')->select('id', 'first_name', 'last_name')->orderBy('first_name')->get()
        ];
    }

    /**
     * Get search statistics
     */
    public function getSearchStats($query)
    {
        $assetCount = Asset::where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('asset_tag', 'like', "%{$query}%")
              ->orWhere('serial_number', 'like', "%{$query}%");
        })->count();

        $userCount = User::where(function ($q) use ($query) {
            $q->where('first_name', 'like', "%{$query}%")
              ->orWhere('last_name', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%");
        })->count();

        return [
            'assets' => $assetCount,
            'users' => $userCount,
            'total' => $assetCount + $userCount
        ];
    }

    /**
     * Save search query for analytics
     */
    public function saveSearchQuery($query, $userId = null, $filters = [])
    {
        // This could be expanded to save search analytics
        // For now, we'll just log it
        \Log::info('Search performed', [
            'query' => $query,
            'user_id' => $userId,
            'filters' => $filters,
            'timestamp' => now()
        ]);
    }

    /**
     * Get recent searches for a user
     */
    public function getRecentSearches($userId, $limit = 10)
    {
        // This would typically come from a database table
        // For now, return empty array
        return [];
    }

    /**
     * Get popular searches
     */
    public function getPopularSearches($limit = 10)
    {
        // This would typically come from analytics
        // For now, return some common searches
        return [
            'laptop',
            'monitor',
            'printer',
            'desktop',
            'keyboard',
            'mouse',
            'active',
            'inactive',
            'maintenance'
        ];
    }
}

