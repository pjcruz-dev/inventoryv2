<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "🔍 Checking Authentication System...\n\n";

// Check if users exist
$userCount = User::count();
echo "Users in database: $userCount\n";

if ($userCount === 0) {
    echo "❌ No users found! Creating a test user...\n";
    
    // Create a test user
    $user = User::create([
        'name' => 'Test Administrator',
        'email' => 'admin@test.com',
        'password' => Hash::make('password'),
        'employee_no' => 'EMP001',
        'position' => 'System Administrator',
        'phone' => '123-456-7890',
        'company' => 'test',
        'entity' => 'test',
        'job_title' => 'Administrator',
        'status' => 1
    ]);
    
    echo "✅ Test user created: admin@test.com / password\n";
} else {
    $user = User::first();
    echo "✅ Found user: " . $user->email . "\n";
}

// Check authentication middleware
echo "\n🔧 Testing Authentication Middleware...\n";

// Test if we can access protected routes
$response = file_get_contents('http://127.0.0.1:8000/assets');
$httpCode = 200;
if (isset($http_response_header)) {
    foreach ($http_response_header as $header) {
        if (strpos($header, 'HTTP/') === 0) {
            $httpCode = (int) substr($header, 9, 3);
            break;
        }
    }
}

echo "Assets page response code: $httpCode\n";

if ($httpCode === 200) {
    echo "⚠️  Assets page is accessible without authentication\n";
    echo "This suggests the authentication middleware is not working properly\n";
} elseif ($httpCode === 302) {
    echo "✅ Assets page redirects to login (authentication working)\n";
} else {
    echo "❓ Unexpected response code: $httpCode\n";
}

echo "\n🎯 Next Steps:\n";
echo "1. Open browser and go to http://127.0.0.1:8000\n";
echo "2. You should be redirected to login page\n";
echo "3. Login with: admin@test.com / password\n";
echo "4. Test all modules after login\n";

echo "\n✅ Authentication check completed!\n";
