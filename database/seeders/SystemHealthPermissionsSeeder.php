<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SystemHealthPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Create permissions
        $permissions = [
            'view_system_health',
            'manage_system_health',
            'clear_system_cache',
            'view_performance_metrics'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole = Role::where('name', 'Admin')->first();
        $superAdminRole = Role::where('name', 'Super Admin')->first();

        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
        }
    }
}
