<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AutomationService;

class RunAutomationTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'automation:run {--task=all : Specific task to run (all, maintenance, warranty, assignment, reports, cleanup)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run automated tasks for the inventory management system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $task = $this->option('task');
        $automationService = new AutomationService();

        $this->info('Starting automation tasks...');

        switch ($task) {
            case 'all':
                $this->runAllTasks($automationService);
                break;
            case 'maintenance':
                $this->runMaintenanceTasks($automationService);
                break;
            case 'warranty':
                $this->runWarrantyTasks($automationService);
                break;
            case 'assignment':
                $this->runAssignmentTasks($automationService);
                break;
            case 'reports':
                $this->runReportTasks($automationService);
                break;
            case 'cleanup':
                $this->runCleanupTasks($automationService);
                break;
            default:
                $this->error('Invalid task specified. Use: all, maintenance, warranty, assignment, reports, cleanup');
                return 1;
        }

        $this->info('Automation tasks completed successfully!');
        return 0;
    }

    /**
     * Run all automation tasks
     */
    private function runAllTasks(AutomationService $automationService)
    {
        $this->info('Running all automation tasks...');
        
        $this->line('Checking maintenance due...');
        $automationService->checkMaintenanceDue();
        
        $this->line('Checking asset warranty expiry...');
        $automationService->checkAssetWarrantyExpiry();
        
        $this->line('Checking assignment expiry...');
        $automationService->checkAssignmentExpiry();
        
        $this->line('Generating maintenance reports...');
        $automationService->generateMaintenanceReports();
        
        $this->line('Cleaning up old logs...');
        $automationService->cleanupOldLogs();
        
        $this->line('Checking for unused assets...');
        $automationService->checkUnusedAssets();
        
        $this->line('Generating system health report...');
        $automationService->generateSystemHealthReport();
    }

    /**
     * Run maintenance-related tasks
     */
    private function runMaintenanceTasks(AutomationService $automationService)
    {
        $this->info('Running maintenance tasks...');
        
        $this->line('Checking maintenance due...');
        $automationService->checkMaintenanceDue();
        
        $this->line('Sending maintenance reminders...');
        $automationService->sendMaintenanceReminders();
        
        $this->line('Generating maintenance reports...');
        $automationService->generateMaintenanceReports();
    }

    /**
     * Run warranty-related tasks
     */
    private function runWarrantyTasks(AutomationService $automationService)
    {
        $this->info('Running warranty tasks...');
        
        $this->line('Checking asset warranty expiry...');
        $automationService->checkAssetWarrantyExpiry();
    }

    /**
     * Run assignment-related tasks
     */
    private function runAssignmentTasks(AutomationService $automationService)
    {
        $this->info('Running assignment tasks...');
        
        $this->line('Checking assignment expiry...');
        $automationService->checkAssignmentExpiry();
    }

    /**
     * Run report-related tasks
     */
    private function runReportTasks(AutomationService $automationService)
    {
        $this->info('Running report tasks...');
        
        $this->line('Generating maintenance reports...');
        $automationService->generateMaintenanceReports();
        
        $this->line('Generating depreciation report...');
        $automationService->generateDepreciationReport();
        
        $this->line('Generating system health report...');
        $automationService->generateSystemHealthReport();
    }

    /**
     * Run cleanup tasks
     */
    private function runCleanupTasks(AutomationService $automationService)
    {
        $this->info('Running cleanup tasks...');
        
        $this->line('Cleaning up old logs...');
        $automationService->cleanupOldLogs();
        
        $this->line('Checking for unused assets...');
        $automationService->checkUnusedAssets();
    }
}
