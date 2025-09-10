<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'view_assets', 'description' => 'View asset information'],
            ['name' => 'create_assets', 'description' => 'Create new assets'],
            ['name' => 'edit_assets', 'description' => 'Edit existing assets'],
            ['name' => 'delete_assets', 'description' => 'Delete assets'],
            ['name' => 'view_users', 'description' => 'View user information'],
            ['name' => 'create_users', 'description' => 'Create new users'],
            ['name' => 'edit_users', 'description' => 'Edit existing users'],
            ['name' => 'delete_users', 'description' => 'Delete users'],
            ['name' => 'view_reports', 'description' => 'View system reports'],
            ['name' => 'manage_transfers', 'description' => 'Manage asset transfers'],
            ['name' => 'manage_maintenance', 'description' => 'Manage asset maintenance'],
            ['name' => 'manage_disposals', 'description' => 'Manage asset disposals'],
            ['name' => 'view_logs', 'description' => 'View system logs'],
            ['name' => 'manage_roles', 'description' => 'Manage user roles'],
            ['name' => 'manage_permissions', 'description' => 'Manage permissions'],
            ['name' => 'system_admin', 'description' => 'Full system administration']
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}
