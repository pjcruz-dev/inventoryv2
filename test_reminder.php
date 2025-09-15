<?php

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AssetAssignmentConfirmation;
use App\Http\Controllers\AssetAssignmentConfirmationController;
use Illuminate\Http\Request;

echo "=== Testing Reminder Functionality ===\n\n";

// Find the pending confirmation for LAP012
$confirmation = AssetAssignmentConfirmation::with(['asset', 'user'])
    ->whereHas('asset', function($query) {
        $query->where('asset_tag', 'LAP012');
    })
    ->where('status', 'pending')
    ->first();

if (!$confirmation) {
    echo "No pending confirmation found for LAP012\n";
    exit;
}

echo "Found confirmation:\n";
echo "ID: {$confirmation->id}\n";
echo "Asset: {$confirmation->asset->asset_tag}\n";
echo "User: {$confirmation->user->first_name} {$confirmation->user->last_name}\n";
echo "Status: {$confirmation->status}\n";
echo "isPending(): " . ($confirmation->isPending() ? 'true' : 'false') . "\n";
echo "Current reminder count: {$confirmation->reminder_count}\n";
echo "Last reminder sent: " . ($confirmation->last_reminder_sent_at ? $confirmation->last_reminder_sent_at : 'Never') . "\n\n";

// Test the status check that's causing the issue
if ($confirmation->status !== 'pending') {
    echo "ERROR: Status check failed - status is '{$confirmation->status}' but should be 'pending'\n";
    exit;
}

echo "Status check passed - proceeding with reminder update...\n";

// Simulate the reminder update
try {
    $oldReminderCount = $confirmation->reminder_count;
    
    $confirmation->update([
        'reminder_count' => $confirmation->reminder_count + 1,
        'last_reminder_sent_at' => now()
    ]);
    
    echo "SUCCESS: Reminder count updated from {$oldReminderCount} to {$confirmation->reminder_count}\n";
    echo "Last reminder sent at: {$confirmation->last_reminder_sent_at}\n";
    
} catch (Exception $e) {
    echo "ERROR: Failed to update reminder - {$e->getMessage()}\n";
}

echo "\nDone.\n";