<?php

namespace App\Console\Commands;

use App\Models\AssetAssignmentConfirmation;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AutoConfirmAssetAssignments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asset:auto-confirm-assignments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically confirm asset assignments after 3 follow-up reminders and 1 day grace period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting automatic asset assignment confirmation process...');
        
        try {
        // Find all pending confirmations that have received 3+ reminders
        // and it's been at least 1 day since the last reminder
        $pendingConfirmations = collect();
        try {
            $pendingConfirmations = AssetAssignmentConfirmation::with(['asset', 'user'])
                ->where('status', 'pending')
                ->where('reminder_count', '>=', 3)
                ->where(function($query) {
                    $query->where('last_reminder_sent_at', '<=', Carbon::now()->subDay())
                          ->orWhereNull('last_reminder_sent_at');
                })
                ->get();
        } catch (\Exception $e) {
            $this->error('Database schema not ready for auto-confirmation. Please run: php artisan migrate');
            Log::error('Auto-confirmation command failed - missing database columns', [
                'error' => $e->getMessage(),
                'command' => $this->signature
            ]);
            return Command::FAILURE;
        }

            $autoConfirmedCount = 0;

            foreach ($pendingConfirmations as $confirmation) {
                try {
                    // Double-check: ensure it's been at least 1 day since last reminder
                    $daysSinceLastReminder = $confirmation->last_reminder_sent_at 
                        ? $confirmation->last_reminder_sent_at->diffInDays(Carbon::now())
                        : $confirmation->assigned_at->diffInDays(Carbon::now());

                    if ($daysSinceLastReminder >= 1) {
                        // Auto-confirm the assignment
                        $this->autoConfirmAssignment($confirmation);
                        $autoConfirmedCount++;
                        
                        $this->line("Auto-confirmed assignment: Asset {$confirmation->asset->asset_tag} → {$confirmation->user->first_name} {$confirmation->user->last_name}");
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to auto-confirm asset assignment', [
                        'confirmation_id' => $confirmation->id,
                        'asset_id' => $confirmation->asset_id,
                        'user_id' => $confirmation->user_id,
                        'error' => $e->getMessage()
                    ]);
                    
                    $this->error("Failed to auto-confirm assignment for asset {$confirmation->asset->asset_tag}: " . $e->getMessage());
                }
            }

            $this->info("Automatic confirmation process completed. {$autoConfirmedCount} assignments auto-confirmed.");
            
            Log::info('Asset assignment auto-confirmation command executed', [
                'executed_at' => now()->toDateTimeString(),
                'auto_confirmed_count' => $autoConfirmedCount,
                'command' => $this->signature
            ]);
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to execute auto-confirmation process: ' . $e->getMessage());
            
            Log::error('Asset assignment auto-confirmation command failed', [
                'error' => $e->getMessage(),
                'executed_at' => now()->toDateTimeString(),
                'command' => $this->signature
            ]);
            
            return Command::FAILURE;
        }
    }

    /**
     * Auto-confirm an asset assignment
     */
    private function autoConfirmAssignment(AssetAssignmentConfirmation $confirmation): void
    {
        // Update the confirmation status
        $confirmation->update([
            'status' => 'confirmed',
            'confirmed_at' => Carbon::now(),
            'auto_confirmed' => true,
            'auto_confirmation_reason' => "Auto-confirmed after {$confirmation->reminder_count} follow-up reminders with no response from user",
            'notes' => ($confirmation->notes ? $confirmation->notes . "\n\n" : '') . 
                      "Auto-confirmed by system on " . Carbon::now()->format('Y-m-d H:i:s') . 
                      " after {$confirmation->reminder_count} follow-up reminders with no response from user."
        ]);

        // Update the asset assignment status if needed
        $asset = $confirmation->asset;
        if ($asset->assigned_to === $confirmation->user_id && $asset->assigned_date) {
            // Asset is already assigned, just log the auto-confirmation
            Log::info('Asset assignment auto-confirmed', [
                'asset_id' => $asset->id,
                'asset_tag' => $asset->asset_tag,
                'user_id' => $confirmation->user_id,
                'user_name' => $confirmation->user->first_name . ' ' . $confirmation->user->last_name,
                'assigned_at' => $asset->assigned_date,
                'confirmed_at' => $confirmation->confirmed_at,
                'reminder_count' => $confirmation->reminder_count
            ]);
        }

        // Send notification email to admin about auto-confirmation
        $this->sendAutoConfirmationNotification($confirmation);
    }

    /**
     * Send notification to admin about auto-confirmation
     */
    private function sendAutoConfirmationNotification(AssetAssignmentConfirmation $confirmation): void
    {
        try {
            // Find admin users to notify
            $adminUsers = User::whereHas('roles', function($query) {
                $query->where('name', 'Admin');
            })->get();

            foreach ($adminUsers as $admin) {
                // Send notification email (you can implement this if needed)
                // For now, just log it
                Log::info('Auto-confirmation notification sent to admin', [
                    'admin_email' => $admin->email,
                    'asset_tag' => $confirmation->asset->asset_tag,
                    'user_name' => $confirmation->user->first_name . ' ' . $confirmation->user->last_name,
                    'reminder_count' => $confirmation->reminder_count
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send auto-confirmation notification', [
                'confirmation_id' => $confirmation->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
