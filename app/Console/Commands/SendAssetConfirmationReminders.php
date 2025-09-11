<?php

namespace App\Console\Commands;

use App\Jobs\SendAssetConfirmationReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendAssetConfirmationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asset:send-confirmation-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send follow-up reminder emails for pending asset assignment confirmations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting asset confirmation reminder process...');
        
        try {
            // Dispatch the job to handle sending reminders
            SendAssetConfirmationReminder::dispatch();
            
            $this->info('Asset confirmation reminder job dispatched successfully.');
            
            Log::info('Asset confirmation reminder command executed', [
                'executed_at' => now()->toDateTimeString(),
                'command' => $this->signature
            ]);
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to dispatch asset confirmation reminder job: ' . $e->getMessage());
            
            Log::error('Asset confirmation reminder command failed', [
                'error' => $e->getMessage(),
                'executed_at' => now()->toDateTimeString(),
                'command' => $this->signature
            ]);
            
            return Command::FAILURE;
        }
    }
}
