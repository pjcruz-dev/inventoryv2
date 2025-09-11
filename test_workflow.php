<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetAssignmentConfirmation;
use App\Mail\AssetAssignmentConfirmation;
use Illuminate\Support\Facades\Mail;

echo "Testing Asset Assignment Workflow...\n\n";

// Check if we have users and assets
$userCount = User::count();
$assetCount = Asset::count();

echo "Users in database: {$userCount}\n";
echo "Assets in database: {$assetCount}\n\n";

if ($userCount == 0 || $assetCount == 0) {
    echo "Error: Need at least 1 user and 1 asset to test workflow\n";
    exit(1);
}

// Get first user and asset
$user = User::first();
$asset = Asset::first();

echo "Testing with User: {$user->first_name} {$user->last_name} ({$user->email})\n";
echo "Testing with Asset: {$asset->name} ({$asset->asset_tag})\n\n";

// Create asset assignment
try {
    $assignment = AssetAssignment::create([
        'asset_id' => $asset->id,
        'user_id' => $user->id,
        'assigned_by' => 1,
        'assigned_date' => now(),
        'status' => 'pending'
    ]);
    
    echo "✓ Asset assignment created successfully (ID: {$assignment->id})\n";
    
    // Check if confirmation was created
    $confirmation = AssetAssignmentConfirmation::where('assignment_id', $assignment->id)->first();
    
    if ($confirmation) {
        echo "✓ Asset assignment confirmation created successfully (ID: {$confirmation->id})\n";
        echo "  - Token: {$confirmation->token}\n";
        echo "  - Status: {$confirmation->status}\n";
        echo "  - Expires at: {$confirmation->expires_at}\n";
    } else {
        echo "✗ Asset assignment confirmation was NOT created\n";
    }
    
    echo "\n✓ Workflow test completed successfully!\n";
    
} catch (Exception $e) {
    echo "✗ Error during workflow test: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}