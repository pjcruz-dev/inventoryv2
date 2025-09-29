<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class AssignSecurityMonitoringPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions if they don't exist
        $permissions = [
            'view_security_monitoring',
            'manage_security_monitoring',
            'run_security_monitoring',
            'clear_security_blocks',
            'view_security_threats',
            'manage_security_threats',
            'export_security_monitoring'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Get all existing users
        $users = User::all();

        // Assign permissions to all users
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

        $this->command->info('Security Monitoring permissions assigned to all users and roles successfully!');
    }
}
