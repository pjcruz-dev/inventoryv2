<?php

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AssetAssignmentConfirmation;

echo "=== Asset Assignment Confirmation Status Debug ===\n\n";

// Get all confirmations with their statuses
$confirmations = AssetAssignmentConfirmation::with(['asset', 'user'])
    ->orderBy('created_at', 'desc')
    ->take(10)
    ->get();

echo "Recent 10 confirmations:\n";
foreach ($confirmations as $confirmation) {
    echo "ID: {$confirmation->id}\n";
    echo "Asset: {$confirmation->asset->asset_tag}\n";
    echo "User: {$confirmation->user->first_name} {$confirmation->user->last_name}\n";
    echo "Status: '{$confirmation->status}'\n";
    echo "Created: {$confirmation->created_at}\n";
    echo "Assigned At: {$confirmation->assigned_at}\n";
    echo "Confirmed At: " . ($confirmation->confirmed_at ? $confirmation->confirmed_at : 'NULL') . "\n";
    echo "isPending(): " . ($confirmation->isPending() ? 'true' : 'false') . "\n";
    echo "---\n";
}

// Check for any non-standard status values
echo "\nAll unique status values in database:\n";
$statuses = AssetAssignmentConfirmation::distinct('status')->pluck('status');
foreach ($statuses as $status) {
    echo "- '{$status}'\n";
}

echo "\nDone.\n";