<?php

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Permission;
use App\Models\Role;

echo "=== Checking User Permissions ===\n\n";

// Get all users and their permissions
$users = User::with(['roles.permissions'])->get();

foreach ($users as $user) {
    echo "User: {$user->first_name} {$user->last_name} ({$user->email})\n";
    echo "Roles: ";
    
    if ($user->roles->count() > 0) {
        foreach ($user->roles as $role) {
            echo "{$role->name} ";
        }
        echo "\n";
        
        echo "Permissions:\n";
        $allPermissions = collect();
        foreach ($user->roles as $role) {
            $allPermissions = $allPermissions->merge($role->permissions);
        }
        
        $uniquePermissions = $allPermissions->unique('id');
        foreach ($uniquePermissions as $permission) {
            echo "  - {$permission->name}\n";
        }
        
        // Check specifically for manage_assignment_confirmations
        $hasManagePermission = $uniquePermissions->where('name', 'manage_assignment_confirmations')->count() > 0;
        echo "Has 'manage_assignment_confirmations': " . ($hasManagePermission ? 'YES' : 'NO') . "\n";
        
    } else {
        echo "No roles assigned\n";
    }
    
    echo "---\n";
}

echo "\nAll available permissions:\n";
$permissions = Permission::all();
foreach ($permissions as $permission) {
    echo "- {$permission->name}\n";
}

echo "\nDone.\n";