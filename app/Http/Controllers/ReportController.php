<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\Computer;
use App\Models\Monitor;
use App\Models\Printer;
use App\Models\Peripheral;
use App\Models\User;
use App\Models\Department;
use App\Models\Vendor;
use App\Models\AssetAssignment;
use App\Models\Maintenance;
use App\Models\Disposal;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\AuditService;
use App\Services\CacheService;
use App\Services\PerformanceService;
use App\Helpers\CurrencyHelper;

class ReportController extends Controller
{
    /**
     * Display main reports dashboard
     */
    public function index()
    {
        $this->authorize('view_reports');
        
        // Get summary statistics
        $stats = $this->getReportStats();
        
        // Get recent reports
        $recentReports = $this->getRecentReports();
        
        // Get report categories
        $categories = $this->getReportCategories();
        
        return view('reports.index', compact('stats', 'recentReports', 'categories'));
    }
    
    /**
     * Asset Analytics Report
     */
    public function assetAnalytics(Request $request)
    {
        $this->authorize('view_reports');
        
        $dateFrom = $request->get('date_from', now()->subYear());
        $dateTo = $request->get('date_to', now());
        
        $analytics = [
            'total_assets' => Asset::count(),
            'active_assets' => Asset::where('status', 'active')->count(),
            'inactive_assets' => Asset::where('status', 'inactive')->count(),
            'disposed_assets' => Asset::where('status', 'disposed')->count(),
            'total_value' => Asset::sum('cost'),
            'average_value' => Asset::avg('cost'),
            'category_breakdown' => $this->getCategoryBreakdown(),
            'department_breakdown' => $this->getDepartmentBreakdown(),
            'vendor_breakdown' => $this->getVendorBreakdown(),
            'monthly_trends' => $this->getMonthlyTrends($dateFrom, $dateTo),
            'top_assets' => $this->getTopAssets(),
            'maintenance_summary' => $this->getMaintenanceSummary(),
            'disposal_summary' => $this->getDisposalSummary()
        ];
        
        return view('reports.asset-analytics', compact('analytics', 'dateFrom', 'dateTo'));
    }
    
    /**
     * User Activity Report
     */
    public function userActivity(Request $request)
    {
        $this->authorize('view_reports');
        
        $dateFrom = $request->get('date_from', now()->subMonth());
        $dateTo = $request->get('date_to', now());
        
        $userActivity = [
            'most_active_users' => $this->getMostActiveUsers($dateFrom, $dateTo),
            'login_patterns' => $this->getLoginPatterns($dateFrom, $dateTo),
            'department_activity' => $this->getDepartmentActivity($dateFrom, $dateTo),
            'action_breakdown' => $this->getActionBreakdown($dateFrom, $dateTo),
            'peak_hours' => $this->getPeakHours($dateFrom, $dateTo)
        ];
        
        return view('reports.user-activity', compact('userActivity', 'dateFrom', 'dateTo'));
    }
    
    /**
     * Financial Report
     */
    public function financial(Request $request)
    {
        $this->authorize('view_reports');
        
        $dateFrom = $request->get('date_from', now()->subYear());
        $dateTo = $request->get('date_to', now());
        
        $financial = [
            'total_investment' => Asset::sum('cost'),
            'monthly_investment' => $this->getMonthlyInvestment($dateFrom, $dateTo),
            'category_costs' => $this->getCategoryCosts(),
            'department_costs' => $this->getDepartmentCosts(),
            'vendor_costs' => $this->getVendorCosts(),
            'depreciation' => $this->getDepreciationData(),
            'maintenance_costs' => $this->getMaintenanceCosts($dateFrom, $dateTo),
            'disposal_value' => $this->getDisposalValue($dateFrom, $dateTo)
        ];
        
        return view('reports.financial', compact('financial', 'dateFrom', 'dateTo'));
    }
    
    /**
     * Maintenance Report
     */
    public function maintenance(Request $request)
    {
        $this->authorize('view_reports');
        
        $dateFrom = $request->get('date_from', now()->subYear());
        $dateTo = $request->get('date_to', now());
        
        $maintenance = [
            'total_maintenance' => Maintenance::count(),
            'pending_maintenance' => Maintenance::where('status', 'pending')->count(),
            'completed_maintenance' => Maintenance::where('status', 'completed')->count(),
            'overdue_maintenance' => Maintenance::where('status', 'overdue')->count(),
            'maintenance_trends' => $this->getMaintenanceTrends($dateFrom, $dateTo),
            'asset_maintenance' => $this->getAssetMaintenance(),
            'cost_analysis' => $this->getMaintenanceCostAnalysis($dateFrom, $dateTo),
            'technician_performance' => $this->getTechnicianPerformance($dateFrom, $dateTo)
        ];
        
        return view('reports.maintenance', compact('maintenance', 'dateFrom', 'dateTo'));
    }
    
    /**
     * Export report data
     */
    public function export(Request $request)
    {
        $this->authorize('export_reports');
        
        $reportType = $request->get('report_type');
        $format = $request->get('format', 'csv');
        
        // Log export action
        AuditService::logDataTransfer('export', $reportType, 0, [
            'format' => $format,
            'filters' => $request->all()
        ]);
        
        switch ($reportType) {
            case 'asset_analytics':
                return $this->exportAssetAnalytics($format, $request);
            case 'user_activity':
                return $this->exportUserActivity($format, $request);
            case 'financial':
                return $this->exportFinancial($format, $request);
            case 'maintenance':
                return $this->exportMaintenance($format, $request);
            default:
                return redirect()->back()->with('error', 'Invalid report type');
        }
    }
    
    /**
     * Get report statistics
     */
    private function getReportStats()
    {
        return [
            'total_assets' => Asset::count(),
            'total_users' => User::count(),
            'total_departments' => Department::count(),
            'total_vendors' => Vendor::count(),
            'active_assignments' => AssetAssignment::where('status', 'active')->count(),
            'pending_maintenance' => Maintenance::where('status', 'pending')->count(),
            'total_value' => Asset::sum('cost'),
            'reports_generated' => $this->getReportsGeneratedCount()
        ];
    }
    
    /**
     * Get recent reports
     */
    private function getRecentReports()
    {
        // This would typically come from a reports table
        return collect([
            ['name' => 'Asset Analytics', 'generated_at' => now()->subHours(2), 'type' => 'asset-analytics'],
            ['name' => 'User Activity', 'generated_at' => now()->subDays(1), 'type' => 'user-activity'],
            ['name' => 'Financial Summary', 'generated_at' => now()->subDays(3), 'type' => 'financial'],
            ['name' => 'Maintenance Report', 'generated_at' => now()->subWeek(), 'type' => 'maintenance']
        ]);
    }
    
    /**
     * Get report categories
     */
    private function getReportCategories()
    {
        return [
            'asset-analytics' => ['name' => 'Analytics', 'icon' => 'fas fa-chart-line', 'color' => 'primary'],
            'user-activity' => ['name' => 'User Activity', 'icon' => 'fas fa-users', 'color' => 'success'],
            'financial' => ['name' => 'Financial', 'icon' => 'fas fa-dollar-sign', 'color' => 'warning'],
            'maintenance' => ['name' => 'Maintenance', 'icon' => 'fas fa-wrench', 'color' => 'info'],
            'security' => ['name' => 'Security', 'icon' => 'fas fa-shield-alt', 'color' => 'danger']
        ];
    }
    
    /**
     * Get category breakdown
     */
    private function getCategoryBreakdown()
    {
        return Asset::with('category')
            ->select('category_id', DB::raw('count(*) as count'), DB::raw('sum(cost) as total_value'))
            ->groupBy('category_id')
            ->with('category')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category ? $item->category->name : 'Uncategorized',
                    'count' => $item->count,
                    'total_value' => $item->total_value
                ];
            });
    }
    
    /**
     * Get department breakdown
     */
    private function getDepartmentBreakdown()
    {
        return Asset::with('department')
            ->select('department_id', DB::raw('count(*) as count'), DB::raw('sum(cost) as total_value'))
            ->groupBy('department_id')
            ->with('department')
            ->get()
            ->map(function ($item) {
                return [
                    'department' => $item->department ? $item->department->name : 'Unassigned',
                    'count' => $item->count,
                    'total_value' => $item->total_value
                ];
            });
    }
    
    /**
     * Get vendor breakdown
     */
    private function getVendorBreakdown()
    {
        return Asset::with('vendor')
            ->select('vendor_id', DB::raw('count(*) as count'), DB::raw('sum(cost) as total_value'))
            ->groupBy('vendor_id')
            ->with('vendor')
            ->get()
            ->map(function ($item) {
                return [
                    'vendor' => $item->vendor ? $item->vendor->name : 'Unknown',
                    'count' => $item->count,
                    'total_value' => $item->total_value
                ];
            });
    }
    
    /**
     * Get monthly trends
     */
    private function getMonthlyTrends($dateFrom, $dateTo)
    {
        return Asset::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count'),
                DB::raw('sum(cost) as total_value')
            )
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
    
    /**
     * Get top assets by value
     */
    private function getTopAssets()
    {
        return Asset::with(['category', 'department', 'vendor'])
            ->orderBy('cost', 'desc')
            ->limit(10)
            ->get();
    }
    
    /**
     * Get maintenance summary
     */
    private function getMaintenanceSummary()
    {
        return [
            'total' => Maintenance::count(),
            'pending' => Maintenance::where('status', 'pending')->count(),
            'completed' => Maintenance::where('status', 'completed')->count(),
            'overdue' => Maintenance::where('status', 'overdue')->count(),
            'total_cost' => Maintenance::sum('cost')
        ];
    }
    
    /**
     * Get disposal summary
     */
    private function getDisposalSummary()
    {
        return [
            'total' => Disposal::count(),
            'total_value' => Disposal::sum('disposal_value'),
            'recent_disposals' => Disposal::with('asset')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
        ];
    }
    
    /**
     * Get most active users
     */
    private function getMostActiveUsers($dateFrom, $dateTo)
    {
        return User::withCount(['auditLogs' => function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('created_at', [$dateFrom, $dateTo]);
            }])
            ->orderBy('audit_logs_count', 'desc')
            ->limit(10)
            ->get();
    }
    
    /**
     * Get login patterns
     */
    private function getLoginPatterns($dateFrom, $dateTo)
    {
        return DB::table('audit_logs')
            ->where('action', 'auth_login')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as login_count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
    
    /**
     * Get department activity
     */
    private function getDepartmentActivity($dateFrom, $dateTo)
    {
        return Department::withCount(['users' => function ($query) use ($dateFrom, $dateTo) {
                $query->whereHas('auditLogs', function ($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('created_at', [$dateFrom, $dateTo]);
                });
            }])
            ->orderBy('users_count', 'desc')
            ->get();
    }
    
    /**
     * Get action breakdown
     */
    private function getActionBreakdown($dateFrom, $dateTo)
    {
        return DB::table('audit_logs')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->select('action', DB::raw('count(*) as count'))
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->get();
    }
    
    /**
     * Get peak hours
     */
    private function getPeakHours($dateFrom, $dateTo)
    {
        return DB::table('audit_logs')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('count(*) as activity_count')
            )
            ->groupBy('hour')
            ->orderBy('activity_count', 'desc')
            ->get();
    }
    
    /**
     * Get monthly investment
     */
    private function getMonthlyInvestment($dateFrom, $dateTo)
    {
        return Asset::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('sum(cost) as total_investment')
            )
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
    
    /**
     * Get category costs
     */
    private function getCategoryCosts()
    {
        return Asset::with('category')
            ->select('category_id', DB::raw('sum(cost) as total_cost'))
            ->groupBy('category_id')
            ->with('category')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category ? $item->category->name : 'Uncategorized',
                    'total_cost' => $item->total_cost
                ];
            });
    }
    
    /**
     * Get department costs
     */
    private function getDepartmentCosts()
    {
        return Asset::with('department')
            ->select('department_id', DB::raw('sum(cost) as total_cost'))
            ->groupBy('department_id')
            ->with('department')
            ->get()
            ->map(function ($item) {
                return [
                    'department' => $item->department ? $item->department->name : 'Unassigned',
                    'total_cost' => $item->total_cost
                ];
            });
    }
    
    /**
     * Get vendor costs
     */
    private function getVendorCosts()
    {
        return Asset::with('vendor')
            ->select('vendor_id', DB::raw('sum(cost) as total_cost'))
            ->groupBy('vendor_id')
            ->with('vendor')
            ->get()
            ->map(function ($item) {
                return [
                    'vendor' => $item->vendor ? $item->vendor->name : 'Unknown',
                    'total_cost' => $item->total_cost
                ];
            });
    }
    
    /**
     * Get depreciation data
     */
    private function getDepreciationData()
    {
        return Asset::select(
                'id',
                'name',
                'cost',
                'purchase_date',
                DB::raw('DATEDIFF(NOW(), purchase_date) as days_owned'),
                DB::raw('cost * 0.1 as annual_depreciation'),
                DB::raw('cost - (cost * 0.1 * DATEDIFF(NOW(), purchase_date) / 365) as current_value')
            )
            ->whereNotNull('purchase_date')
            ->get();
    }
    
    /**
     * Get maintenance costs
     */
    private function getMaintenanceCosts($dateFrom, $dateTo)
    {
        return Maintenance::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('sum(cost) as total_cost')
            )
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
    
    /**
     * Get disposal value
     */
    private function getDisposalValue($dateFrom, $dateTo)
    {
        return Disposal::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('sum(disposal_value) as total_value')
            )
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
    
    /**
     * Get maintenance trends
     */
    private function getMaintenanceTrends($dateFrom, $dateTo)
    {
        return Maintenance::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count'),
                DB::raw('sum(cost) as total_cost')
            )
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
    
    /**
     * Get asset maintenance
     */
    private function getAssetMaintenance()
    {
        return Asset::withCount('maintenances')
            ->with(['maintenances', 'category'])
            ->orderBy('maintenances_count', 'desc')
            ->limit(10)
            ->get();
    }
    
    /**
     * Get maintenance cost analysis
     */
    private function getMaintenanceCostAnalysis($dateFrom, $dateTo)
    {
        return Maintenance::with('asset')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->select(
                'asset_id',
                DB::raw('count(*) as maintenance_count'),
                DB::raw('sum(cost) as total_cost'),
                DB::raw('avg(cost) as avg_cost')
            )
            ->groupBy('asset_id')
            ->with('asset')
            ->orderBy('total_cost', 'desc')
            ->get();
    }
    
    /**
     * Get technician performance (using vendor instead of technician)
     */
    private function getTechnicianPerformance($dateFrom, $dateTo)
    {
        return Maintenance::with('vendor')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereNotNull('vendor_id')
            ->select(
                'vendor_id',
                DB::raw('count(*) as maintenance_count'),
                DB::raw('sum(cost) as total_cost'),
                DB::raw('avg(cost) as avg_cost')
            )
            ->groupBy('vendor_id')
            ->orderBy('maintenance_count', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'technician' => $item->vendor ? $item->vendor->name : 'Unknown Vendor',
                    'maintenance_count' => $item->maintenance_count,
                    'total_cost' => $item->total_cost,
                    'avg_cost' => $item->avg_cost
                ];
            });
    }
    
    /**
     * Get reports generated count
     */
    private function getReportsGeneratedCount()
    {
        // This would typically come from a reports table
        return 42; // Placeholder
    }
    
    /**
     * Export asset analytics
     */
    private function exportAssetAnalytics($format, $request)
    {
        $dateFrom = $request->get('date_from', now()->subYear());
        $dateTo = $request->get('date_to', now());
        
        $filename = 'asset_analytics_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($dateFrom, $dateTo) {
            $file = fopen('php://output', 'w');
            
            // Write CSV headers
            fputcsv($file, ['Asset Analytics Report', 'Generated: ' . now()->format('Y-m-d H:i:s')]);
            fputcsv($file, ['']);
            
            // Summary Statistics
            fputcsv($file, ['SUMMARY STATISTICS']);
            fputcsv($file, ['Metric', 'Value']);
            fputcsv($file, ['Total Assets', Asset::count()]);
            fputcsv($file, ['Active Assets', Asset::where('status', 'active')->count()]);
            fputcsv($file, ['Inactive Assets', Asset::where('status', 'inactive')->count()]);
            fputcsv($file, ['Disposed Assets', Asset::where('status', 'disposed')->count()]);
            fputcsv($file, ['Total Value', '₱' . number_format(Asset::sum('cost'), 2)]);
            fputcsv($file, ['Average Value', '₱' . number_format(Asset::avg('cost'), 2)]);
            fputcsv($file, ['']);
            
            // Category Breakdown
            fputcsv($file, ['CATEGORY BREAKDOWN']);
            fputcsv($file, ['Category', 'Count', 'Total Value']);
            $categoryBreakdown = $this->getCategoryBreakdown();
            foreach ($categoryBreakdown as $category) {
                fputcsv($file, [
                    $category['category'],
                    $category['count'],
                    '₱' . number_format($category['total_value'], 2)
                ]);
            }
            fputcsv($file, ['']);
            
            // Department Breakdown
            fputcsv($file, ['DEPARTMENT BREAKDOWN']);
            fputcsv($file, ['Department', 'Count', 'Total Value']);
            $departmentBreakdown = $this->getDepartmentBreakdown();
            foreach ($departmentBreakdown as $department) {
                fputcsv($file, [
                    $department['department'],
                    $department['count'],
                    '₱' . number_format($department['total_value'], 2)
                ]);
            }
            fputcsv($file, ['']);
            
            // Top Assets
            fputcsv($file, ['TOP ASSETS BY VALUE']);
            fputcsv($file, ['Asset Name', 'Category', 'Department', 'Cost']);
            $topAssets = $this->getTopAssets();
            foreach ($topAssets as $asset) {
                fputcsv($file, [
                    $asset->name,
                    $asset->category ? $asset->category->name : 'N/A',
                    $asset->department ? $asset->department->name : 'N/A',
                    '₱' . number_format($asset->cost ?? 0, 2)
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Export user activity
     */
    private function exportUserActivity($format, $request)
    {
        $dateFrom = $request->get('date_from', now()->subMonth());
        $dateTo = $request->get('date_to', now());
        
        $filename = 'user_activity_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($dateFrom, $dateTo) {
            $file = fopen('php://output', 'w');
            
            // Write CSV headers
            fputcsv($file, ['User Activity Report', 'Generated: ' . now()->format('Y-m-d H:i:s')]);
            fputcsv($file, ['Period: ' . $dateFrom->format('Y-m-d') . ' to ' . $dateTo->format('Y-m-d')]);
            fputcsv($file, ['']);
            
            // Most Active Users
            fputcsv($file, ['MOST ACTIVE USERS']);
            fputcsv($file, ['User Name', 'Email', 'Department', 'Activity Count', 'Last Activity']);
            $mostActiveUsers = $this->getMostActiveUsers($dateFrom, $dateTo);
            foreach ($mostActiveUsers as $user) {
                fputcsv($file, [
                    $user->name,
                    $user->email,
                    $user->department ? $user->department->name : 'N/A',
                    $user->audit_logs_count,
                    $user->auditLogs->first() ? $user->auditLogs->first()->created_at->format('Y-m-d H:i:s') : 'N/A'
                ]);
            }
            fputcsv($file, ['']);
            
            // Login Patterns
            fputcsv($file, ['LOGIN PATTERNS']);
            fputcsv($file, ['Date', 'Login Count']);
            $loginPatterns = $this->getLoginPatterns($dateFrom, $dateTo);
            foreach ($loginPatterns as $pattern) {
                fputcsv($file, [
                    $pattern->date,
                    $pattern->login_count
                ]);
            }
            fputcsv($file, ['']);
            
            // Department Activity
            fputcsv($file, ['DEPARTMENT ACTIVITY']);
            fputcsv($file, ['Department', 'Active Users']);
            $departmentActivity = $this->getDepartmentActivity($dateFrom, $dateTo);
            foreach ($departmentActivity as $department) {
                fputcsv($file, [
                    $department->name,
                    $department->users_count
                ]);
            }
            fputcsv($file, ['']);
            
            // Action Breakdown
            fputcsv($file, ['ACTION BREAKDOWN']);
            fputcsv($file, ['Action', 'Count', 'Percentage']);
            $actionBreakdown = $this->getActionBreakdown($dateFrom, $dateTo);
            $totalActions = $actionBreakdown->sum('count');
            foreach ($actionBreakdown as $action) {
                $percentage = $totalActions > 0 ? ($action->count / $totalActions) * 100 : 0;
                fputcsv($file, [
                    ucwords(str_replace('_', ' ', $action->action)),
                    $action->count,
                    number_format($percentage, 1) . '%'
                ]);
            }
            fputcsv($file, ['']);
            
            // Peak Hours
            fputcsv($file, ['PEAK HOURS']);
            fputcsv($file, ['Hour', 'Activity Count']);
            $peakHours = $this->getPeakHours($dateFrom, $dateTo);
            foreach ($peakHours as $hour) {
                fputcsv($file, [
                    $hour->hour . ':00',
                    $hour->activity_count
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Export financial data
     */
    private function exportFinancial($format, $request)
    {
        $dateFrom = $request->get('date_from', now()->subYear());
        $dateTo = $request->get('date_to', now());
        
        $filename = 'financial_report_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($dateFrom, $dateTo) {
            $file = fopen('php://output', 'w');
            
            // Write CSV headers
            fputcsv($file, ['Financial Report', 'Generated: ' . now()->format('Y-m-d H:i:s')]);
            fputcsv($file, ['Period: ' . $dateFrom->format('Y-m-d') . ' to ' . $dateTo->format('Y-m-d')]);
            fputcsv($file, ['']);
            
            // Financial Summary
            fputcsv($file, ['FINANCIAL SUMMARY']);
            fputcsv($file, ['Metric', 'Value']);
            fputcsv($file, ['Total Investment', '₱' . number_format(Asset::sum('cost'), 2)]);
            fputcsv($file, ['Maintenance Costs', '₱' . number_format($this->getMaintenanceCosts($dateFrom, $dateTo)->sum('total_cost'), 2)]);
            fputcsv($file, ['Disposal Value', '₱' . number_format($this->getDisposalValue($dateFrom, $dateTo)->sum('total_value'), 2)]);
            fputcsv($file, ['Total Depreciation', '₱' . number_format($this->getDepreciationData()->sum('depreciation_amount'), 2)]);
            fputcsv($file, ['']);
            
            // Monthly Investment
            fputcsv($file, ['MONTHLY INVESTMENT TRENDS']);
            fputcsv($file, ['Month', 'Total Investment']);
            $monthlyInvestment = $this->getMonthlyInvestment($dateFrom, $dateTo);
            foreach ($monthlyInvestment as $investment) {
                fputcsv($file, [
                    $investment->month,
                    '₱' . number_format($investment->total_investment, 2)
                ]);
            }
            fputcsv($file, ['']);
            
            // Category Costs
            fputcsv($file, ['CATEGORY COSTS']);
            fputcsv($file, ['Category', 'Total Cost']);
            $categoryCosts = $this->getCategoryCosts();
            foreach ($categoryCosts as $category) {
                fputcsv($file, [
                    $category['category'],
                    '₱' . number_format($category['total_cost'], 2)
                ]);
            }
            fputcsv($file, ['']);
            
            // Department Costs
            fputcsv($file, ['DEPARTMENT COSTS']);
            fputcsv($file, ['Department', 'Total Cost']);
            $departmentCosts = $this->getDepartmentCosts();
            foreach ($departmentCosts as $department) {
                fputcsv($file, [
                    $department['department'],
                    '₱' . number_format($department['total_cost'], 2)
                ]);
            }
            fputcsv($file, ['']);
            
            // Vendor Costs
            fputcsv($file, ['VENDOR COSTS']);
            fputcsv($file, ['Vendor', 'Total Cost']);
            $vendorCosts = $this->getVendorCosts();
            foreach ($vendorCosts as $vendor) {
                fputcsv($file, [
                    $vendor['vendor'],
                    '₱' . number_format($vendor['total_cost'], 2)
                ]);
            }
            fputcsv($file, ['']);
            
            // Asset Depreciation
            fputcsv($file, ['ASSET DEPRECIATION']);
            fputcsv($file, ['Asset Name', 'Original Cost', 'Days Owned', 'Annual Depreciation', 'Current Value', 'Depreciation Amount']);
            $depreciation = $this->getDepreciationData();
            foreach ($depreciation as $asset) {
                fputcsv($file, [
                    $asset->name,
                    '₱' . number_format($asset->cost, 2),
                    $asset->days_owned,
                    '₱' . number_format($asset->annual_depreciation, 2),
                    '₱' . number_format($asset->current_value, 2),
                    '₱' . number_format($asset->depreciation_amount, 2)
                ]);
            }
            fputcsv($file, ['']);
            
            // Maintenance Costs
            fputcsv($file, ['MAINTENANCE COSTS BY MONTH']);
            fputcsv($file, ['Month', 'Total Cost']);
            $maintenanceCosts = $this->getMaintenanceCosts($dateFrom, $dateTo);
            foreach ($maintenanceCosts as $cost) {
                fputcsv($file, [
                    $cost->month,
                    '₱' . number_format($cost->total_cost, 2)
                ]);
            }
            fputcsv($file, ['']);
            
            // Disposal Value
            fputcsv($file, ['DISPOSAL VALUE BY MONTH']);
            fputcsv($file, ['Month', 'Total Value']);
            $disposalValue = $this->getDisposalValue($dateFrom, $dateTo);
            foreach ($disposalValue as $value) {
                fputcsv($file, [
                    $value->month,
                    '₱' . number_format($value->total_value, 2)
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Export maintenance data
     */
    private function exportMaintenance($format, $request)
    {
        $dateFrom = $request->get('date_from', now()->subYear());
        $dateTo = $request->get('date_to', now());
        
        $filename = 'maintenance_report_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($dateFrom, $dateTo) {
            $file = fopen('php://output', 'w');
            
            // Write CSV headers
            fputcsv($file, ['Maintenance Report', 'Generated: ' . now()->format('Y-m-d H:i:s')]);
            fputcsv($file, ['Period: ' . $dateFrom->format('Y-m-d') . ' to ' . $dateTo->format('Y-m-d')]);
            fputcsv($file, ['']);
            
            // Maintenance Summary
            fputcsv($file, ['MAINTENANCE SUMMARY']);
            fputcsv($file, ['Metric', 'Count']);
            fputcsv($file, ['Total Maintenance', Maintenance::count()]);
            fputcsv($file, ['Pending Maintenance', Maintenance::where('status', 'pending')->count()]);
            fputcsv($file, ['Completed Maintenance', Maintenance::where('status', 'completed')->count()]);
            fputcsv($file, ['Overdue Maintenance', Maintenance::where('status', 'overdue')->count()]);
            fputcsv($file, ['Total Cost', '₱' . number_format(Maintenance::sum('cost'), 2)]);
            fputcsv($file, ['']);
            
            // Maintenance Trends
            fputcsv($file, ['MAINTENANCE TRENDS']);
            fputcsv($file, ['Month', 'Count', 'Total Cost']);
            $maintenanceTrends = $this->getMaintenanceTrends($dateFrom, $dateTo);
            foreach ($maintenanceTrends as $trend) {
                fputcsv($file, [
                    $trend->month,
                    $trend->count,
                    '₱' . number_format($trend->total_cost, 2)
                ]);
            }
            fputcsv($file, ['']);
            
            // Assets Requiring Most Maintenance
            fputcsv($file, ['ASSETS REQUIRING MOST MAINTENANCE']);
            fputcsv($file, ['Asset Name', 'Category', 'Maintenance Count', 'Total Cost', 'Last Maintenance']);
            $assetMaintenance = $this->getAssetMaintenance();
            foreach ($assetMaintenance as $asset) {
                fputcsv($file, [
                    $asset->name,
                    $asset->category ? $asset->category->name : 'N/A',
                    $asset->maintenances_count,
                    '₱' . number_format($asset->maintenances->sum('cost'), 2),
                    $asset->maintenances->count() > 0 ? $asset->maintenances->first()->created_at->format('Y-m-d') : 'N/A'
                ]);
            }
            fputcsv($file, ['']);
            
            // Maintenance Cost Analysis
            fputcsv($file, ['MAINTENANCE COST ANALYSIS']);
            fputcsv($file, ['Asset Name', 'Maintenance Count', 'Total Cost', 'Average Cost']);
            $costAnalysis = $this->getMaintenanceCostAnalysis($dateFrom, $dateTo);
            foreach ($costAnalysis as $analysis) {
                fputcsv($file, [
                    $analysis->asset ? $analysis->asset->name : 'Unknown Asset',
                    $analysis->maintenance_count,
                    '₱' . number_format($analysis->total_cost, 2),
                    '₱' . number_format($analysis->avg_cost, 2)
                ]);
            }
            fputcsv($file, ['']);
            
            // Vendor Performance
            fputcsv($file, ['VENDOR PERFORMANCE']);
            fputcsv($file, ['Vendor', 'Maintenance Count', 'Total Cost', 'Average Cost']);
            $technicianPerformance = $this->getTechnicianPerformance($dateFrom, $dateTo);
            foreach ($technicianPerformance as $vendor) {
                fputcsv($file, [
                    $vendor['technician'],
                    $vendor['maintenance_count'],
                    '₱' . number_format($vendor['total_cost'], 2),
                    '₱' . number_format($vendor['avg_cost'], 2)
                ]);
            }
            fputcsv($file, ['']);
            
            // Detailed Maintenance Records
            fputcsv($file, ['DETAILED MAINTENANCE RECORDS']);
            fputcsv($file, ['Asset Name', 'Issue Reported', 'Repair Action', 'Cost', 'Start Date', 'End Date', 'Status', 'Remarks']);
            $maintenanceRecords = Maintenance::with(['asset', 'vendor'])
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->orderBy('created_at', 'desc')
                ->get();
            
            foreach ($maintenanceRecords as $record) {
                fputcsv($file, [
                    $record->asset ? $record->asset->name : 'Unknown Asset',
                    $record->issue_reported,
                    $record->repair_action,
                    '₱' . number_format($record->cost ?? 0, 2),
                    $record->start_date ? $record->start_date->format('Y-m-d') : 'N/A',
                    $record->end_date ? $record->end_date->format('Y-m-d') : 'N/A',
                    $record->status,
                    $record->remarks
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
}
