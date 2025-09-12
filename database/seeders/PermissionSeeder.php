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
            // Asset Management
            ['name' => 'view_assets', 'description' => 'View asset information'],
            ['name' => 'create_assets', 'description' => 'Create new assets'],
            ['name' => 'edit_assets', 'description' => 'Edit existing assets'],
            ['name' => 'delete_assets', 'description' => 'Delete assets'],
            ['name' => 'assign_assets', 'description' => 'Assign assets to users'],
            
            // User Management
            ['name' => 'view_users', 'description' => 'View user information'],
            ['name' => 'create_users', 'description' => 'Create new users'],
            ['name' => 'edit_users', 'description' => 'Edit existing users'],
            ['name' => 'delete_users', 'description' => 'Delete users'],
            ['name' => 'manage_user_roles', 'description' => 'Manage user role assignments'],
            ['name' => 'assign_roles', 'description' => 'Assign roles to users'],
            ['name' => 'remove_roles', 'description' => 'Remove roles from users'],
            ['name' => 'bulk_assign_roles', 'description' => 'Bulk assign roles to users'],
            ['name' => 'view_user_roles', 'description' => 'View user role assignments'],
            
            // System Management
            ['name' => 'view_reports', 'description' => 'View system reports'],
            ['name' => 'manage_transfers', 'description' => 'Manage asset transfers'],
            ['name' => 'manage_maintenance', 'description' => 'Manage asset maintenance'],
            ['name' => 'manage_disposals', 'description' => 'Manage asset disposals'],
            ['name' => 'view_logs', 'description' => 'View system logs'],
            
            // Role & Permission Management
            ['name' => 'view_roles', 'description' => 'View user roles'],
            ['name' => 'manage_roles', 'description' => 'Manage user roles'],
            ['name' => 'view_permissions', 'description' => 'View permissions'],
            ['name' => 'manage_permissions', 'description' => 'Manage permissions'],
            ['name' => 'system_admin', 'description' => 'Full system administration'],
            
            // Import/Export Core
            ['name' => 'import_export_access', 'description' => 'Access import/export interface'],
            ['name' => 'template_download', 'description' => 'Download import/export templates'],
            ['name' => 'template_preview', 'description' => 'Preview import/export templates'],
            ['name' => 'data_export', 'description' => 'Export data from the system'],
            ['name' => 'data_import', 'description' => 'Import data into the system'],
            ['name' => 'bulk_export', 'description' => 'Bulk export multiple modules'],
            
            // Import/Export Advanced
            ['name' => 'import_validation', 'description' => 'Validate import data'],
            ['name' => 'import_status', 'description' => 'View import status and progress'],
            ['name' => 'import_history', 'description' => 'View import history'],
            ['name' => 'import_details', 'description' => 'View detailed import information'],
            ['name' => 'error_reports', 'description' => 'View import error reports'],
            ['name' => 'error_download', 'description' => 'Download error reports'],
            
            // Serial Number Management
            ['name' => 'serial_generation', 'description' => 'Generate serial number suggestions'],
            ['name' => 'serial_validation', 'description' => 'Validate serial numbers'],
            ['name' => 'serial_stats', 'description' => 'View serial number statistics'],
            
            // Field & Data Management
            ['name' => 'field_validation', 'description' => 'Validate individual fields'],
            ['name' => 'data_lookup', 'description' => 'Lookup reference data'],
            ['name' => 'field_mapping', 'description' => 'Access field mapping functionality'],
            ['name' => 'import_progress', 'description' => 'View import progress'],
            
            // Admin Access
            ['name' => 'admin_access', 'description' => 'Administrative access to system management'],
            
            // Maintenance Management
            ['name' => 'view_maintenance', 'description' => 'View maintenance records'],
            ['name' => 'create_maintenance', 'description' => 'Create new maintenance records'],
            ['name' => 'edit_maintenance', 'description' => 'Edit existing maintenance records'],
            ['name' => 'delete_maintenance', 'description' => 'Delete maintenance records'],
            
            // Disposal Management
            ['name' => 'view_disposal', 'description' => 'View disposal records'],
            ['name' => 'create_disposal', 'description' => 'Create new disposal records'],
            ['name' => 'edit_disposal', 'description' => 'Edit existing disposal records'],
            ['name' => 'delete_disposal', 'description' => 'Delete disposal records']
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name'], 'guard_name' => 'web'],
                array_merge($permission, ['guard_name' => 'web'])
            );
        }
    }
}
