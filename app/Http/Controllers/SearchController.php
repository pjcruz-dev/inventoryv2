<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SearchService;

class SearchController extends Controller
{
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Show the search page
     */
    public function index()
    {
        $this->authorize('view_assets');
        
        $filterOptions = $this->searchService->getFilterOptions();
        $popularSearches = $this->searchService->getPopularSearches();
        
        return view('search.index', compact('filterOptions', 'popularSearches'));
    }

    /**
     * Perform global search
     */
    public function search(Request $request)
    {
        $this->authorize('view_assets');
        
        $query = $request->input('q', '');
        $filters = $request->only([
            'status', 'category_id', 'department_id', 'assigned_to', 
            'vendor_id', 'date_from', 'date_to', 'cost_min', 'cost_max'
        ]);
        
        // Clean up filters
        $filters = array_filter($filters, function($value) {
            return !empty($value);
        });
        
        $limit = $request->input('limit', 20);
        $entity = $request->input('entity', 'all');
        
        $results = [];
        $stats = $this->searchService->getSearchStats($query);
        
        if ($entity === 'all' || $entity === 'assets') {
            $results['assets'] = $this->searchService->searchAssets($query, $limit, $filters);
        }
        
        if ($entity === 'all' || $entity === 'users') {
            $results['users'] = $this->searchService->searchUsers($query, $limit);
        }
        
        if ($entity === 'all' || $entity === 'categories') {
            $results['categories'] = $this->searchService->searchCategories($query, $limit);
        }
        
        if ($entity === 'all' || $entity === 'departments') {
            $results['departments'] = $this->searchService->searchDepartments($query, $limit);
        }
        
        if ($entity === 'all' || $entity === 'vendors') {
            $results['vendors'] = $this->searchService->searchVendors($query, $limit);
        }
        
        // Save search query for analytics
        $this->searchService->saveSearchQuery($query, auth()->id(), $filters);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'query' => $query,
                'results' => $results,
                'stats' => $stats,
                'filters' => $filters
            ]);
        }
        
        return view('search.results', compact('query', 'results', 'stats', 'filters'));
    }

    /**
     * Get search suggestions for autocomplete
     */
    public function suggestions(Request $request)
    {
        $query = $request->input('q', '');
        $limit = $request->input('limit', 10);
        
        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'suggestions' => []
            ]);
        }
        
        $suggestions = $this->searchService->getSearchSuggestions($query, $limit);
        
        return response()->json([
            'success' => true,
            'suggestions' => $suggestions
        ]);
    }

    /**
     * Get filter options
     */
    public function filterOptions()
    {
        $this->authorize('view_assets');
        
        $options = $this->searchService->getFilterOptions();
        
        return response()->json([
            'success' => true,
            'options' => $options
        ]);
    }

    /**
     * Get recent searches
     */
    public function recentSearches()
    {
        $this->authorize('view_assets');
        
        $recent = $this->searchService->getRecentSearches(auth()->id());
        
        return response()->json([
            'success' => true,
            'recent' => $recent
        ]);
    }

    /**
     * Get popular searches
     */
    public function popularSearches()
    {
        $this->authorize('view_assets');
        
        $popular = $this->searchService->getPopularSearches();
        
        return response()->json([
            'success' => true,
            'popular' => $popular
        ]);
    }
}

