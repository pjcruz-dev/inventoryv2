<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class AssignReportsPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions if they don't exist
        $permissions = [
            'view_reports',
            'export_reports',
            'view_asset_analytics',
            'view_user_activity',
            'view_financial_reports',
            'view_maintenance_reports',
            'export_asset_analytics',
            'export_user_activity',
            'export_financial_reports',
            'export_maintenance_reports'
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

        $this->command->info('Reports permissions assigned to all users and roles successfully!');
    }
}
