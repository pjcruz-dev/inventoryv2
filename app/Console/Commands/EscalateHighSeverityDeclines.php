<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AssetAssignmentConfirmation;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\DeclineEscalationNotification;

class EscalateHighSeverityDeclines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'declines:escalate 
                            {--days=3 : Number of days before escalation}
                            {--dry-run : Run without sending notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Escalate unresolved high-severity declined asset assignments to management';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $days = $this->option('days');
        $dryRun = $this->option('dry-run');
        
        $this->info("🔍 Checking for unresolved high-severity declines older than {$days} days...");
        
        // Find high-severity declines that:
        // 1. Were declined more than X days ago
        // 2. Require follow-up
        // 3. Asset is still available (not reassigned)
        $unresolvedDeclines = AssetAssignmentConfirmation::where('status', 'declined')
            ->where('decline_severity', 'high')
            ->where('follow_up_required', true)
            ->whereDate('declined_at', '<=', now()->subDays($days))
            ->with(['asset.category', 'user.department'])
            ->get()
            ->filter(function ($confirmation) {
                // Only escalate if asset is still available (not reassigned)
                return $confirmation->asset->status === 'Available';
            });
        
        if ($unresolvedDeclines->count() === 0) {
            $this->info("✅ No unresolved high-severity declines found.");
            return Command::SUCCESS;
        }
        
        $this->warn("⚠️  Found {$unresolvedDeclines->count()} unresolved high-severity decline(s):");
        
        // Create table for display
        $tableData = [];
        foreach ($unresolvedDeclines as $decline) {
            $daysAgo = $decline->declined_at->diffInDays(now());
            $tableData[] = [
                $decline->id,
                $decline->asset->asset_tag,
                $decline->user->first_name . ' ' . $decline->user->last_name,
                $decline->getFormattedDeclineReason(),
                $daysAgo . ' days ago'
            ];
        }
        
        $this->table(
            ['ID', 'Asset Tag', 'User', 'Reason', 'Declined'],
            $tableData
        );
        
        if ($dryRun) {
            $this->comment("🔸 DRY RUN: No notifications will be sent.");
            return Command::SUCCESS;
        }
        
        // Find managers and super admins to notify
        $managementUsers = User::whereHas('role', function($query) {
            $query->whereIn('name', ['Manager', 'Super Admin', 'Admin']);
        })->get();
        
        if ($managementUsers->count() === 0) {
            $this->error("❌ No management users found to escalate to.");
            return Command::FAILURE;
        }
        
        $this->info("📧 Escalating to {$managementUsers->count()} management user(s)...");
        
        $progressBar = $this->output->createProgressBar($managementUsers->count());
        $progressBar->start();
        
        $escalatedCount = 0;
        foreach ($managementUsers as $manager) {
            try {
                // Create notification
                $notificationData = [
                    'type' => 'decline_escalation',
                    'title' => 'HIGH PRIORITY: Unresolved Declined Asset Assignments',
                    'message' => sprintf(
                        '%d high-severity declined asset assignment(s) require immediate attention. Assets have been unassigned for more than %d days.',
                        $unresolvedDeclines->count(),
                        $days
                    ),
                    'data' => [
                        'declined_count' => $unresolvedDeclines->count(),
                        'days_threshold' => $days,
                        'declined_assets' => $unresolvedDeclines->map(function($decline) {
                            return [
                                'confirmation_id' => $decline->id,
                                'asset_tag' => $decline->asset->asset_tag,
                                'asset_name' => $decline->asset->asset_name,
                                'user_name' => $decline->user->first_name . ' ' . $decline->user->last_name,
                                'decline_reason' => $decline->getFormattedDeclineReason(),
                                'declined_at' => $decline->declined_at->toISOString(),
                                'days_ago' => $decline->declined_at->diffInDays(now())
                            ];
                        })->toArray()
                    ]
                ];
                
                Notification::create(array_merge($notificationData, [
                    'notifiable_type' => 'App\Models\User',
                    'notifiable_id' => $manager->id
                ]));
                
                // Send email notification
                Mail::to($manager->email)->send(new DeclineEscalationNotification(
                    $unresolvedDeclines,
                    $days,
                    $manager
                ));
                
                $escalatedCount++;
            } catch (\Exception $e) {
                $this->error("\n❌ Failed to escalate to {$manager->email}: " . $e->getMessage());
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        // Mark declines as escalated
        foreach ($unresolvedDeclines as $decline) {
            $decline->update([
                'escalated' => true,
                'escalated_at' => now()
            ]);
        }
        
        $this->info("✅ Successfully escalated to {$escalatedCount} management user(s).");
        $this->comment("📊 Total declines processed: {$unresolvedDeclines->count()}");
        
        return Command::SUCCESS;
    }
}

