<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\User;
use App\Models\Department;
use App\Models\Vendor;
use App\Models\AssetAssignmentConfirmation;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
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
        // Redirect User role to assets page since they don't have dashboard access
        if (auth()->user()->hasRole('User') && !auth()->user()->hasAnyRole(['Admin', 'Super Admin', 'Manager', 'IT Support'])) {
            return redirect()->route('assets.index');
        }
        // Get filter parameters
        $filterMonth = $request->get('month');
        $filterYear = $request->get('year');
        $filterEntity = $request->get('entity');
        
        // Get basic statistics with entity filter
        $assetQuery = Asset::query();
        $userQuery = User::query();
        
        if ($filterEntity) {
            $assetQuery->where('entity', $filterEntity);
            $userQuery->where('entity', $filterEntity);
        }
        
        $totalAssets = $assetQuery->count();
        $totalUsers = $userQuery->count();
        $totalDepartments = Department::count();
        $totalVendors = Vendor::count();
        
        // Calculate growth indicators (current vs last month)
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $lastMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
        $lastMonthYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;
        
        // Assets growth
        $currentMonthAssets = $assetQuery->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)->count();
        $lastMonthAssets = $assetQuery->whereYear('created_at', $lastMonthYear)
            ->whereMonth('created_at', $lastMonth)->count();
        $assetsGrowth = $this->calculateGrowthPercentage($currentMonthAssets, $lastMonthAssets);
        
        // Users growth
        $currentMonthUsers = $userQuery->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)->count();
        $lastMonthUsers = $userQuery->whereYear('created_at', $lastMonthYear)
            ->whereMonth('created_at', $lastMonth)->count();
        $usersGrowth = $this->calculateGrowthPercentage($currentMonthUsers, $lastMonthUsers);
        
        // Departments growth
        $currentMonthDepartments = Department::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)->count();
        $lastMonthDepartments = Department::whereYear('created_at', $lastMonthYear)
            ->whereMonth('created_at', $lastMonth)->count();
        $departmentsGrowth = $this->calculateGrowthPercentage($currentMonthDepartments, $lastMonthDepartments);
        
        // Vendors growth
        $currentMonthVendors = Vendor::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)->count();
        $lastMonthVendors = Vendor::whereYear('created_at', $lastMonthYear)
            ->whereMonth('created_at', $lastMonth)->count();
        $vendorsGrowth = $this->calculateGrowthPercentage($currentMonthVendors, $lastMonthVendors);
        
        // Get recent assets (last 5) with entity filter
        $recentAssetsQuery = Asset::with('category')->latest();
        if ($filterEntity) {
            $recentAssetsQuery->where('entity', $filterEntity);
        }
        $recentAssets = $recentAssetsQuery->take(5)->get();
        
        // Calculate deployed/active assets percentage with entity filter
        // Consider assets as "deployed" if they are active, assigned, or deployed
        $deployedAssetsQuery = Asset::whereIn('status', ['Active', 'Pending Confirmation']);
        if ($filterEntity) {
            $deployedAssetsQuery->where('entity', $filterEntity);
        }
        $deployedAssets = $deployedAssetsQuery->count();
        $deployedAssetsPercentage = $totalAssets > 0 ? round(($deployedAssets / $totalAssets) * 100, 1) : 0;
        
        // Get data for the three dashboard sections with filters
        $weeklyBreakdown = $this->getWeeklyBreakdown($filterMonth, $filterYear);
        $monthlyRollup = $this->getMonthlyRollup($filterMonth, $filterYear);
        $chartData = $this->getChartData($filterMonth, $filterYear);
        
        // Get entities for filter dropdown
        $entities = Asset::distinct()->pluck('entity')->filter()->sort()->values();
        
        // Get declined assets data for the new widget
        $declinedAssets = $this->getDeclinedAssetsData();
        
        return view('dashboard', compact(
            'totalAssets',
            'totalUsers', 
            'totalDepartments',
            'totalVendors',
            'recentAssets',
            'deployedAssetsPercentage',
            'weeklyBreakdown',
            'monthlyRollup',
            'chartData',
            'entities',
            'assetsGrowth',
            'usersGrowth',
            'departmentsGrowth',
            'vendorsGrowth',
            'declinedAssets'
        ));
    }
    
    /**
     * Calculate growth percentage between two values
     */
    private function calculateGrowthPercentage($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? ['percentage' => 100, 'trend' => 'positive', 'text' => '+100% from last month'] : 
                   ['percentage' => 0, 'trend' => 'neutral', 'text' => 'No change'];
        }
        
        $percentage = round((($current - $previous) / $previous) * 100, 1);
        
        if ($percentage > 0) {
            return [
                'percentage' => abs($percentage),
                'trend' => 'positive',
                'text' => '+' . abs($percentage) . '% from last month'
            ];
        } elseif ($percentage < 0) {
            return [
                'percentage' => abs($percentage),
                'trend' => 'negative',
                'text' => '-' . abs($percentage) . '% from last month'
            ];
        } else {
            return [
                'percentage' => 0,
                'trend' => 'neutral',
                'text' => 'No change'
            ];
        }
    }
    
    /**
     * Get weekly breakdown data for asset lifecycle status
     */
    private function getWeeklyBreakdown($filterMonth = null, $filterYear = null)
    {
        $statuses = ['Return', 'New Arrival', 'Deployed'];
        $months = [];
        
        if ($filterMonth && $filterYear) {
            // Filter for specific month and year
            $date = now()->setYear((int)$filterYear)->setMonth((int)$filterMonth);
            $monthName = $date->format('F Y');
            $monthData = [];
            
            
            $startOfMonth = $date->startOfMonth()->copy();
            $endOfMonth = $date->endOfMonth()->copy();
            
            for ($week = 1; $week <= 4; $week++) {
                $weekStart = $startOfMonth->copy()->addWeeks($week - 1);
                $weekEnd = $weekStart->copy()->addDays(6);
                
                if ($weekEnd->gt($endOfMonth)) {
                    $weekEnd = $endOfMonth->copy();
                }
                
                $weekData = [];
                foreach ($statuses as $status) {
                    // Get unique asset IDs that changed to this movement during this week
                    $timelineAssetIds = \App\Models\AssetTimeline::where('action', 'updated')
                        ->whereJsonContains('new_values->movement', $status)
                        ->whereBetween('performed_at', [$weekStart, $weekEnd])
                        ->distinct('asset_id')
                        ->pluck('asset_id');
                    
                    // Get asset IDs created with this movement during this week
                    $createdAssetIds = \App\Models\Asset::where('movement', $status)
                        ->whereBetween('created_at', [$weekStart, $weekEnd])
                        ->pluck('id');
                    
                    // Combine and get unique count (same logic as detail view)
                    $uniqueAssetIds = $timelineAssetIds->merge($createdAssetIds)->unique();
                    $weekData[$status] = $uniqueAssetIds->count();
                }
                $monthData["Week $week"] = $weekData;
            }
            
            $months[$monthName] = $monthData;
        } else {
            // Get last 3 months (default behavior) - show latest month first
            for ($i = 0; $i < 3; $i++) {
                $date = now()->subMonths($i);
                $monthName = $date->format('F Y');
                $monthData = [];
                
                // Get weeks in this month
                $startOfMonth = $date->startOfMonth()->copy();
                $endOfMonth = $date->endOfMonth()->copy();
                
                for ($week = 1; $week <= 4; $week++) {
                    $weekStart = $startOfMonth->copy()->addWeeks($week - 1);
                    $weekEnd = $weekStart->copy()->addDays(6);
                    
                    if ($weekEnd->gt($endOfMonth)) {
                        $weekEnd = $endOfMonth->copy();
                    }
                    
                    $weekData = [];
                    foreach ($statuses as $status) {
                        // Get unique asset IDs that changed to this movement during this week
                        $timelineAssetIds = \App\Models\AssetTimeline::where('action', 'updated')
                            ->whereJsonContains('new_values->movement', $status)
                            ->whereBetween('performed_at', [$weekStart, $weekEnd])
                            ->distinct('asset_id')
                            ->pluck('asset_id');
                        
                        // Get asset IDs created with this movement during this week
                        $createdAssetIds = \App\Models\Asset::where('movement', $status)
                            ->whereBetween('created_at', [$weekStart, $weekEnd])
                            ->pluck('id');
                        
                        // Combine and get unique count (same logic as detail view)
                        $uniqueAssetIds = $timelineAssetIds->merge($createdAssetIds)->unique();
                        $weekData[$status] = $uniqueAssetIds->count();
                    }
                    $monthData["Week $week"] = $weekData;
                }
                
                $months[$monthName] = $monthData;
            }
        }
        
        return ['statuses' => $statuses, 'months' => $months];
    }
    
    /**
     * Get monthly rollup data with percentages
     */
    private function getMonthlyRollup($filterMonth = null, $filterYear = null)
    {
        $statuses = ['Return', 'New Arrival', 'Deployed'];
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
                // Get unique asset IDs that changed to this movement during this month
                $timelineAssetIds = \App\Models\AssetTimeline::where('action', 'updated')
                    ->whereJsonContains('new_values->movement', $status)
                    ->whereBetween('performed_at', [$startOfMonth, $endOfMonth])
                    ->distinct('asset_id')
                    ->pluck('asset_id');
                
                // Get asset IDs created with this movement during this month
                $createdAssetIds = Asset::where('movement', $status)
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
            // Get last 3 months (default behavior) - show latest month first
            for ($i = 0; $i < 3; $i++) {
                $date = now()->subMonths($i);
                $monthName = $date->format('F');
                
                $startOfMonth = $date->startOfMonth();
                $endOfMonth = $date->endOfMonth();
                
                $monthData = [];
                $totalForMonth = 0;
                
                foreach ($statuses as $status) {
                    // Get unique asset IDs that changed to this movement during this month
                    $timelineAssetIds = \App\Models\AssetTimeline::where('action', 'updated')
                        ->whereJsonContains('new_values->movement', $status)
                        ->whereBetween('performed_at', [$startOfMonth, $endOfMonth])
                        ->distinct('asset_id')
                        ->pluck('asset_id');
                    
                    // Get asset IDs created with this movement during this month
                    $createdAssetIds = Asset::where('movement', $status)
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
        
        return ['statuses' => $statuses, 'months' => $months];
    }
    
    /**
     * Get chart data for management insights
     */
    private function getChartData($filterMonth = null, $filterYear = null)
    {
        $statuses = ['Available', 'Maintenance', 'Pending Confirmation', 'Active', 'For Disposal'];
        
        // Current status distribution (for pie chart)
        $currentDistribution = [];
        $total = Asset::count();
        
        foreach ($statuses as $status) {
            $count = Asset::where('status', $status)->count();
            $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
            $currentDistribution[$status] = [
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
                
                $count = Asset::where('status', 'Maintenance')
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
                
                $count = Asset::where('status', 'Maintenance')
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
        
        // Add weekly data for the chart
        $weeklyData = $this->getWeeklyChartData();
        
        return [
            'currentDistribution' => $currentDistribution,
            'problematicTrend' => $problematicTrend,
            'monthlyTotals' => $monthlyTotals,
            'statuses' => $statuses,
            'weeklyData' => $weeklyData
        ];
    }

    /**
     * Get weekly chart data for the dashboard
     */
    private function getWeeklyChartData()
    {
        $weeks = [];
        $deployedData = [];
        $maintenanceData = [];
        $pendingData = [];
        
        // Get last 8 weeks of data
        for ($i = 7; $i >= 0; $i--) {
            $startDate = now()->subWeeks($i)->startOfWeek();
            $endDate = now()->subWeeks($i)->endOfWeek();
            
            $weekLabel = $startDate->format('M d') . ' - ' . $endDate->format('M d');
            $weeks[] = $weekLabel;
            
            // Count assets by status for this week
            $deployed = Asset::where('status', 'Active')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
                
            $maintenance = Asset::where('status', 'Maintenance')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
                
            $pending = Asset::where('status', 'Pending Confirmation')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            
            $deployedData[] = $deployed;
            $maintenanceData[] = $maintenance;
            $pendingData[] = $pending;
        }
        
        return [
            'weeks' => $weeks,
            'deployed' => $deployedData,
            'maintenance' => $maintenanceData,
            'pending' => $pendingData
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
        
        // Use the exact same logic as the dashboard count to ensure consistency
        // Get unique asset IDs that changed to this movement during this week
        $timelineAssetIds = \App\Models\AssetTimeline::where('action', 'updated')
            ->whereJsonContains('new_values->movement', $status)
            ->whereBetween('performed_at', [$weekStart, $weekEnd])
            ->distinct('asset_id')
            ->pluck('asset_id');
        
        // Get asset IDs created with this movement during this week
        $createdAssetIds = \App\Models\Asset::where('movement', $status)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->pluck('id');
        
        // Combine and get unique asset IDs (same logic as dashboard count)
        $uniqueAssetIds = $timelineAssetIds->merge($createdAssetIds)->unique();
        
        // Get the actual assets with relationships
        $assets = \App\Models\Asset::with(['category', 'vendor', 'assignedUser', 'department'])
            ->whereIn('id', $uniqueAssetIds)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('dashboard.asset-movements', compact('assets', 'week', 'status', 'month', 'year', 'weekStart', 'weekEnd'));
    }

    /**
     * Get declined assets data for dashboard widget
     */
    private function getDeclinedAssetsData()
    {
        // Get all declined confirmations with asset, user, and relationships
        $declinedConfirmations = AssetAssignmentConfirmation::where('status', 'declined')
            ->with(['asset.category', 'user.department'])
            ->orderBy('declined_at', 'desc')
            ->get();

        // Calculate statistics
        $totalDeclined = $declinedConfirmations->count();
        
        // Count by severity
        $highSeverity = $declinedConfirmations->where('decline_severity', 'high')->count();
        $mediumSeverity = $declinedConfirmations->where('decline_severity', 'medium')->count();
        $lowSeverity = $declinedConfirmations->where('decline_severity', 'low')->count();
        
        // Count requiring follow-up
        $requiresFollowUp = $declinedConfirmations->where('follow_up_required', true)->count();
        
        // Get pending (unresolved) high-severity declines (declined in last 7 days and not reassigned)
        $pendingHighSeverity = $declinedConfirmations
            ->where('decline_severity', 'high')
            ->where('follow_up_required', true)
            ->filter(function ($confirmation) {
                // Check if declined within last 7 days and asset is still available (not reassigned)
                return $confirmation->declined_at >= now()->subDays(7) 
                    && $confirmation->asset->status === 'Available';
            });

        // Get recent declined assets (last 5)
        $recentDeclined = $declinedConfirmations->take(5);

        // Group by decline category
        $byCategory = $declinedConfirmations->groupBy('decline_category')->map(function ($group) {
            return $group->count();
        });

        // Calculate decline trend (last 30 days)
        $declineTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayName = $date->format('M d');
            
            $count = AssetAssignmentConfirmation::where('status', 'declined')
                ->whereDate('declined_at', $date->toDateString())
                ->count();
            
            $declineTrend[] = [
                'date' => $dayName,
                'count' => $count
            ];
        }

        return [
            'total' => $totalDeclined,
            'high_severity' => $highSeverity,
            'medium_severity' => $mediumSeverity,
            'low_severity' => $lowSeverity,
            'requires_follow_up' => $requiresFollowUp,
            'pending_high_severity' => $pendingHighSeverity,
            'recent' => $recentDeclined,
            'by_category' => $byCategory,
            'trend' => $declineTrend
        ];
    }
}
