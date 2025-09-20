<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Send asset confirmation reminders daily at 9:00 AM
        $schedule->command('asset:send-confirmation-reminders')
                 ->dailyAt('09:00')
                 ->withoutOverlapping()
                 ->runInBackground();
        
        // Auto-confirm asset assignments after 3 reminders and 1 day grace period
        // Run daily at 10:00 AM (after reminders are sent)
        $schedule->command('asset:auto-confirm-assignments')
                 ->dailyAt('10:00')
                 ->withoutOverlapping()
                 ->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}