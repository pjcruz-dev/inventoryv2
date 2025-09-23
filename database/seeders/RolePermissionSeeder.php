<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

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
            
            // Also ensure any existing Super Admin users get the role
            $superAdminUsers = User::whereHas('roles', function ($query) {
                $query->where('name', 'Super Admin');
            })->get();
            foreach ($superAdminUsers as $user) {
                $user->syncRoles(['Super Admin']);
                $this->command->info("✓ Super Admin role confirmed for user: {$user->email}");
            }
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
                'view_dashboard', 'view_notifications', 'manage_notifications',
                'import_export_access', 'template_download', 'data_export', 'data_import',
                'view_accountability_forms', 'generate_accountability_forms', 'print_accountability_forms', 'bulk_accountability_forms',
                // New feature permissions
                'generate_qr_codes', 'download_qr_codes', 'bulk_generate_qr_codes', 'scan_qr_codes', 'process_qr_scans',
                'upload_asset_images', 'delete_asset_images', 'view_asset_images', 'get_asset_thumbnails',
                'global_search', 'advanced_search', 'search_suggestions', 'search_filter_options',
                'use_keyboard_shortcuts', 'view_keyboard_shortcuts',
                'mobile_access', 'mobile_asset_management', 'mobile_qr_scanner',
                'export_assets_excel', 'export_assets_pdf', 'export_assets_csv', 'preview_exports', 'download_exports',
                'toggle_dark_mode', 'manage_theme_preferences',
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
                'template_download', 'data_export',
                // New feature permissions for managers
                'generate_qr_codes', 'download_qr_codes', 'scan_qr_codes', 'process_qr_scans',
                'view_asset_images', 'get_asset_thumbnails',
                'global_search', 'advanced_search', 'search_suggestions',
                'use_keyboard_shortcuts', 'view_keyboard_shortcuts',
                'mobile_access', 'mobile_asset_management', 'mobile_qr_scanner',
                'export_assets_excel', 'export_assets_pdf', 'export_assets_csv', 'preview_exports',
                'toggle_dark_mode'
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
                'view_timeline', 'view_dashboard', 'view_notifications',
                // New feature permissions for users
                'scan_qr_codes', 'process_qr_scans',
                'view_asset_images', 'get_asset_thumbnails',
                'global_search', 'search_suggestions',
                'use_keyboard_shortcuts', 'view_keyboard_shortcuts',
                'mobile_access', 'mobile_asset_management', 'mobile_qr_scanner',
                'export_assets_excel', 'export_assets_pdf', 'export_assets_csv',
                'toggle_dark_mode'
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
                'template_download', 'data_export',
                // New feature permissions for IT Support
                'generate_qr_codes', 'download_qr_codes', 'bulk_generate_qr_codes', 'scan_qr_codes', 'process_qr_scans',
                'upload_asset_images', 'delete_asset_images', 'view_asset_images', 'get_asset_thumbnails',
                'global_search', 'advanced_search', 'search_suggestions', 'search_filter_options',
                'use_keyboard_shortcuts', 'view_keyboard_shortcuts',
                'mobile_access', 'mobile_asset_management', 'mobile_qr_scanner',
                'export_assets_excel', 'export_assets_pdf', 'export_assets_csv', 'preview_exports', 'download_exports',
                'toggle_dark_mode', 'manage_theme_preferences'
            ];
            $itSupportRole->syncPermissions($itSupportPermissions);
            $this->command->info('✓ IT Support permissions assigned to IT Support role');
        }
    }
}
