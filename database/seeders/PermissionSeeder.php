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
            
            // Asset Assignment Management
            ['name' => 'view_asset_assignments', 'description' => 'View asset assignments'],
            ['name' => 'create_asset_assignments', 'description' => 'Create new asset assignments'],
            ['name' => 'edit_asset_assignments', 'description' => 'Edit existing asset assignments'],
            ['name' => 'delete_asset_assignments', 'description' => 'Delete asset assignments'],
            ['name' => 'manage_asset_assignments', 'description' => 'Full management of asset assignments'],
            
            // Asset Assignment Confirmation Management
            ['name' => 'view_assignment_confirmations', 'description' => 'View assignment confirmations'],
            ['name' => 'create_assignment_confirmations', 'description' => 'Create new assignment confirmations'],
            ['name' => 'edit_assignment_confirmations', 'description' => 'Edit existing assignment confirmations'],
            ['name' => 'delete_assignment_confirmations', 'description' => 'Delete assignment confirmations'],
            ['name' => 'manage_assignment_confirmations', 'description' => 'Full management of assignment confirmations'],
            
            // Asset Category Management
            ['name' => 'view_asset_categories', 'description' => 'View asset categories'],
            ['name' => 'create_asset_categories', 'description' => 'Create new asset categories'],
            ['name' => 'edit_asset_categories', 'description' => 'Edit existing asset categories'],
            ['name' => 'delete_asset_categories', 'description' => 'Delete asset categories'],
            ['name' => 'manage_asset_categories', 'description' => 'Full management of asset categories'],
             
            // Computer Management
            ['name' => 'view_computers', 'description' => 'View computer information'],
            ['name' => 'create_computers', 'description' => 'Create new computers'],
            ['name' => 'edit_computers', 'description' => 'Edit existing computers'],
            ['name' => 'delete_computers', 'description' => 'Delete computers'],
            
            // Monitor Management
            ['name' => 'view_monitors', 'description' => 'View monitor information'],
            ['name' => 'create_monitors', 'description' => 'Create new monitors'],
            ['name' => 'edit_monitors', 'description' => 'Edit existing monitors'],
            ['name' => 'delete_monitors', 'description' => 'Delete monitors'],
            
            // Printer Management
            ['name' => 'view_printers', 'description' => 'View printer information'],
            ['name' => 'create_printers', 'description' => 'Create new printers'],
            ['name' => 'edit_printers', 'description' => 'Edit existing printers'],
            ['name' => 'delete_printers', 'description' => 'Delete printers'],
            
            // Department Management
            ['name' => 'view_departments', 'description' => 'View department information'],
            ['name' => 'create_departments', 'description' => 'Create new departments'],
            ['name' => 'edit_departments', 'description' => 'Edit existing departments'],
            ['name' => 'delete_departments', 'description' => 'Delete departments'],
            
            // Vendor Management
            ['name' => 'view_vendors', 'description' => 'View vendor information'],
            ['name' => 'create_vendors', 'description' => 'Create new vendors'],
            ['name' => 'edit_vendors', 'description' => 'Edit existing vendors'],
            ['name' => 'delete_vendors', 'description' => 'Delete vendors'],
            
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
            ['name' => 'delete_disposal', 'description' => 'Delete disposal records'],
            
            // Timeline Management
            ['name' => 'view_timeline', 'description' => 'View asset timeline'],
            ['name' => 'create_timeline', 'description' => 'Create timeline entries'],
            
            // Dashboard Access
            ['name' => 'view_dashboard', 'description' => 'View dashboard and statistics'],
            
            // Notification Management
            ['name' => 'view_notifications', 'description' => 'View notifications'],
            ['name' => 'manage_notifications', 'description' => 'Manage notifications'],
            
            // Peripheral Management
            ['name' => 'view_peripherals', 'description' => 'View peripheral devices'],
            ['name' => 'create_peripherals', 'description' => 'Create new peripheral devices'],
            ['name' => 'edit_peripherals', 'description' => 'Edit existing peripheral devices'],
            ['name' => 'delete_peripherals', 'description' => 'Delete peripheral devices'],
            
            // Asset Confirmation Management
            ['name' => 'view_asset_confirmations', 'description' => 'View asset confirmations'],
            ['name' => 'manage_asset_confirmations', 'description' => 'Manage asset confirmations'],
            
            // Export Management
            ['name' => 'export_maintenance', 'description' => 'Export maintenance records'],
            ['name' => 'export_disposal', 'description' => 'Export disposal records'],
            
            // Accountability Form Management
            ['name' => 'view_accountability_forms', 'description' => 'View accountability forms'],
            ['name' => 'generate_accountability_forms', 'description' => 'Generate accountability forms'],
            ['name' => 'print_accountability_forms', 'description' => 'Print accountability forms'],
            ['name' => 'bulk_accountability_forms', 'description' => 'Generate bulk accountability forms']
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name'], 'guard_name' => 'web'],
                array_merge($permission, ['guard_name' => 'web'])
            );
        }
    }
}
