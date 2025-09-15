<?php

// Test reminder functionality for all LAP012 confirmations
$host = 'localhost';
$dbname = 'inventoryv2';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Testing reminder functionality for all LAP012 confirmations...\n\n";
    
    // Get all confirmations for LAP012
    $stmt = $pdo->prepare("
        SELECT aac.id, aac.status, aac.reminder_count, aac.last_reminder_sent_at, a.asset_tag, u.first_name, u.last_name
        FROM asset_assignment_confirmations aac
        JOIN assets a ON aac.asset_id = a.id
        JOIN users u ON aac.user_id = u.id
        WHERE a.asset_tag = 'LAP012'
        ORDER BY aac.id DESC
    ");
    $stmt->execute();
    $confirmations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($confirmations) . " confirmations for LAP012:\n\n";
    
    foreach ($confirmations as $conf) {
        echo "Confirmation ID: {$conf['id']}\n";
        echo "- Asset: {$conf['asset_tag']}\n";
        echo "- User: {$conf['first_name']} {$conf['last_name']}\n";
        echo "- Status: {$conf['status']}\n";
        echo "- Reminder Count: {$conf['reminder_count']}\n";
        echo "- Last Reminder: {$conf['last_reminder_sent_at']}\n";
        
        if ($conf['status'] === 'pending') {
            echo "- ✅ CAN send reminder (status is pending)\n";
        } else {
            echo "- ❌ CANNOT send reminder (status is '{$conf['status']}', not 'pending')\n";
            echo "- This would show: 'Cannot send reminder for non-pending confirmation'\n";
        }
        echo "\n";
    }
    
    // Check if there are any confirmations with non-pending status
    $stmt = $pdo->prepare("
        SELECT aac.id, aac.status, a.asset_tag
        FROM asset_assignment_confirmations aac
        JOIN assets a ON aac.asset_id = a.id
        WHERE a.asset_tag = 'LAP012' AND aac.status != 'pending'
    ");
    $stmt->execute();
    $nonPendingConfirmations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($nonPendingConfirmations) > 0) {
        echo "\n*** POTENTIAL ISSUE ***\n";
        echo "Found non-pending confirmations for LAP012:\n";
        foreach ($nonPendingConfirmations as $conf) {
            echo "- Confirmation ID {$conf['id']}: Status = {$conf['status']}\n";
        }
        echo "\nIf you're trying to send a reminder for any of these, you'll get the error.\n";
    } else {
        echo "\n✅ All LAP012 confirmations are pending - reminders should work for all.\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}