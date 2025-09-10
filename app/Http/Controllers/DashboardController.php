<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\User;
use App\Models\Department;
use App\Models\Vendor;

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
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // Get basic statistics
        $totalAssets = Asset::count();
        $totalUsers = User::count();
        $totalDepartments = Department::count();
        $totalVendors = Vendor::count();
        
        // Get recent assets (last 5)
        $recentAssets = Asset::with('category')
            ->latest()
            ->take(5)
            ->get();
        
        // Calculate deployed assets percentage
        $deployedAssets = Asset::where('status', 'deployed')->count();
        $deployedAssetsPercentage = $totalAssets > 0 ? round(($deployedAssets / $totalAssets) * 100, 1) : 0;
        
        // Get filter parameters
        $filterMonth = $request->get('month');
        $filterYear = $request->get('year');
        
        // Get data for the three dashboard sections with filters
        $weeklyBreakdown = $this->getWeeklyBreakdown($filterMonth, $filterYear);
        $monthlyRollup = $this->getMonthlyRollup($filterMonth, $filterYear);
        $chartData = $this->getChartData($filterMonth, $filterYear);
        
        return view('dashboard', compact(
            'totalAssets',
            'totalUsers', 
            'totalDepartments',
            'totalVendors',
            'recentAssets',
            'deployedAssetsPercentage',
            'weeklyBreakdown',
            'monthlyRollup',
            'chartData'
        ));
    }
    
    /**
     * Get weekly breakdown data for asset lifecycle status
     */
    private function getWeeklyBreakdown($filterMonth = null, $filterYear = null)
    {
        $statuses = ['deployed', 'problematic', 'pending_confirm', 'returned', 'disposed', 'new_arrived'];
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
                    // Count assets that changed to this status during this week
                    $count = \App\Models\AssetTimeline::where('action', 'updated')
                        ->whereJsonContains('new_values->status', $status)
                        ->whereBetween('performed_at', [$weekStart, $weekEnd])
                        ->distinct('asset_id')
                        ->count('asset_id');
                    
                    // Also count assets created with this status during this week
                    $createdCount = \App\Models\Asset::where('status', $status)
                        ->whereBetween('created_at', [$weekStart, $weekEnd])
                        ->count();
                    
                    $weekData[$status] = $count + $createdCount;
                }
                $monthData["Week $week"] = $weekData;
            }
            
            $months[$monthName] = $monthData;
        } else {
            // Get last 3 months (default behavior)
            for ($i = 2; $i >= 0; $i--) {
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
                        // Count assets that changed to this status during this week
                        $count = \App\Models\AssetTimeline::where('action', 'updated')
                            ->whereJsonContains('new_values->status', $status)
                            ->whereBetween('performed_at', [$weekStart, $weekEnd])
                            ->distinct('asset_id')
                            ->count('asset_id');
                        
                        // Also count assets created with this status during this week
                        $createdCount = \App\Models\Asset::where('status', $status)
                            ->whereBetween('created_at', [$weekStart, $weekEnd])
                            ->count();
                        
                        $weekData[$status] = $count + $createdCount;
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
                // Count assets that changed to this status during this month
                $count = \App\Models\AssetTimeline::where('action', 'updated')
                    ->whereJsonContains('new_values->status', $status)
                    ->whereBetween('performed_at', [$startOfMonth, $endOfMonth])
                    ->distinct('asset_id')
                    ->count('asset_id');
                
                // Also count assets created with this status during this month
                $createdCount = Asset::where('status', $status)
                    ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->count();
                
                $totalCount = $count + $createdCount;
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
            // Get last 3 months (default behavior)
            for ($i = 2; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthName = $date->format('F');
                
                $startOfMonth = $date->startOfMonth();
                $endOfMonth = $date->endOfMonth();
                
                $monthData = [];
                $totalForMonth = 0;
                
                foreach ($statuses as $status) {
                    // Count assets that changed to this status during this month
                    $count = \App\Models\AssetTimeline::where('action', 'updated')
                        ->whereJsonContains('new_values->status', $status)
                        ->whereBetween('performed_at', [$startOfMonth, $endOfMonth])
                        ->distinct('asset_id')
                        ->count('asset_id');
                    
                    // Also count assets created with this status during this month
                    $createdCount = Asset::where('status', $status)
                        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                        ->count();
                    
                    $totalCount = $count + $createdCount;
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
        $statuses = ['deployed', 'problematic', 'pending_confirm', 'returned', 'disposed', 'new_arrived'];
        
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
            // Filter for specific month and year
            $date = now()->setYear((int)$filterYear)->setMonth((int)$filterMonth);
            
            // Monthly trends for problematic assets (single month)
            $problematicTrend = [];
            $monthName = $date->format('M Y');
            $count = Asset::where('status', 'problematic')
                ->whereYear('created_at', $filterYear)
                ->whereMonth('created_at', $filterMonth)
                ->count();
            $problematicTrend[] = [
                'month' => $monthName,
                'count' => $count
            ];
            
            // Monthly totals by status (single month)
            $monthlyTotals = [];
            $monthName = $date->format('F');
            $monthData = [];
            foreach ($statuses as $status) {
                $count = Asset::where('status', $status)
                    ->whereYear('created_at', $filterYear)
                    ->whereMonth('created_at', $filterMonth)
                    ->count();
                $monthData[$status] = $count;
            }
            $monthlyTotals[$monthName] = $monthData;
        } else {
            // Monthly trends for problematic assets (for trend line)
            $problematicTrend = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthName = $date->format('M Y');
                
                $count = Asset::where('status', 'problematic')
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
}
