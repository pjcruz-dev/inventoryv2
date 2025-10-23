<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AssetAssignmentConfirmation;

class FixLegacyDeclinedAssets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'declines:fix-legacy 
                            {--dry-run : Preview changes without applying them}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix legacy declined assets that don\'t have decline reasons or severity levels';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn("🔸 DRY RUN MODE: No changes will be saved to database");
        }
        
        $this->info("🔍 Searching for legacy declined assets without proper decline data...");
        
        // Find all declined confirmations without decline reasons
        $legacyDeclines = AssetAssignmentConfirmation::where('status', 'declined')
            ->whereNull('decline_reason')
            ->with(['asset', 'user'])
            ->get();
        
        if ($legacyDeclines->count() === 0) {
            $this->info("✅ No legacy declined assets found. All declines have proper data!");
            return Command::SUCCESS;
        }
        
        $this->warn("⚠️  Found {$legacyDeclines->count()} legacy declined asset(s) without proper decline data:");
        
        // Display table of legacy declines
        $tableData = [];
        foreach ($legacyDeclines as $decline) {
            $tableData[] = [
                $decline->id,
                $decline->asset->asset_tag ?? 'N/A',
                $decline->user->first_name . ' ' . $decline->user->last_name,
                $decline->declined_at ? $decline->declined_at->format('Y-m-d H:i') : 'Unknown',
                $decline->decline_reason ?? 'NULL',
                $decline->decline_severity ?? 'NULL'
            ];
        }
        
        $this->table(
            ['ID', 'Asset Tag', 'User', 'Declined At', 'Reason', 'Severity'],
            $tableData
        );
        
        if ($dryRun) {
            $this->comment("\n📋 Changes that would be applied:");
            $this->info("  • decline_reason: 'other_reason'");
            $this->info("  • decline_category: 'other'");
            $this->info("  • decline_severity: 'low'");
            $this->info("  • follow_up_required: false");
            $this->info("  • decline_comments: 'Legacy decline - no reason was provided when this asset was declined.'");
            $this->newLine();
            $this->comment("Run without --dry-run to apply changes.");
            return Command::SUCCESS;
        }
        
        // Confirm before proceeding
        if (!$this->confirm("Do you want to update these {$legacyDeclines->count()} declined asset(s)?", true)) {
            $this->info("❌ Operation cancelled.");
            return Command::SUCCESS;
        }
        
        $this->info("🔧 Updating legacy declined assets...");
        $progressBar = $this->output->createProgressBar($legacyDeclines->count());
        $progressBar->start();
        
        $updatedCount = 0;
        foreach ($legacyDeclines as $decline) {
            try {
                $decline->update([
                    'decline_reason' => 'other_reason',
                    'decline_category' => 'other',
                    'decline_severity' => 'low',
                    'follow_up_required' => false,
                    'decline_comments' => 'Legacy decline - no reason was provided when this asset was declined.'
                ]);
                
                $updatedCount++;
            } catch (\Exception $e) {
                $this->newLine(2);
                $this->error("Failed to update decline ID {$decline->id}: " . $e->getMessage());
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        $this->info("✅ Successfully updated {$updatedCount} out of {$legacyDeclines->count()} declined asset(s)!");
        
        // Show summary
        $this->newLine();
        $this->comment("📊 Summary:");
        $this->info("  • All legacy declines now have:");
        $this->info("    - Reason: 'Other reason (please specify below)'");
        $this->info("    - Category: 'Other'");
        $this->info("    - Severity: 'LOW'");
        $this->info("    - Follow-up: Not required");
        $this->newLine();
        $this->info("💡 These assets will now appear correctly in your dashboard widget!");
        
        return Command::SUCCESS;
    }
}

