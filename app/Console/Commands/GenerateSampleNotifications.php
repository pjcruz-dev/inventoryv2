<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateSampleNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:generate-sample {--count=10 : Number of notifications to generate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sample notifications for testing';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = $this->option('count');
        $users = User::all();

        if ($users->isEmpty()) {
            $this->error('No users found. Please create users first.');
            return 1;
        }

        $this->info("Generating {$count} sample notifications...");

        $notificationTypes = [
            'info' => 'Information',
            'success' => 'Success',
            'warning' => 'Warning',
            'error' => 'Error',
            'asset_update' => 'Asset Update',
            'user_action' => 'User Action',
            'system' => 'System',
            'maintenance' => 'Maintenance',
            'assignment' => 'Assignment',
            'disposal' => 'Disposal',
        ];

        $sampleMessages = [
            'info' => [
                'System maintenance scheduled for tonight',
                'New feature available in the dashboard',
                'Database backup completed successfully',
                'System performance report generated',
            ],
            'success' => [
                'Asset successfully created',
                'User account activated',
                'Data export completed',
                'Backup restored successfully',
            ],
            'warning' => [
                'Low disk space detected',
                'Scheduled maintenance in 1 hour',
                'Asset requires attention',
                'User login attempt failed multiple times',
            ],
            'error' => [
                'Failed to process asset data',
                'Database connection error',
                'File upload failed',
                'System error occurred',
            ],
            'asset_update' => [
                'Asset status changed to Active',
                'Asset assigned to new user',
                'Asset maintenance completed',
                'Asset location updated',
            ],
            'user_action' => [
                'User logged in from new device',
                'User profile updated',
                'User password changed',
                'User role modified',
            ],
            'system' => [
                'System configuration updated',
                'New user registered',
                'System backup started',
                'Log files rotated',
            ],
            'maintenance' => [
                'Scheduled maintenance in 30 minutes',
                'Maintenance window extended',
                'Maintenance completed successfully',
                'Emergency maintenance required',
            ],
            'assignment' => [
                'Asset assignment pending approval',
                'Assignment request approved',
                'Assignment request declined',
                'Assignment transferred',
            ],
            'disposal' => [
                'Asset marked for disposal',
                'Disposal request approved',
                'Asset disposed successfully',
                'Disposal audit completed',
            ],
        ];

        for ($i = 0; $i < $count; $i++) {
            $user = $users->random();
            $type = array_rand($notificationTypes);
            $title = $notificationTypes[$type];
            $message = $sampleMessages[$type][array_rand($sampleMessages[$type])];
            
            $isUrgent = rand(1, 10) === 1; // 10% chance of being urgent
            $expiresAt = rand(1, 3) === 1 ? Carbon::now()->addDays(rand(1, 7)) : null;
            
            $actionUrl = rand(1, 2) === 1 ? route('assets.index') : null;
            $actionText = $actionUrl ? 'View Assets' : null;

            $this->notificationService->create(
                $type,
                $title,
                $message,
                $user,
                [
                    'generated_at' => now()->toISOString(),
                    'sample' => true,
                ],
                $isUrgent,
                $actionUrl,
                $actionText,
                $expiresAt
            );
        }

        $this->info("Successfully generated {$count} sample notifications!");
        return 0;
    }
}