<?php

// Simple database check without Laravel bootstrap
$host = 'localhost';
$dbname = 'inventoryv2';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Checking asset assignment confirmations...\n\n";
    
    // Check confirmation ID 6 specifically
    $stmt = $pdo->prepare("SELECT id, asset_id, user_id, status, reminder_count, last_reminder_sent_at FROM asset_assignment_confirmations WHERE id = 6");
    $stmt->execute();
    $confirmation = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($confirmation) {
        echo "Confirmation ID 6 details:\n";
        echo "- ID: {$confirmation['id']}\n";
        echo "- Asset ID: {$confirmation['asset_id']}\n";
        echo "- User ID: {$confirmation['user_id']}\n";
        echo "- Status: {$confirmation['status']}\n";
        echo "- Reminder Count: {$confirmation['reminder_count']}\n";
        echo "- Last Reminder Sent: {$confirmation['last_reminder_sent_at']}\n\n";
        
        if ($confirmation['status'] !== 'pending') {
            echo "*** ISSUE FOUND ***\n";
            echo "The confirmation status is '{$confirmation['status']}', not 'pending'.\n";
            echo "This is why you're getting the 'Cannot send reminder for non-pending confirmation' error.\n\n";
        } else {
            echo "Status is 'pending' - reminder should work fine.\n\n";
        }
    } else {
        echo "Confirmation with ID 6 not found.\n\n";
    }
    
    // Check all confirmations for LAP012
    $stmt = $pdo->prepare("
        SELECT aac.id, aac.status, aac.reminder_count, a.asset_tag 
        FROM asset_assignment_confirmations aac
        JOIN assets a ON aac.asset_id = a.id
        WHERE a.asset_tag = 'LAP012'
        ORDER BY aac.id DESC
    ");
    $stmt->execute();
    $confirmations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "All confirmations for LAP012:\n";
    foreach ($confirmations as $conf) {
        echo "- Confirmation ID: {$conf['id']}, Status: {$conf['status']}, Reminder Count: {$conf['reminder_count']}\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}