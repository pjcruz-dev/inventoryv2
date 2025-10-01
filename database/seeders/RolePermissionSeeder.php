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
                // Asset Management
                'view_assets', 'create_assets', 'edit_assets', 'delete_assets', 'assign_assets',
                
                // Asset Assignment Management
                'view_asset_assignments', 'create_asset_assignments', 'edit_asset_assignments', 'delete_asset_assignments', 'manage_asset_assignments',
                
                // Asset Assignment Confirmation Management
                'view_assignment_confirmations', 'create_assignment_confirmations', 'edit_assignment_confirmations', 'delete_assignment_confirmations', 'manage_assignment_confirmations',
                
                // Asset Category Management
                'view_asset_categories', 'create_asset_categories', 'edit_asset_categories', 'delete_asset_categories', 'manage_asset_categories',
                
                // Computer Management
                'view_computers', 'create_computers', 'edit_computers', 'delete_computers',
                
                // Monitor Management
                'view_monitors', 'create_monitors', 'edit_monitors', 'delete_monitors',
                
                // Printer Management
                'view_printers', 'create_printers', 'edit_printers', 'delete_printers',
                
                // Peripheral Management
                'view_peripherals', 'create_peripherals', 'edit_peripherals', 'delete_peripherals',
                
                // Department Management
                'view_departments', 'create_departments', 'edit_departments', 'delete_departments',
                
                // Vendor Management
                'view_vendors', 'create_vendors', 'edit_vendors', 'delete_vendors',
                
                // User Management
                'view_users', 'create_users', 'edit_users', 'delete_users', 'manage_user_roles', 'assign_roles', 'remove_roles', 'bulk_assign_roles', 'view_user_roles',
                
                // System Management
                'view_reports', 'manage_transfers', 'manage_maintenance', 'manage_disposals', 'view_logs',
                
                // Role & Permission Management
                'view_roles', 'create_roles', 'edit_roles', 'delete_roles', 'manage_roles', 
                'view_permissions', 'create_permissions', 'edit_permissions', 'delete_permissions', 'manage_permissions',
                
                // Maintenance Management
                'view_maintenance', 'create_maintenance', 'edit_maintenance', 'delete_maintenance',
                
                // Disposal Management
                'view_disposal', 'create_disposal', 'edit_disposal', 'delete_disposal',
                
                // Timeline Management
                'view_timeline', 'create_timeline',
                
                // Dashboard & Notifications
                'view_dashboard', 'view_notifications', 'manage_notifications',
                
                // Import/Export
                'import_export_access', 'template_download', 'template_preview', 'data_export', 'data_import', 'bulk_export',
                'import_validation', 'import_status', 'import_history', 'import_details', 'error_reports', 'error_download',
                
                // Serial Number Management
                'serial_generation', 'serial_validation', 'serial_stats',
                
                // Field & Data Management
                'field_validation', 'data_lookup', 'field_mapping', 'import_progress',
                
                // Accountability Forms
                'view_accountability_forms', 'generate_accountability_forms', 'print_accountability_forms', 'bulk_accountability_forms',
                
                // System Health & Security
                'view_system_health', 'manage_system_health', 'clear_system_cache', 'view_performance_metrics',
                'view_security_audit', 'manage_security_audit', 'view_security_monitoring', 'manage_security_monitoring',
                
                // Admin Access
                'admin_access', 'system_admin'
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
                'view_assignment_confirmations', 'manage_assignment_confirmations',
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
        
        // Assign specific permissions to User role - VERY LIMITED ACCESS
        $userRole = Role::where('name', 'User')->first();
        if ($userRole) {
            $userPermissions = [
                'view_assets'  // Only view assets - NO DASHBOARD ACCESS
            ];
            $userRole->syncPermissions($userPermissions);
            $this->command->info('✓ User permissions assigned to User role (ASSETS ONLY - NO DASHBOARD)');
        }
        
        // Assign specific permissions to IT Support role
        $itSupportRole = Role::where('name', 'IT Support')->first();
        if ($itSupportRole) {
            $itSupportPermissions = [
                'view_assets', 'create_assets', 'edit_assets',
                'view_asset_assignments', 'create_asset_assignments', 'edit_asset_assignments',
                'view_assignment_confirmations', 'manage_assignment_confirmations',
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
