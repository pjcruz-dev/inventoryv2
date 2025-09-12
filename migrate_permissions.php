<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Migrating role-permission relationships...\n";
    
    $rolePermissions = DB::table('role_permissions')->get();
    
    foreach ($rolePermissions as $rp) {
        // Check if relationship already exists
        $exists = DB::table('role_has_permissions')
            ->where('permission_id', $rp->permission_id)
            ->where('role_id', $rp->role_id)
            ->exists();
            
        if (!$exists) {
            DB::table('role_has_permissions')->insert([
                'permission_id' => $rp->permission_id,
                'role_id' => $rp->role_id
            ]);
            echo "Migrated: Role {$rp->role_id} -> Permission {$rp->permission_id}\n";
        } else {
            echo "Already exists: Role {$rp->role_id} -> Permission {$rp->permission_id}\n";
        }
    }
    
    echo "Migration completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}