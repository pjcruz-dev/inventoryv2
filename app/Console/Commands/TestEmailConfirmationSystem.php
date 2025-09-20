<?php

namespace App\Console\Commands;

use App\Models\AssetAssignmentConfirmation;
use App\Models\Asset;
use App\Models\User;
use App\Mail\AssetAssignmentConfirmation as AssetAssignmentConfirmationMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TestEmailConfirmationSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email-confirmation-system {--dry-run : Show what would be tested without sending emails}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the complete email confirmation system including reminders and auto-confirmation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No emails will be sent');
            $this->newLine();
        }

        $this->info('🧪 Testing Email Confirmation System...');
        $this->newLine();

        // Test 1: Check existing confirmations
        $this->testExistingConfirmations();

        // Test 2: Create test confirmation if none exist
        $testConfirmation = $this->createTestConfirmation($dryRun);

        // Test 3: Test reminder emails
        if ($testConfirmation) {
            $this->testReminderEmails($testConfirmation, $dryRun);
        }

        // Test 4: Test auto-confirmation logic
        $this->testAutoConfirmationLogic($dryRun);

        $this->newLine();
        $this->info('✅ Email confirmation system test completed!');
        
        if (!$dryRun) {
            $this->info('📧 Check your Mailpit interface at http://localhost:8025 to view sent emails');
        }

        return Command::SUCCESS;
    }

    /**
     * Test existing confirmations
     */
    private function testExistingConfirmations(): void
    {
        $this->info('📋 Checking existing confirmations...');
        
        $totalConfirmations = AssetAssignmentConfirmation::count();
        $pendingConfirmations = AssetAssignmentConfirmation::where('status', 'pending')->count();
        $confirmedConfirmations = AssetAssignmentConfirmation::where('status', 'confirmed')->count();
        $autoConfirmedConfirmations = 0;
        try {
            $autoConfirmedConfirmations = AssetAssignmentConfirmation::where('auto_confirmed', true)->count();
        } catch (\Exception $e) {
            $this->warn('⚠️  Auto-confirmation columns not yet migrated. Run: php artisan migrate');
        }

        $this->table(
            ['Status', 'Count'],
            [
                ['Total', $totalConfirmations],
                ['Pending', $pendingConfirmations],
                ['Confirmed', $confirmedConfirmations],
                ['Auto-Confirmed', $autoConfirmedConfirmations]
            ]
        );

        // Show pending confirmations with reminder counts
        if ($pendingConfirmations > 0) {
            $pendingConfirmations = AssetAssignmentConfirmation::with(['asset', 'user'])
                ->where('status', 'pending')
                ->get();

            $this->newLine();
            $this->info('📝 Pending Confirmations:');
            
            $headers = ['ID', 'Asset Tag', 'User', 'Reminders', 'Last Reminder', 'Days Since Last'];
            $rows = [];

            foreach ($pendingConfirmations as $confirmation) {
                $daysSinceLastReminder = $confirmation->last_reminder_sent_at 
                    ? $confirmation->last_reminder_sent_at->diffInDays(Carbon::now())
                    : $confirmation->assigned_at->diffInDays(Carbon::now());

                $rows[] = [
                    $confirmation->id,
                    $confirmation->asset->asset_tag ?? 'N/A',
                    $confirmation->user->first_name . ' ' . $confirmation->user->last_name,
                    $confirmation->reminder_count,
                    $confirmation->last_reminder_sent_at ? $confirmation->last_reminder_sent_at->format('Y-m-d H:i') : 'Never',
                    $daysSinceLastReminder
                ];
            }

            $this->table($headers, $rows);
        }

        $this->newLine();
    }

    /**
     * Create a test confirmation if none exist
     */
    private function createTestConfirmation(bool $dryRun): ?AssetAssignmentConfirmation
    {
        // Check if we have any assets and users
        $assetCount = Asset::count();
        $userCount = User::count();

        if ($assetCount === 0) {
            $this->warn('⚠️  No assets found. Please run AssetSeeder first.');
            return null;
        }

        if ($userCount === 0) {
            $this->warn('⚠️  No users found. Please run UserSeeder first.');
            return null;
        }

        // Check if we already have pending confirmations
        $existingPending = AssetAssignmentConfirmation::where('status', 'pending')->first();
        if ($existingPending) {
            $this->info('✅ Using existing pending confirmation for testing');
            return $existingPending;
        }

        // Create a test confirmation
        $asset = Asset::first();
        $user = User::first();

        if (!$asset || !$user) {
            $this->error('❌ Could not find asset or user for testing');
            return null;
        }

        $this->info('🔧 Creating test confirmation...');

        if (!$dryRun) {
            $testConfirmation = AssetAssignmentConfirmation::create([
                'asset_id' => $asset->id,
                'user_id' => $user->id,
                'confirmation_token' => AssetAssignmentConfirmation::generateToken(),
                'status' => 'pending',
                'assigned_at' => Carbon::now()->subDays(5), // 5 days ago
                'reminder_count' => 0,
                'last_reminder_sent_at' => null
            ]);

            $this->info("✅ Created test confirmation ID: {$testConfirmation->id}");
            $this->info("   Asset: {$asset->asset_tag}");
            $this->info("   User: {$user->first_name} {$user->last_name}");
            
            return $testConfirmation;
        } else {
            $this->info("✅ Would create test confirmation for:");
            $this->info("   Asset: {$asset->asset_tag}");
            $this->info("   User: {$user->first_name} {$user->last_name}");
            
            return null;
        }
    }

    /**
     * Test reminder emails
     */
    private function testReminderEmails(AssetAssignmentConfirmation $confirmation, bool $dryRun): void
    {
        $this->newLine();
        $this->info('📧 Testing reminder emails...');

        // Test initial confirmation email
        $this->testInitialConfirmationEmail($confirmation, $dryRun);

        // Test follow-up reminders
        $this->testFollowUpReminders($confirmation, $dryRun);
    }

    /**
     * Test initial confirmation email
     */
    private function testInitialConfirmationEmail(AssetAssignmentConfirmation $confirmation, bool $dryRun): void
    {
        $this->info('📨 Testing initial confirmation email...');

        if (!$dryRun) {
            try {
                Mail::to($confirmation->user->email)->send(
                    new AssetAssignmentConfirmationMail(
                        $confirmation->asset,
                        $confirmation->user,
                        $confirmation->confirmation_token,
                        false // isFollowUp = false
                    )
                );

                $this->info("✅ Initial confirmation email sent to: {$confirmation->user->email}");
            } catch (\Exception $e) {
                $this->error("❌ Failed to send initial confirmation email: " . $e->getMessage());
            }
        } else {
            $this->info("✅ Would send initial confirmation email to: {$confirmation->user->email}");
        }
    }

    /**
     * Test follow-up reminders
     */
    private function testFollowUpReminders(AssetAssignmentConfirmation $confirmation, bool $dryRun): void
    {
        $this->info('📨 Testing follow-up reminder emails...');

        for ($reminderNumber = 1; $reminderNumber <= 3; $reminderNumber++) {
            $this->info("   Testing {$reminderNumber}{$this->getOrdinalSuffix($reminderNumber)} reminder...");

            if (!$dryRun) {
                try {
                    // Update reminder count
                    $confirmation->update([
                        'reminder_count' => $reminderNumber,
                        'last_reminder_sent_at' => Carbon::now()
                    ]);

                    // Send follow-up email
                    Mail::to($confirmation->user->email)->send(
                        new AssetAssignmentConfirmationMail(
                            $confirmation->asset,
                            $confirmation->user,
                            $confirmation->confirmation_token,
                            true // isFollowUp = true
                        )
                    );

                    $this->info("   ✅ {$reminderNumber}{$this->getOrdinalSuffix($reminderNumber)} reminder sent to: {$confirmation->user->email}");
                } catch (\Exception $e) {
                    $this->error("   ❌ Failed to send {$reminderNumber}{$this->getOrdinalSuffix($reminderNumber)} reminder: " . $e->getMessage());
                }
            } else {
                $this->info("   ✅ Would send {$reminderNumber}{$this->getOrdinalSuffix($reminderNumber)} reminder to: {$confirmation->user->email}");
            }
        }
    }

    /**
     * Test auto-confirmation logic
     */
    private function testAutoConfirmationLogic(bool $dryRun): void
    {
        $this->newLine();
        $this->info('🤖 Testing auto-confirmation logic...');

        // Find confirmations that would be auto-confirmed
        $candidatesForAutoConfirmation = collect();
        try {
            $candidatesForAutoConfirmation = AssetAssignmentConfirmation::with(['asset', 'user'])
                ->where('status', 'pending')
                ->where('reminder_count', '>=', 3)
                ->where(function($query) {
                    $query->where('last_reminder_sent_at', '<=', Carbon::now()->subDay())
                          ->orWhereNull('last_reminder_sent_at');
                })
                ->get();
        } catch (\Exception $e) {
            $this->warn('⚠️  Auto-confirmation columns not yet migrated. Run: php artisan migrate');
            $this->info('📋 Auto-confirmation criteria:');
            $this->info('   - Status: pending');
            $this->info('   - Reminder count: >= 3');
            $this->info('   - Days since last reminder: >= 1');
            return;
        }

        if ($candidatesForAutoConfirmation->isEmpty()) {
            $this->info('ℹ️  No confirmations found that would be auto-confirmed');
            
            // Show what would trigger auto-confirmation
            $this->info('📋 Auto-confirmation criteria:');
            $this->info('   - Status: pending');
            $this->info('   - Reminder count: >= 3');
            $this->info('   - Days since last reminder: >= 1');
            
            return;
        }

        $this->info("🔍 Found {$candidatesForAutoConfirmation->count()} confirmation(s) that would be auto-confirmed:");

        $headers = ['ID', 'Asset Tag', 'User', 'Reminders', 'Last Reminder', 'Days Since Last'];
        $rows = [];

        foreach ($candidatesForAutoConfirmation as $confirmation) {
            $daysSinceLastReminder = $confirmation->last_reminder_sent_at 
                ? $confirmation->last_reminder_sent_at->diffInDays(Carbon::now())
                : $confirmation->assigned_at->diffInDays(Carbon::now());

            $rows[] = [
                $confirmation->id,
                $confirmation->asset->asset_tag ?? 'N/A',
                $confirmation->user->first_name . ' ' . $confirmation->user->last_name,
                $confirmation->reminder_count,
                $confirmation->last_reminder_sent_at ? $confirmation->last_reminder_sent_at->format('Y-m-d H:i') : 'Never',
                $daysSinceLastReminder
            ];
        }

        $this->table($headers, $rows);

        if (!$dryRun) {
            $this->newLine();
            $confirm = $this->confirm('Do you want to actually auto-confirm these assignments?', false);
            
            if ($confirm) {
                $autoConfirmedCount = 0;
                foreach ($candidatesForAutoConfirmation as $confirmation) {
                    try {
                        $updateData = [
                            'status' => 'confirmed',
                            'confirmed_at' => Carbon::now(),
                            'notes' => ($confirmation->notes ? $confirmation->notes . "\n\n" : '') . 
                                      "Test auto-confirmed by system on " . Carbon::now()->format('Y-m-d H:i:s') . 
                                      " after {$confirmation->reminder_count} follow-up reminders."
                        ];
                        
                        // Add auto-confirmation fields if they exist
                        try {
                            $updateData['auto_confirmed'] = true;
                            $updateData['auto_confirmation_reason'] = "Test auto-confirmed after {$confirmation->reminder_count} follow-up reminders";
                        } catch (\Exception $e) {
                            // Columns don't exist yet, skip auto-confirmation fields
                        }
                        
                        $confirmation->update($updateData);

                        $autoConfirmedCount++;
                        $this->info("✅ Auto-confirmed: Asset {$confirmation->asset->asset_tag} → {$confirmation->user->first_name} {$confirmation->user->last_name}");
                    } catch (\Exception $e) {
                        $this->error("❌ Failed to auto-confirm ID {$confirmation->id}: " . $e->getMessage());
                    }
                }
                
                $this->info("🎉 Auto-confirmed {$autoConfirmedCount} assignment(s)");
            } else {
                $this->info('ℹ️  Auto-confirmation skipped');
            }
        }
    }

    /**
     * Get ordinal suffix (1st, 2nd, 3rd, etc.)
     */
    private function getOrdinalSuffix(int $number): string
    {
        $suffixes = ['th', 'st', 'nd', 'rd'];
        $v = $number % 100;
        return $suffixes[($v - 20) % 10] ?? $suffixes[$v] ?? $suffixes[0];
    }
}
