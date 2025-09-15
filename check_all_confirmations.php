<?php

// Check all confirmations to find any non-pending ones
$host = 'localhost';
$dbname = 'inventoryv2';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Checking all asset assignment confirmations...\n\n";
    
    // Get all confirmations with their status
    $stmt = $pdo->prepare("
        SELECT aac.id, aac.status, aac.reminder_count, a.asset_tag, u.first_name, u.last_name,
               aac.created_at, aac.confirmed_at
        FROM asset_assignment_confirmations aac
        JOIN assets a ON aac.asset_id = a.id
        JOIN users u ON aac.user_id = u.id
        ORDER BY aac.created_at DESC
        LIMIT 20
    ");
    $stmt->execute();
    $confirmations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Recent 20 confirmations:\n\n";
    
    foreach ($confirmations as $conf) {
        echo "ID: {$conf['id']} | Asset: {$conf['asset_tag']} | User: {$conf['first_name']} {$conf['last_name']} | Status: {$conf['status']} | Created: {$conf['created_at']}";
        if ($conf['confirmed_at']) {
            echo " | Confirmed: {$conf['confirmed_at']}";
        }
        echo "\n";
        
        if ($conf['status'] !== 'pending') {
            echo "  ⚠️  This confirmation would show 'Cannot send reminder for non-pending confirmation' if you try to send a reminder\n";
        }
        echo "\n";
    }
    
    // Count by status
    $stmt = $pdo->prepare("SELECT status, COUNT(*) as count FROM asset_assignment_confirmations GROUP BY status");
    $stmt->execute();
    $statusCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nConfirmation counts by status:\n";
    foreach ($statusCounts as $status) {
        echo "- {$status['status']}: {$status['count']}\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}