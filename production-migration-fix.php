<?php
/**
 * Production Migration Fix
 * 
 * Use this if the auto-confirmation migration fails due to index name length
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🔧 Production Migration Fix for Auto-Confirmation Fields\n";
echo "======================================================\n\n";

try {
    // Check current state
    $hasAutoConfirmed = Schema::hasColumn('asset_assignment_confirmations', 'auto_confirmed');
    $hasAutoConfirmationReason = Schema::hasColumn('asset_assignment_confirmations', 'auto_confirmation_reason');
    
    echo "Current database state:\n";
    echo "auto_confirmed column: " . ($hasAutoConfirmed ? "✅ Exists" : "❌ Missing") . "\n";
    echo "auto_confirmation_reason column: " . ($hasAutoConfirmationReason ? "✅ Exists" : "❌ Missing") . "\n\n";
    
    if (!$hasAutoConfirmed || !$hasAutoConfirmationReason) {
        echo "Adding missing columns...\n";
        
        if (!$hasAutoConfirmed) {
            DB::statement("ALTER TABLE asset_assignment_confirmations ADD COLUMN auto_confirmed BOOLEAN DEFAULT FALSE AFTER status");
            echo "✅ Added auto_confirmed column\n";
        }
        
        if (!$hasAutoConfirmationReason) {
            DB::statement("ALTER TABLE asset_assignment_confirmations ADD COLUMN auto_confirmation_reason TEXT NULL AFTER auto_confirmed");
            echo "✅ Added auto_confirmation_reason column\n";
        }
        
        // Add index with short name
        try {
            DB::statement("ALTER TABLE asset_assignment_confirmations ADD INDEX aac_status_reminder_auto_idx (status, reminder_count, auto_confirmed)");
            echo "✅ Added index with short name\n";
        } catch (Exception $e) {
            echo "⚠️  Index might already exist: " . $e->getMessage() . "\n";
        }
    }
    
    // Mark migration as completed
    $migrationExists = DB::table('migrations')
        ->where('migration', '2025_01_20_000000_add_auto_confirmation_fields_to_asset_assignment_confirmations_table')
        ->exists();
    
    if (!$migrationExists) {
        DB::table('migrations')->insert([
            'migration' => '2025_01_20_000000_add_auto_confirmation_fields_to_asset_assignment_confirmations_table',
            'batch' => DB::table('migrations')->max('batch') + 1
        ]);
        echo "✅ Migration marked as completed\n";
    }
    
    echo "\n🎉 Production migration fix completed successfully!\n";
    echo "You can now run: php artisan test:email-confirmation-system --dry-run\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Please contact support with this error message.\n";
}
