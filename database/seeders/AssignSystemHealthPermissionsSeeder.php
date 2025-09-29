<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class AssignSystemHealthPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Create permissions if they don't exist
        $permissions = [
            'view_system_health',
            'manage_system_health',
            'clear_system_cache',
            'view_performance_metrics'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to all users
        $users = User::all();
        
        foreach ($users as $user) {
            foreach ($permissions as $permission) {
                $user->givePermissionTo($permission);
            }
        }

        // Also assign to roles
        $roles = Role::all();
        foreach ($roles as $role) {
            foreach ($permissions as $permission) {
                $role->givePermissionTo($permission);
            }
        }

        $this->command->info('System Health permissions assigned to all users and roles successfully!');
    }
}
