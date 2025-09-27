<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Maintenance;
use App\Models\AssetAssignment;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AutomationService
{
    /**
     * Run all automated tasks
     */
    public function runAllTasks()
    {
        $this->checkMaintenanceDue();
        $this->checkAssetWarrantyExpiry();
        $this->checkAssignmentExpiry();
        $this->generateMaintenanceReports();
        $this->cleanupOldLogs();
        
        Log::info('Automation tasks completed successfully');
    }

    /**
     * Check for maintenance due
     */
    public function checkMaintenanceDue()
    {
        $dueMaintenance = Maintenance::where('status', 'pending')
            ->where('scheduled_date', '<=', now())
            ->get();

        foreach ($dueMaintenance as $maintenance) {
            // Update status to overdue if past due date
            if ($maintenance->scheduled_date < now()) {
                $maintenance->update(['status' => 'overdue']);
                
                // Log the status change
                AuditLog::create([
                    'user_id' => null, // System action
                    'action' => 'maintenance_overdue',
                    'model_type' => 'App\Models\Maintenance',
                    'model_id' => $maintenance->id,
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'System Automation',
                    'details' => [
                        'maintenance_id' => $maintenance->id,
                        'asset_id' => $maintenance->asset_id,
                        'scheduled_date' => $maintenance->scheduled_date,
                        'overdue_days' => now()->diffInDays($maintenance->scheduled_date)
                    ],
                    'timestamp' => now()
                ]);
            }
        }

        Log::info("Checked {count} maintenance records for due status", ['count' => $dueMaintenance->count()]);
    }

    /**
     * Check for asset warranty expiry
     */
    public function checkAssetWarrantyExpiry()
    {
        $expiringAssets = Asset::whereNotNull('warranty_end_date')
            ->where('warranty_end_date', '<=', now()->addDays(30))
            ->where('warranty_end_date', '>', now())
            ->get();

        foreach ($expiringAssets as $asset) {
            // Log warranty expiry warning
            AuditLog::create([
                'user_id' => null, // System action
                'action' => 'warranty_expiring',
                'model_type' => 'App\Models\Asset',
                'model_id' => $asset->id,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'System Automation',
                'details' => [
                    'asset_id' => $asset->id,
                    'asset_name' => $asset->name,
                    'warranty_end_date' => $asset->warranty_end_date,
                    'days_remaining' => now()->diffInDays($asset->warranty_end_date, false)
                ],
                'timestamp' => now()
            ]);
        }

        Log::info("Checked {count} assets for warranty expiry", ['count' => $expiringAssets->count()]);
    }

    /**
     * Check for assignment expiry
     */
    public function checkAssignmentExpiry()
    {
        $expiringAssignments = AssetAssignment::where('status', 'active')
            ->whereNotNull('end_date')
            ->where('end_date', '<=', now()->addDays(7))
            ->where('end_date', '>', now())
            ->get();

        foreach ($expiringAssignments as $assignment) {
            // Log assignment expiry warning
            AuditLog::create([
                'user_id' => null, // System action
                'action' => 'assignment_expiring',
                'model_type' => 'App\Models\AssetAssignment',
                'model_id' => $assignment->id,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'System Automation',
                'details' => [
                    'assignment_id' => $assignment->id,
                    'asset_id' => $assignment->asset_id,
                    'user_id' => $assignment->user_id,
                    'end_date' => $assignment->end_date,
                    'days_remaining' => now()->diffInDays($assignment->end_date, false)
                ],
                'timestamp' => now()
            ]);
        }

        Log::info("Checked {count} assignments for expiry", ['count' => $expiringAssignments->count()]);
    }

    /**
     * Generate maintenance reports
     */
    public function generateMaintenanceReports()
    {
        $reportData = [
            'total_maintenance' => Maintenance::count(),
            'pending_maintenance' => Maintenance::where('status', 'pending')->count(),
            'overdue_maintenance' => Maintenance::where('status', 'overdue')->count(),
            'completed_this_month' => Maintenance::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->count(),
            'total_cost_this_month' => Maintenance::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->sum('cost')
        ];

        // Log the report generation
        AuditLog::create([
            'user_id' => null, // System action
            'action' => 'maintenance_report_generated',
            'model_type' => 'App\Models\Maintenance',
            'model_id' => null,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'System Automation',
            'details' => $reportData,
            'timestamp' => now()
        ]);

        Log::info("Generated maintenance report", $reportData);
    }

    /**
     * Cleanup old logs
     */
    public function cleanupOldLogs()
    {
        $retentionDays = config('security.audit_logging.retention_days', 365);
        $cutoffDate = now()->subDays($retentionDays);
        
        $deletedCount = AuditLog::where('created_at', '<', $cutoffDate)->delete();
        
        if ($deletedCount > 0) {
            Log::info("Cleaned up {count} old audit logs", ['count' => $deletedCount]);
        }
    }

    /**
     * Send maintenance reminders
     */
    public function sendMaintenanceReminders()
    {
        $overdueMaintenance = Maintenance::where('status', 'overdue')
            ->with(['asset', 'assignedTo'])
            ->get();

        foreach ($overdueMaintenance as $maintenance) {
            if ($maintenance->assignedTo) {
                $this->sendMaintenanceReminderEmail($maintenance);
            }
        }

        Log::info("Sent maintenance reminders for {count} overdue items", ['count' => $overdueMaintenance->count()]);
    }

    /**
     * Send maintenance reminder email
     */
    private function sendMaintenanceReminderEmail($maintenance)
    {
        try {
            $user = $maintenance->assignedTo;
            $asset = $maintenance->asset;
            
            Mail::send('emails.maintenance-reminder', [
                'maintenance' => $maintenance,
                'asset' => $asset,
                'user' => $user
            ], function ($message) use ($user, $maintenance) {
                $message->to($user->email, $user->name)
                    ->subject('Maintenance Reminder: ' . $maintenance->title);
            });

            Log::info("Sent maintenance reminder email to {user}", ['user' => $user->email]);
        } catch (\Exception $e) {
            Log::error("Failed to send maintenance reminder email: {error}", ['error' => $e->getMessage()]);
        }
    }

    /**
     * Generate asset depreciation report
     */
    public function generateDepreciationReport()
    {
        $assets = Asset::whereNotNull('purchase_date')
            ->whereNotNull('cost')
            ->get();

        $depreciationData = $assets->map(function ($asset) {
            $daysOwned = now()->diffInDays($asset->purchase_date);
            $annualDepreciation = $asset->cost * 0.1; // 10% annual depreciation
            $currentValue = max(0, $asset->cost - ($annualDepreciation * $daysOwned / 365));
            
            return [
                'asset_id' => $asset->id,
                'asset_name' => $asset->name,
                'purchase_date' => $asset->purchase_date,
                'original_cost' => $asset->cost,
                'days_owned' => $daysOwned,
                'annual_depreciation' => $annualDepreciation,
                'current_value' => $currentValue,
                'depreciation_amount' => $asset->cost - $currentValue
            ];
        });

        // Log the depreciation report
        AuditLog::create([
            'user_id' => null, // System action
            'action' => 'depreciation_report_generated',
            'model_type' => 'App\Models\Asset',
            'model_id' => null,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'System Automation',
            'details' => [
                'total_assets' => $assets->count(),
                'total_original_value' => $assets->sum('cost'),
                'total_current_value' => $depreciationData->sum('current_value'),
                'total_depreciation' => $depreciationData->sum('depreciation_amount')
            ],
            'timestamp' => now()
        ]);

        Log::info("Generated depreciation report for {count} assets", ['count' => $assets->count()]);
    }

    /**
     * Check for unused assets
     */
    public function checkUnusedAssets()
    {
        $unusedAssets = Asset::where('status', 'active')
            ->whereDoesntHave('assignments', function ($query) {
                $query->where('status', 'active');
            })
            ->get();

        foreach ($unusedAssets as $asset) {
            // Log unused asset
            AuditLog::create([
                'user_id' => null, // System action
                'action' => 'unused_asset_detected',
                'model_type' => 'App\Models\Asset',
                'model_id' => $asset->id,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'System Automation',
                'details' => [
                    'asset_id' => $asset->id,
                    'asset_name' => $asset->name,
                    'asset_value' => $asset->cost,
                    'days_unused' => now()->diffInDays($asset->updated_at)
                ],
                'timestamp' => now()
            ]);
        }

        Log::info("Checked for unused assets: {count} found", ['count' => $unusedAssets->count()]);
    }

    /**
     * Generate system health report
     */
    public function generateSystemHealthReport()
    {
        $healthData = [
            'total_assets' => Asset::count(),
            'active_assets' => Asset::where('status', 'active')->count(),
            'total_users' => User::count(),
            'active_assignments' => AssetAssignment::where('status', 'active')->count(),
            'pending_maintenance' => Maintenance::where('status', 'pending')->count(),
            'overdue_maintenance' => Maintenance::where('status', 'overdue')->count(),
            'total_audit_logs' => AuditLog::count(),
            'recent_activity' => AuditLog::where('created_at', '>=', now()->subDay())->count()
        ];

        // Log the system health report
        AuditLog::create([
            'user_id' => null, // System action
            'action' => 'system_health_report_generated',
            'model_type' => 'System',
            'model_id' => null,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'System Automation',
            'details' => $healthData,
            'timestamp' => now()
        ]);

        Log::info("Generated system health report", $healthData);
    }
}
