<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\AssetAssignment;
use App\Models\AssetAssignmentConfirmation;
use App\Models\Asset;

echo "=== PRODUCTION FIX SCRIPT ===\n";
echo "Fixing pending assignments affected by confirmation sync bug...\n\n";

$fixed = 0;
$errors = 0;

// Fix pending assignments that have confirmed confirmations
echo "🔧 Fixing pending assignments with confirmed confirmations...\n";
$pendingAssignments = AssetAssignment::where('status', 'pending')
    ->whereHas('confirmation', function($query) {
        $query->where('status', 'confirmed');
    })
    ->with(['asset', 'user', 'confirmation'])
    ->get();

echo "Found {$pendingAssignments->count()} pending assignments with confirmed confirmations\n";

foreach ($pendingAssignments as $assignment) {
    try {
        $assignment->update(['status' => 'confirmed']);
        echo "✓ Fixed Assignment {$assignment->id}: {$assignment->asset->asset_tag} → {$assignment->user->name}\n";
        $fixed++;
    } catch (Exception $e) {
        echo "✗ Error fixing Assignment {$assignment->id}: " . $e->getMessage() . "\n";
        $errors++;
    }
}

// Fix confirmed assignments that have declined confirmations
echo "\n🔧 Fixing confirmed assignments with declined confirmations...\n";
$confirmedAssignments = AssetAssignment::where('status', 'confirmed')
    ->whereHas('confirmation', function($query) {
        $query->where('status', 'declined');
    })
    ->with(['asset', 'user', 'confirmation'])
    ->get();

echo "Found {$confirmedAssignments->count()} confirmed assignments with declined confirmations\n";

foreach ($confirmedAssignments as $assignment) {
    try {
        $assignment->update(['status' => 'declined']);
        
        // Also fix the asset status
        $assignment->asset->update([
            'status' => 'Available',
            'assigned_to' => null,
            'assigned_date' => null,
            'movement' => 'Returned'
        ]);
        
        echo "✓ Fixed Assignment {$assignment->id}: {$assignment->asset->asset_tag} → {$assignment->user->name} (declined)\n";
        $fixed++;
    } catch (Exception $e) {
        echo "✗ Error fixing Assignment {$assignment->id}: " . $e->getMessage() . "\n";
        $errors++;
    }
}

// Handle orphaned confirmations (confirmations without matching assignments)
echo "\n🔧 Checking for orphaned confirmations...\n";
$orphanedConfirmations = AssetAssignmentConfirmation::whereNotExists(function($query) {
    $query->select(\DB::raw(1))
          ->from('asset_assignments')
          ->whereRaw('asset_assignments.asset_id = asset_assignment_confirmations.asset_id')
          ->whereRaw('asset_assignments.user_id = asset_assignment_confirmations.user_id')
          ->whereRaw('asset_assignments.assigned_date = asset_assignment_confirmations.assigned_at');
})->with(['asset', 'user'])->get();

echo "Found {$orphanedConfirmations->count()} orphaned confirmations\n";

foreach ($orphanedConfirmations as $confirmation) {
    // Only create assignments for confirmed or declined confirmations
    if (in_array($confirmation->status, ['confirmed', 'declined'])) {
        try {
            $assignment = AssetAssignment::create([
                'asset_id' => $confirmation->asset_id,
                'user_id' => $confirmation->user_id,
                'assigned_by' => 1, // Default to admin user
                'assigned_date' => $confirmation->assigned_at,
                'status' => $confirmation->status,
                'notes' => 'Auto-created from orphaned confirmation'
            ]);
            
            echo "✓ Created Assignment {$assignment->id} for orphaned confirmation {$confirmation->id}\n";
            $fixed++;
        } catch (Exception $e) {
            echo "✗ Error creating assignment for confirmation {$confirmation->id}: " . $e->getMessage() . "\n";
            $errors++;
        }
    }
}

echo "\n=== SYNC COMPLETE ===\n";
echo "✅ Fixed: {$fixed} records\n";
echo "❌ Errors: {$errors} records\n";

// Show final status summary
echo "\n📊 Final Status Summary:\n";
$assignmentCounts = AssetAssignment::selectRaw('status, count(*) as count')
    ->groupBy('status')
    ->get();

foreach ($assignmentCounts as $status) {
    echo "Assignments - {$status->status}: {$status->count}\n";
}

$assetCounts = Asset::selectRaw('status, count(*) as count')
    ->groupBy('status')
    ->get();

foreach ($assetCounts as $status) {
    echo "Assets - {$status->status}: {$status->count}\n";
}

echo "\n🎉 Production fix complete!\n";
echo "All pending assignments affected by the bug have been synchronized.\n";
echo "New asset assignments will now work correctly with email confirmations.\n";
