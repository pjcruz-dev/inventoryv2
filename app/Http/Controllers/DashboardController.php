<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\User;
use App\Models\Department;
use App\Models\Vendor;
use App\Services\BreadcrumbService;
use Illuminate\Support\Facades\View;

class DashboardController extends Controller
{
    protected $breadcrumbService;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BreadcrumbService $breadcrumbService)
    {
        $this->breadcrumbService = $breadcrumbService;
        $this->middleware('auth');
        $this->middleware('permission:view_dashboard');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $filterMonth = $request->get('month');
        $filterYear = $request->get('year');
        $filterEntity = $request->get('entity');
        
        // Get basic statistics with entity filter
        $assetQuery = Asset::query();
        $userQuery = User::query();
        
        if ($filterEntity) {
            $assetQuery->where('entity', $filterEntity);
            // Only apply entity filter to users if the column exists
            try {
                $userQuery->where('entity', $filterEntity);
            } catch (\Exception $e) {
                // Entity column doesn't exist in users table, skip filter
            }
        }
        
        $totalAssets = $assetQuery->count();
        $totalUsers = $userQuery->count();
        $totalDepartments = Department::count();
        $totalVendors = Vendor::count();
        
        // Get recent assets (last 5) with entity filter
        $recentAssetsQuery = Asset::with('category')->latest();
        if ($filterEntity) {
            $recentAssetsQuery->where('entity', $filterEntity);
        }
        $recentAssets = $recentAssetsQuery->take(5)->get();
        
        // Calculate active assets percentage with entity filter (using 'Active' status)
        $activeAssetsQuery = Asset::where('status', 'Active');
        if ($filterEntity) {
            $activeAssetsQuery->where('entity', $filterEntity);
        }
        $activeAssets = $activeAssetsQuery->count();
        $activeAssetsPercentage = $totalAssets > 0 ? round(($activeAssets / $totalAssets) * 100, 1) : 0;
        
        // Get status breakdown for all asset statuses
        $statusBreakdown = [];
        $statuses = ['Available', 'Active', 'Inactive', 'Under Maintenance', 'Issue Reported', 'Pending Confirmation', 'Disposed'];
        
        foreach ($statuses as $status) {
            $query = Asset::where('status', $status);
            if ($filterEntity) {
                $query->where('entity', $filterEntity);
            }
            $statusBreakdown[$status] = $query->count();
        }
        
        // Get data for the three dashboard sections with filters
        $weeklyBreakdown = $this->getWeeklyBreakdown($filterMonth, $filterYear);
        $monthlyRollup = $this->getMonthlyRollup($filterMonth, $filterYear);
        $chartData = $this->getChartData($filterMonth, $filterYear);
        
        // Get entities for filter dropdown
        $entities = Asset::distinct()->pluck('entity')->filter()->sort()->values();
        
        // Check if users table has entity column, if not, use empty collection
        try {
            $userEntities = User::distinct()->pluck('entity')->filter()->sort()->values();
        } catch (\Exception $e) {
            $userEntities = collect();
        }
        
        return view('dashboard', compact(
            'totalAssets',
            'totalUsers', 
            'totalDepartments',
            'totalVendors',
            'recentAssets',
            'activeAssetsPercentage',
            'statusBreakdown',
            'weeklyBreakdown',
            'monthlyRollup',
            'chartData',
            'entities'
        ));
    }
    
    /**
     * Get weekly breakdown data for asset lifecycle status
     */
    private function getWeeklyBreakdown($filterMonth = null, $filterYear = null)
    {
        $statuses = ['Deployed', 'Disposed', 'New Arrival', 'Returned'];
        $months = [];
        
        if ($filterMonth && $filterYear) {
            // Filter for specific month and year
            $date = now()->setYear((int)$filterYear)->setMonth((int)$filterMonth);
            $monthName = $date->format('F Y');
            $monthData = [];
            
            $startOfMonth = $date->startOfMonth()->copy();
            $endOfMonth = $date->endOfMonth()->copy();
            
            // Calculate the correct number of weeks needed for this month
            $totalDays = $endOfMonth->day;
            $weeksNeeded = ceil($totalDays / 7);
            
            for ($week = 1; $week <= $weeksNeeded; $week++) {
                $weekStart = $startOfMonth->copy()->addDays(($week - 1) * 7);
                $weekEnd = $weekStart->copy()->addDays(6);
                
                if ($weekEnd->gt($endOfMonth)) {
                    $weekEnd = $endOfMonth->copy();
                }
                
                $weekData = [];
                foreach ($statuses as $status) {
                    $assetIds = collect();
                    
                    // Get assets that were created with this movement during this week
                    $createdAssetIds = \App\Models\Asset::where('movement', $status)
                        ->whereBetween('created_at', [$weekStart, $weekEnd])
                        ->pluck('id');
                    $assetIds = $assetIds->merge($createdAssetIds);
                    
                    // Get assets that had their movement changed to this status during this week
                    $timelineAssetIds = \App\Models\AssetTimeline::where('action', 'updated')
                        ->whereJsonContains('new_values->movement', $status)
                        ->whereBetween('performed_at', [$weekStart, $weekEnd])
                        ->whereNotIn('asset_id', $createdAssetIds)
                        ->pluck('asset_id');
                    $assetIds = $assetIds->merge($timelineAssetIds);
                    
                    // For Deployed status, also include Deployed Tagged
                    if ($status === 'Deployed') {
                        $deployedTaggedCreatedIds = \App\Models\Asset::where('movement', 'Deployed Tagged')
                            ->whereBetween('created_at', [$weekStart, $weekEnd])
                            ->pluck('id');
                        $assetIds = $assetIds->merge($deployedTaggedCreatedIds);
                        
                        $deployedTaggedTimelineIds = \App\Models\AssetTimeline::where('action', 'updated')
                            ->whereJsonContains('new_values->movement', 'Deployed Tagged')
                            ->whereBetween('performed_at', [$weekStart, $weekEnd])
                            ->whereNotIn('asset_id', $deployedTaggedCreatedIds)
                            ->pluck('asset_id');
                        $assetIds = $assetIds->merge($deployedTaggedTimelineIds);
                    }
                    
                    $count = $assetIds->unique()->count();
                    $weekData[$status] = $count;
                }
                
                // Create week label with date range
                $weekLabel = "Week $week";
                $dateRange = $weekStart->format('M j') . ' - ' . $weekEnd->format('M j');
                $weekData['_label'] = $weekLabel;
                $weekData['_date_range'] = $dateRange;
                
                $monthData["$weekLabel ($dateRange)"] = $weekData;
            }
            
            $months[$monthName] = $monthData;
        } else {
            // Get last 3 months (default behavior) - latest month first
            for ($i = 0; $i <= 2; $i++) {
                $date = now()->subMonths($i);
                $monthName = $date->format('F Y');
                $monthData = [];
                
                // Get weeks in this month
                $startOfMonth = $date->startOfMonth()->copy();
                $endOfMonth = $date->endOfMonth()->copy();
                
                // Calculate the correct number of weeks needed for this month
                $totalDays = $endOfMonth->day;
                $weeksNeeded = ceil($totalDays / 7);
                
                for ($week = 1; $week <= $weeksNeeded; $week++) {
                    $weekStart = $startOfMonth->copy()->addDays(($week - 1) * 7);
                    $weekEnd = $weekStart->copy()->addDays(6);
                    
                    if ($weekEnd->gt($endOfMonth)) {
                        $weekEnd = $endOfMonth->copy();
                    }
                    
                    $weekData = [];
                    foreach ($statuses as $status) {
                        $assetIds = collect();
                        
                        // Get assets that were created with this movement during this week
                        $createdAssetIds = \App\Models\Asset::where('movement', $status)
                            ->whereBetween('created_at', [$weekStart, $weekEnd])
                            ->pluck('id');
                        $assetIds = $assetIds->merge($createdAssetIds);
                        
                        // Get assets that had their movement changed to this status during this week
                        $timelineAssetIds = \App\Models\AssetTimeline::where('action', 'updated')
                            ->whereJsonContains('new_values->movement', $status)
                            ->whereBetween('performed_at', [$weekStart, $weekEnd])
                            ->whereNotIn('asset_id', $createdAssetIds)
                            ->pluck('asset_id');
                        $assetIds = $assetIds->merge($timelineAssetIds);
                        
                        // For Deployed status, also include Deployed Tagged
                        if ($status === 'Deployed') {
                            $deployedTaggedCreatedIds = \App\Models\Asset::where('movement', 'Deployed Tagged')
                                ->whereBetween('created_at', [$weekStart, $weekEnd])
                                ->pluck('id');
                            $assetIds = $assetIds->merge($deployedTaggedCreatedIds);
                            
                            $deployedTaggedTimelineIds = \App\Models\AssetTimeline::where('action', 'updated')
                                ->whereJsonContains('new_values->movement', 'Deployed Tagged')
                                ->whereBetween('performed_at', [$weekStart, $weekEnd])
                                ->whereNotIn('asset_id', $deployedTaggedCreatedIds)
                                ->pluck('asset_id');
                            $assetIds = $assetIds->merge($deployedTaggedTimelineIds);
                        }
                        
                        $count = $assetIds->unique()->count();
                        $weekData[$status] = $count;
                    }
                    
                    // Create week label with date range
                    $weekLabel = "Week $week";
                    $dateRange = $weekStart->format('M j') . ' - ' . $weekEnd->format('M j');
                    $weekData['_label'] = $weekLabel;
                    $weekData['_date_range'] = $dateRange;
                    
                    $monthData["$weekLabel ($dateRange)"] = $weekData;
                }
                
                $months[$monthName] = $monthData;
            }
        }
        
        return $months;
    }
    
    /**
     * Get monthly rollup data with percentages
     */
    private function getMonthlyRollup($filterMonth = null, $filterYear = null)
    {
        $statuses = ['deployed', 'problematic', 'pending_confirm', 'returned', 'disposed', 'new_arrived'];
        $months = [];
        
        if ($filterMonth && $filterYear) {
            // Filter for specific month and year
            $date = now()->setYear((int)$filterYear)->setMonth((int)$filterMonth);
            $monthName = $date->format('F');
            
            $startOfMonth = $date->startOfMonth();
            $endOfMonth = $date->endOfMonth();
            
            $monthData = [];
            $totalForMonth = 0;
            
            foreach ($statuses as $status) {
                // Get unique asset IDs that changed to this status during this month
                $timelineAssetIds = \App\Models\AssetTimeline::where('action', 'updated')
                    ->whereJsonContains('new_values->status', $status)
                    ->whereBetween('performed_at', [$startOfMonth, $endOfMonth])
                    ->distinct('asset_id')
                    ->pluck('asset_id');
                
                // Get asset IDs created with this status during this month
                $createdAssetIds = Asset::where('status', $status)
                    ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->pluck('id');
                
                // Combine and get unique count (same logic as weekly breakdown)
                $uniqueAssetIds = $timelineAssetIds->merge($createdAssetIds)->unique();
                $totalCount = $uniqueAssetIds->count();
                
                $monthData[$status] = $totalCount;
                $totalForMonth += $totalCount;
            }
            
            // Calculate percentages
            $monthDataWithPercentages = [];
            foreach ($monthData as $status => $count) {
                $percentage = $totalForMonth > 0 ? round(($count / $totalForMonth) * 100, 1) : 0;
                $monthDataWithPercentages[$status] = [
                    'count' => $count,
                    'percentage' => $percentage
                ];
            }
            
            $months[$monthName] = $monthDataWithPercentages;
        } else {
                // Get last 3 months (default behavior) - latest month first
            for ($i = 0; $i <= 2; $i++) {
                $date = now()->subMonths($i);
                $monthName = $date->format('F');
                
                $startOfMonth = $date->startOfMonth();
                $endOfMonth = $date->endOfMonth();
                
                $monthData = [];
                $totalForMonth = 0;
                
                foreach ($statuses as $status) {
                    // Get unique asset IDs that changed to this status during this month
                    $timelineAssetIds = \App\Models\AssetTimeline::where('action', 'updated')
                        ->whereJsonContains('new_values->status', $status)
                        ->whereBetween('performed_at', [$startOfMonth, $endOfMonth])
                        ->distinct('asset_id')
                        ->pluck('asset_id');
                    
                    // Get asset IDs created with this status during this month
                    $createdAssetIds = Asset::where('status', $status)
                        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                        ->pluck('id');
                    
                    // Combine and get unique count (same logic as weekly breakdown)
                    $uniqueAssetIds = $timelineAssetIds->merge($createdAssetIds)->unique();
                    $totalCount = $uniqueAssetIds->count();
                    
                    $monthData[$status] = $totalCount;
                    $totalForMonth += $totalCount;
                }
                
                // Calculate percentages
                $monthDataWithPercentages = [];
                foreach ($monthData as $status => $count) {
                    $percentage = $totalForMonth > 0 ? round(($count / $totalForMonth) * 100, 1) : 0;
                    $monthDataWithPercentages[$status] = [
                        'count' => $count,
                        'percentage' => $percentage
                    ];
                }
                
                $months[$monthName] = $monthDataWithPercentages;
            }
        }
        
        return $months;
    }
    
    /**
     * Get chart data for management insights
     */
    private function getChartData($filterMonth = null, $filterYear = null)
    {
        $statuses = ['Active', 'Issue Reported', 'Pending Confirmation', 'Under Maintenance', 'Disposed', 'New Arrival'];
        $movements = ['Deployed', 'Disposed', 'New Arrival', 'Returned'];
        
        // Current movement distribution (for pie chart)
        $currentDistribution = [];
        $total = Asset::count();
        
        foreach ($movements as $movement) {
            if ($movement === 'Deployed') {
                // Combine both 'Deployed' and 'Deployed Tagged' into one 'Deployed' count
                $count = Asset::whereIn('movement', ['Deployed', 'Deployed Tagged'])->count();
            } else {
                $count = Asset::where('movement', $movement)->count();
            }
            $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
            $currentDistribution[strtolower(str_replace(' ', '_', $movement))] = [
                'count' => $count,
                'percentage' => $percentage
            ];
        }
        
        if ($filterMonth && $filterYear) {
            // Filter for specific month and year - show current status distribution
            $date = now()->setYear((int)$filterYear)->setMonth((int)$filterMonth);
            
            // Monthly trends for problematic assets (last 6 months)
            $problematicTrend = [];
            for ($i = 5; $i >= 0; $i--) {
                $trendDate = $date->copy()->subMonths($i);
                $monthName = $trendDate->format('M Y');
                
                $count = Asset::where('status', 'Issue Reported')
                    ->whereYear('created_at', $trendDate->year)
                    ->whereMonth('created_at', $trendDate->month)
                    ->count();
                    
                $problematicTrend[] = [
                    'month' => $monthName,
                    'count' => $count
                ];
            }
            
            // Monthly totals by status (last 3 months)
            $monthlyTotals = [];
            for ($i = 2; $i >= 0; $i--) {
                $trendDate = $date->copy()->subMonths($i);
                $monthName = $trendDate->format('F');
                
                $monthData = [];
                foreach ($statuses as $status) {
                    $count = Asset::where('status', $status)
                        ->whereYear('created_at', $trendDate->year)
                        ->whereMonth('created_at', $trendDate->month)
                        ->count();
                    $monthData[$status] = $count;
                }
                $monthlyTotals[$monthName] = $monthData;
            }
        } else {
            // Monthly trends for problematic assets (for trend line)
            $problematicTrend = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthName = $date->format('M Y');
                
                $count = Asset::where('status', 'Issue Reported')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
                    
                $problematicTrend[] = [
                    'month' => $monthName,
                    'count' => $count
                ];
            }
            
            // Monthly totals by status (for bar chart)
            $monthlyTotals = [];
            for ($i = 2; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthName = $date->format('F');
                
                $monthData = [];
                foreach ($statuses as $status) {
                    $count = Asset::where('status', $status)
                        ->whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->count();
                    $monthData[$status] = $count;
                }
                $monthlyTotals[$monthName] = $monthData;
            }
        }
        
        return [
            'currentDistribution' => $currentDistribution,
            'problematicTrend' => $problematicTrend,
            'monthlyTotals' => $monthlyTotals,
            'statuses' => $statuses
        ];
    }

    /**
     * Show detailed asset movements for a specific week and status
     */
    public function assetMovements(Request $request)
    {
        $week = $request->get('week');
        $status = $request->get('status');
        $month = $request->get('month');
        $year = $request->get('year');
        
        if (!$week || !$status || !$month || !$year) {
            return redirect()->route('dashboard')->with('error', 'Invalid parameters');
        }
        
        // Parse the month and year
        $date = now()->setYear((int)$year)->setMonth((int)$month);
        $startOfMonth = $date->startOfMonth()->copy();
        $endOfMonth = $date->endOfMonth()->copy();
        
        // Calculate week dates
        $weekNumber = (int)str_replace('Week ', '', $week);
        $weekStart = $startOfMonth->copy()->addWeeks($weekNumber - 1);
        $weekEnd = $weekStart->copy()->addDays(6);
        
        if ($weekEnd->gt($endOfMonth)) {
            $weekEnd = $endOfMonth->copy();
        }
        
        // Get assets that had their movement changed to this status during this specific week
        $assetIds = collect();
        
        // Get assets that were created with this movement during this week
        $createdAssetIds = \App\Models\Asset::where('movement', $status)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->pluck('id');
        $assetIds = $assetIds->merge($createdAssetIds);
        
        // Get assets that had their movement changed to this status during this week
        // (excluding the ones already created with this status)
        $timelineAssetIds = \App\Models\AssetTimeline::where('action', 'updated')
            ->whereJsonContains('new_values->movement', $status)
            ->whereBetween('performed_at', [$weekStart, $weekEnd])
            ->whereNotIn('asset_id', $createdAssetIds)
            ->pluck('asset_id');
        $assetIds = $assetIds->merge($timelineAssetIds);
        
        // For Deployed status, also include Deployed Tagged
        if ($status === 'Deployed') {
            $deployedTaggedCreatedIds = \App\Models\Asset::where('movement', 'Deployed Tagged')
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->pluck('id');
            $assetIds = $assetIds->merge($deployedTaggedCreatedIds);
            
            $deployedTaggedTimelineIds = \App\Models\AssetTimeline::where('action', 'updated')
                ->whereJsonContains('new_values->movement', 'Deployed Tagged')
                ->whereBetween('performed_at', [$weekStart, $weekEnd])
                ->whereNotIn('asset_id', $deployedTaggedCreatedIds)
                ->pluck('asset_id');
            $assetIds = $assetIds->merge($deployedTaggedTimelineIds);
        }
        
        // Get unique asset IDs
        $uniqueAssetIds = $assetIds->unique();
        
        // Get the actual assets with relationships
        $assets = \App\Models\Asset::with(['category', 'vendor', 'assignedUser', 'department'])
            ->whereIn('id', $uniqueAssetIds)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('dashboard.asset-movements', compact('assets', 'week', 'status', 'month', 'year', 'weekStart', 'weekEnd'));
    }
}
