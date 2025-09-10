<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RolePermission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolePermissions = [
            // Super Admin - All permissions
            ['role_id' => 1, 'permission_id' => 1],  // view_assets
            ['role_id' => 1, 'permission_id' => 2],  // create_assets
            ['role_id' => 1, 'permission_id' => 3],  // edit_assets
            ['role_id' => 1, 'permission_id' => 4],  // delete_assets
            ['role_id' => 1, 'permission_id' => 5],  // view_users
            ['role_id' => 1, 'permission_id' => 6],  // create_users
            ['role_id' => 1, 'permission_id' => 7],  // edit_users
            ['role_id' => 1, 'permission_id' => 8],  // delete_users
            ['role_id' => 1, 'permission_id' => 9],  // view_reports
            ['role_id' => 1, 'permission_id' => 10], // manage_transfers
            ['role_id' => 1, 'permission_id' => 11], // manage_maintenance
            ['role_id' => 1, 'permission_id' => 12], // manage_disposals
            ['role_id' => 1, 'permission_id' => 13], // view_logs
            ['role_id' => 1, 'permission_id' => 14], // manage_roles
            ['role_id' => 1, 'permission_id' => 15], // manage_permissions
            ['role_id' => 1, 'permission_id' => 16], // system_admin
            
            // Admin - Most permissions except system admin
            ['role_id' => 2, 'permission_id' => 1],  // view_assets
            ['role_id' => 2, 'permission_id' => 2],  // create_assets
            ['role_id' => 2, 'permission_id' => 3],  // edit_assets
            ['role_id' => 2, 'permission_id' => 4],  // delete_assets
            ['role_id' => 2, 'permission_id' => 5],  // view_users
            ['role_id' => 2, 'permission_id' => 6],  // create_users
            ['role_id' => 2, 'permission_id' => 7],  // edit_users
            ['role_id' => 2, 'permission_id' => 9],  // view_reports
            ['role_id' => 2, 'permission_id' => 10], // manage_transfers
            ['role_id' => 2, 'permission_id' => 11], // manage_maintenance
            ['role_id' => 2, 'permission_id' => 12], // manage_disposals
            ['role_id' => 2, 'permission_id' => 13], // view_logs
            
            // Manager - View and approve permissions
            ['role_id' => 3, 'permission_id' => 1],  // view_assets
            ['role_id' => 3, 'permission_id' => 5],  // view_users
            ['role_id' => 3, 'permission_id' => 9],  // view_reports
            ['role_id' => 3, 'permission_id' => 10], // manage_transfers
            ['role_id' => 3, 'permission_id' => 12], // manage_disposals
            
            // User - Basic view permissions
            ['role_id' => 4, 'permission_id' => 1],  // view_assets
            
            // IT Support - Technical permissions
            ['role_id' => 5, 'permission_id' => 1],  // view_assets
            ['role_id' => 5, 'permission_id' => 3],  // edit_assets
            ['role_id' => 5, 'permission_id' => 11], // manage_maintenance
        ];

        foreach ($rolePermissions as $rolePermission) {
            RolePermission::firstOrCreate(
                ['role_id' => $rolePermission['role_id'], 'permission_id' => $rolePermission['permission_id']],
                $rolePermission
            );
        }
    }
}
