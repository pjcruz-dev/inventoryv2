<?php

namespace App\Jobs;

use App\Models\AssetAssignmentConfirmation;
use App\Mail\AssetAssignmentConfirmation as AssetAssignmentConfirmationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendAssetConfirmationReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Find all pending confirmations that need follow-up reminders
        $pendingConfirmations = AssetAssignmentConfirmation::with(['asset', 'user'])
            ->where('status', 'pending')
            ->where('assigned_at', '<=', Carbon::now()->subDays(3))
            ->get();

        foreach ($pendingConfirmations as $confirmation) {
            try {
                // Check if it's time to send a reminder (every 3 days)
                $daysSinceAssignment = $confirmation->assigned_at->diffInDays(Carbon::now());
                $daysSinceLastReminder = $confirmation->last_reminder_sent_at 
                    ? $confirmation->last_reminder_sent_at->diffInDays(Carbon::now())
                    : $daysSinceAssignment;

                // Send reminder if it's been 3+ days since assignment and 3+ days since last reminder
                if ($daysSinceAssignment >= 3 && $daysSinceLastReminder >= 3) {
                    // Send follow-up email
                    Mail::to($confirmation->user->email)->send(
                        new AssetAssignmentConfirmationMail(
                            $confirmation->asset,
                            $confirmation->user,
                            $confirmation->confirmation_token,
                            true // isFollowUp = true
                        )
                    );

                    // Update the last reminder sent timestamp
                    $confirmation->update([
                        'last_reminder_sent_at' => Carbon::now(),
                        'reminder_count' => $confirmation->reminder_count + 1
                    ]);

                    Log::info('Asset confirmation reminder sent', [
                        'confirmation_id' => $confirmation->id,
                        'asset_tag' => $confirmation->asset->asset_tag,
                        'user_email' => $confirmation->user->email,
                        'reminder_count' => $confirmation->reminder_count
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to send asset confirmation reminder', [
                    'confirmation_id' => $confirmation->id,
                    'error' => $e->getMessage(),
                    'asset_tag' => $confirmation->asset->asset_tag ?? 'Unknown',
                    'user_email' => $confirmation->user->email ?? 'Unknown'
                ]);
            }
        }

        Log::info('Asset confirmation reminder job completed', [
            'processed_confirmations' => $pendingConfirmations->count(),
            'executed_at' => Carbon::now()->toDateTimeString()
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Asset confirmation reminder job failed', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
