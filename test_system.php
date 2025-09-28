<?php

/**
 * Complete System Testing Script
 * Tests all major endpoints and functionality
 */

$baseUrl = 'http://127.0.0.1:8000';
$testResults = [];
$passedTests = 0;
$totalTests = 0;

function testEndpoint($url, $expectedStatus = 200, $description = '') {
    global $testResults, $passedTests, $totalTests;
    
    $totalTests++;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    $success = ($httpCode == $expectedStatus) && empty($error);
    if ($success) $passedTests++;
    
    $testResults[] = [
        'url' => $url,
        'description' => $description,
        'expected' => $expectedStatus,
        'actual' => $httpCode,
        'success' => $success,
        'error' => $error
    ];
    
    echo sprintf(
        "%s %s - %s (Expected: %d, Got: %d)\n",
        $success ? '✅' : '❌',
        $description ?: $url,
        $success ? 'PASS' : 'FAIL',
        $expectedStatus,
        $httpCode
    );
    
    if ($error) {
        echo "   Error: $error\n";
    }
}

echo "🧪 Starting Complete System Testing...\n\n";

// Health Check Endpoints
echo "🔍 Testing Health Check Endpoints:\n";
testEndpoint("$baseUrl/health", 200, 'Basic Health Check');
testEndpoint("$baseUrl/health/detailed", 200, 'Detailed Health Check');
testEndpoint("$baseUrl/health/readiness", 200, 'Readiness Probe');
testEndpoint("$baseUrl/health/liveness", 200, 'Liveness Probe');
testEndpoint("$baseUrl/health/metrics", 200, 'Health Metrics');

echo "\n";

// Core Module Endpoints (these will redirect to login, which is expected)
echo "📋 Testing Core Module Endpoints:\n";
testEndpoint("$baseUrl/assets", 302, 'Assets Module');
testEndpoint("$baseUrl/computers", 302, 'Computers Module');
testEndpoint("$baseUrl/monitors", 302, 'Monitors Module');
testEndpoint("$baseUrl/printers", 302, 'Printers Module');
testEndpoint("$baseUrl/peripherals", 302, 'Peripherals Module');
testEndpoint("$baseUrl/asset-categories", 302, 'Asset Categories Module');
testEndpoint("$baseUrl/users", 302, 'Users Module');
testEndpoint("$baseUrl/departments", 302, 'Departments Module');
testEndpoint("$baseUrl/vendors", 302, 'Vendors Module');
testEndpoint("$baseUrl/asset-assignments", 302, 'Asset Assignments Module');
testEndpoint("$baseUrl/asset-assignment-confirmations", 302, 'Assignment Confirmations Module');
testEndpoint("$baseUrl/accountability", 302, 'Accountability Module');
testEndpoint("$baseUrl/maintenance", 302, 'Maintenance Module');
testEndpoint("$baseUrl/disposal", 302, 'Disposal Module');

echo "\n";

// Reports Endpoints (these will also redirect to login)
echo "📊 Testing Reports Endpoints:\n";
testEndpoint("$baseUrl/reports", 302, 'Reports Dashboard');
testEndpoint("$baseUrl/reports/asset-analytics", 302, 'Asset Analytics Report');
testEndpoint("$baseUrl/reports/financial", 302, 'Financial Report');
testEndpoint("$baseUrl/reports/user-activity", 302, 'User Activity Report');
testEndpoint("$baseUrl/reports/maintenance", 302, 'Maintenance Report');

echo "\n";

// Security Endpoints (these will also redirect to login)
echo "🔒 Testing Security Endpoints:\n";
testEndpoint("$baseUrl/security/audit", 302, 'Security Audit');
testEndpoint("$baseUrl/security/monitoring", 302, 'Security Monitoring');
testEndpoint("$baseUrl/system/health", 302, 'System Health');

echo "\n";

// API Endpoints (these should work without authentication)
echo "🔌 Testing API Endpoints:\n";
testEndpoint("$baseUrl/security/monitoring/threats", 401, 'Security Threats API');
testEndpoint("$baseUrl/security/monitoring/events", 401, 'Security Events API');
testEndpoint("$baseUrl/security/monitoring/statistics", 401, 'Security Statistics API');

echo "\n";

// Summary
echo "📊 Testing Summary:\n";
echo "==================\n";
echo "Total Tests: $totalTests\n";
echo "Passed: $passedTests\n";
echo "Failed: " . ($totalTests - $passedTests) . "\n";
echo "Success Rate: " . round(($passedTests / $totalTests) * 100, 2) . "%\n\n";

// Detailed Results
echo "📋 Detailed Results:\n";
echo "===================\n";
foreach ($testResults as $result) {
    $status = $result['success'] ? 'PASS' : 'FAIL';
    echo sprintf(
        "[%s] %s - %s (Expected: %d, Got: %d)\n",
        $status,
        $result['description'] ?: $result['url'],
        $result['success'] ? '✅' : '❌',
        $result['expected'],
        $result['actual']
    );
    if ($result['error']) {
        echo "   Error: " . $result['error'] . "\n";
    }
}

echo "\n🎯 Next Steps:\n";
echo "==============\n";
echo "1. Login to the system and test authenticated endpoints\n";
echo "2. Test the UI functionality manually\n";
echo "3. Check mobile responsiveness\n";
echo "4. Test export functionality\n";
echo "5. Verify all reports display correctly\n";
echo "6. Test security monitoring features\n";

echo "\n✅ System testing completed!\n";
