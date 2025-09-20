<?php

namespace App\Console\Commands;

use App\Models\AssetAssignmentConfirmation;
use Illuminate\Console\Command;
use Carbon\Carbon;

class TestAutoConfirmation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asset:test-auto-confirmation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the auto-confirmation logic by showing pending confirmations that would be auto-confirmed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing auto-confirmation logic...');
        
        // Find all pending confirmations that have received 3+ reminders
        $pendingConfirmations = AssetAssignmentConfirmation::with(['asset', 'user'])
            ->where('status', 'pending')
            ->where('reminder_count', '>=', 3)
            ->where(function($query) {
                $query->where('last_reminder_sent_at', '<=', Carbon::now()->subDay())
                      ->orWhereNull('last_reminder_sent_at');
            })
            ->get();

        if ($pendingConfirmations->isEmpty()) {
            $this->info('No pending confirmations found that would be auto-confirmed.');
            return Command::SUCCESS;
        }

        $this->info("Found {$pendingConfirmations->count()} confirmations that would be auto-confirmed:");
        $this->newLine();

        $headers = ['ID', 'Asset Tag', 'User', 'Reminders', 'Last Reminder', 'Days Since Last Reminder'];
        $rows = [];

        foreach ($pendingConfirmations as $confirmation) {
            $daysSinceLastReminder = $confirmation->last_reminder_sent_at 
                ? $confirmation->last_reminder_sent_at->diffInDays(Carbon::now())
                : $confirmation->assigned_at->diffInDays(Carbon::now());

            $rows[] = [
                $confirmation->id,
                $confirmation->asset->asset_tag,
                $confirmation->user->first_name . ' ' . $confirmation->user->last_name,
                $confirmation->reminder_count,
                $confirmation->last_reminder_sent_at ? $confirmation->last_reminder_sent_at->format('Y-m-d H:i') : 'Never',
                $daysSinceLastReminder
            ];
        }

        $this->table($headers, $rows);

        $this->newLine();
        $this->info('To run the actual auto-confirmation, use: php artisan asset:auto-confirm-assignments');
        
        return Command::SUCCESS;
    }
}


