<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a request to simulate clicking the reminder button
$request = Illuminate\Http\Request::create(
    '/asset-assignment-confirmations/6/send-reminder',
    'POST',
    [],
    [],
    [],
    ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'] // Simulate AJAX request
);

// Add CSRF token
$request->headers->set('X-CSRF-TOKEN', 'test-token');

try {
    echo "Testing reminder functionality for confirmation ID 6...\n";
    
    // Check the confirmation status first
    $confirmation = \App\Models\AssetAssignmentConfirmation::find(6);
    if ($confirmation) {
        echo "Confirmation found:\n";
        echo "- ID: {$confirmation->id}\n";
        echo "- Asset Tag: {$confirmation->assetAssignment->asset->asset_tag}\n";
        echo "- Status: {$confirmation->status}\n";
        echo "- Is Pending: " . ($confirmation->isPending() ? 'Yes' : 'No') . "\n";
        echo "- Reminder Count: {$confirmation->reminder_count}\n";
        echo "- Last Reminder: {$confirmation->last_reminder_sent_at}\n\n";
        
        // Test the sendReminder method directly
        $controller = new \App\Http\Controllers\AssetAssignmentConfirmationController();
        
        echo "Testing sendReminder method...\n";
        
        if ($confirmation->status !== 'pending') {
            echo "ERROR: Confirmation status is '{$confirmation->status}', not 'pending'\n";
            echo "This is why you're getting the 'Cannot send reminder for non-pending confirmation' error.\n";
        } else {
            echo "Status is 'pending' - reminder should work.\n";
        }
        
    } else {
        echo "Confirmation with ID 6 not found.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}