<?php

/**
 * Permission Fix Script
 * Run this script to fix super admin permissions
 * Usage: Access via web browser or include in a route
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

echo "🔧 Fixing Super Admin Permissions...\n\n";

try {
    // Get all permissions
    $allPermissions = Permission::all();
    echo "📋 Total permissions found: {$allPermissions->count()}\n";
    
    // Get or create Super Admin role
    $superAdminRole = Role::firstOrCreate(
        ['name' => 'Super Admin', 'guard_name' => 'web'],
        ['description' => 'Full system access with all permissions']
    );
    echo "👤 Super Admin role: {$superAdminRole->name}\n";
    
    // Assign all permissions to Super Admin role
    $superAdminRole->syncPermissions($allPermissions);
    echo "✅ All permissions assigned to Super Admin role\n";
    
    // Find and fix all users with Super Admin role
    $superAdminUsers = User::role('Super Admin')->get();
    echo "🔍 Found {$superAdminUsers->count()} Super Admin user(s)\n";
    
    foreach ($superAdminUsers as $user) {
        echo "  - Fixing permissions for: {$user->email}\n";
        $user->syncRoles(['Super Admin']);
        
        // Verify permissions
        $userPermissions = $user->getAllPermissions();
        echo "    ✅ User now has {$userPermissions->count()} permissions\n";
    }
    
    // Clear permission cache
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    echo "🗑️ Permission cache cleared\n";
    
    // Test key permissions
    echo "\n🧪 Testing key permissions:\n";
    $testUser = $superAdminUsers->first();
    if ($testUser) {
        $keyPermissions = [
            'view_assets', 'create_assets', 'view_users', 'view_maintenance', 
            'view_departments', 'view_vendors', 'view_logs', 'view_roles'
        ];
        
        foreach ($keyPermissions as $permission) {
            $hasPermission = $testUser->can($permission);
            $status = $hasPermission ? '✅' : '❌';
            echo "  {$status} {$permission}\n";
        }
    }
    
    echo "\n🎉 Super admin permissions fixed successfully!\n";
    echo "🔄 Please refresh your browser to see the changes.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
