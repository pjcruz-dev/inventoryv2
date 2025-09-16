<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all permissions
        $allPermissions = Permission::all();
        
        // Assign all permissions to Super Admin
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $superAdminRole->syncPermissions($allPermissions);
            $this->command->info('✓ All permissions assigned to Super Admin role');
        }
        
        // Assign specific permissions to Admin role
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminPermissions = [
                'view_assets', 'create_assets', 'edit_assets', 'delete_assets', 'assign_assets',
                'view_asset_assignments', 'create_asset_assignments', 'edit_asset_assignments', 'delete_asset_assignments',
                'view_asset_categories', 'create_asset_categories', 'edit_asset_categories', 'delete_asset_categories',
                'view_computers', 'create_computers', 'edit_computers', 'delete_computers',
                'view_monitors', 'create_monitors', 'edit_monitors', 'delete_monitors',
                'view_printers', 'create_printers', 'edit_printers', 'delete_printers',
                'view_peripherals', 'create_peripherals', 'edit_peripherals', 'delete_peripherals',
                'view_departments', 'create_departments', 'edit_departments', 'delete_departments',
                'view_vendors', 'create_vendors', 'edit_vendors', 'delete_vendors',
                'view_users', 'create_users', 'edit_users',
                'view_maintenance', 'create_maintenance', 'edit_maintenance', 'delete_maintenance',
                'view_disposal', 'create_disposal', 'edit_disposal', 'delete_disposal',
                'view_timeline', 'create_timeline',
                'view_dashboard', 'view_notifications',
                'import_export_access', 'template_download', 'data_export', 'data_import',
                'admin_access'
            ];
            $adminRole->syncPermissions($adminPermissions);
            $this->command->info('✓ Admin permissions assigned to Admin role');
        }
        
        // Assign specific permissions to Manager role
        $managerRole = Role::where('name', 'Manager')->first();
        if ($managerRole) {
            $managerPermissions = [
                'view_assets', 'assign_assets',
                'view_asset_assignments', 'create_asset_assignments', 'edit_asset_assignments',
                'view_asset_categories',
                'view_computers', 'view_monitors', 'view_printers', 'view_peripherals',
                'view_departments', 'view_vendors', 'view_users',
                'view_maintenance', 'create_maintenance',
                'view_disposal', 'create_disposal',
                'view_timeline', 'view_dashboard', 'view_notifications',
                'template_download', 'data_export'
            ];
            $managerRole->syncPermissions($managerPermissions);
            $this->command->info('✓ Manager permissions assigned to Manager role');
        }
        
        // Assign specific permissions to User role
        $userRole = Role::where('name', 'User')->first();
        if ($userRole) {
            $userPermissions = [
                'view_assets',
                'view_asset_assignments',
                'view_asset_categories',
                'view_computers', 'view_monitors', 'view_printers', 'view_peripherals',
                'view_departments', 'view_vendors',
                'view_timeline', 'view_dashboard', 'view_notifications'
            ];
            $userRole->syncPermissions($userPermissions);
            $this->command->info('✓ User permissions assigned to User role');
        }
        
        // Assign specific permissions to IT Support role
        $itSupportRole = Role::where('name', 'IT Support')->first();
        if ($itSupportRole) {
            $itSupportPermissions = [
                'view_assets', 'create_assets', 'edit_assets',
                'view_asset_assignments', 'create_asset_assignments', 'edit_asset_assignments',
                'view_asset_categories',
                'view_computers', 'create_computers', 'edit_computers',
                'view_monitors', 'create_monitors', 'edit_monitors',
                'view_printers', 'create_printers', 'edit_printers',
                'view_peripherals', 'create_peripherals', 'edit_peripherals',
                'view_departments', 'view_vendors',
                'view_maintenance', 'create_maintenance', 'edit_maintenance',
                'view_timeline', 'create_timeline',
                'view_dashboard', 'view_notifications',
                'template_download', 'data_export'
            ];
            $itSupportRole->syncPermissions($itSupportPermissions);
            $this->command->info('✓ IT Support permissions assigned to IT Support role');
        }
    }
}
