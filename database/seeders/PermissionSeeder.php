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
            ['name' => 'mark_notification_read', 'description' => 'Mark notifications as read'],
            ['name' => 'mark_all_notifications_read', 'description' => 'Mark all notifications as read'],
            ['name' => 'get_unread_notifications', 'description' => 'Get unread notifications'],
            ['name' => 'get_all_notifications', 'description' => 'Get all notifications'],
            ['name' => 'delete_notifications', 'description' => 'Delete notifications'],
            ['name' => 'delete_read_notifications', 'description' => 'Delete read notifications'],
            
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
            ['name' => 'bulk_accountability_forms', 'description' => 'Generate bulk accountability forms'],
            
            // Additional missing permissions
            ['name' => 'view_transfers', 'description' => 'View asset transfers'],
            ['name' => 'create_transfers', 'description' => 'Create asset transfers'],
            ['name' => 'edit_transfers', 'description' => 'Edit asset transfers'],
            ['name' => 'delete_transfers', 'description' => 'Delete asset transfers'],
            
            // System Administration
            ['name' => 'system_settings', 'description' => 'Manage system settings'],
            ['name' => 'backup_restore', 'description' => 'Backup and restore system data'],
            ['name' => 'audit_logs', 'description' => 'View audit logs'],
            ['name' => 'system_monitoring', 'description' => 'Monitor system performance'],
            
            // QR Code Management
            ['name' => 'generate_qr_codes', 'description' => 'Generate QR codes for assets'],
            ['name' => 'download_qr_codes', 'description' => 'Download QR codes'],
            ['name' => 'bulk_generate_qr_codes', 'description' => 'Generate QR codes in bulk'],
            ['name' => 'scan_qr_codes', 'description' => 'Scan QR codes'],
            ['name' => 'process_qr_scans', 'description' => 'Process QR code scans'],
            
            // Image Management
            ['name' => 'upload_asset_images', 'description' => 'Upload images for assets'],
            ['name' => 'delete_asset_images', 'description' => 'Delete asset images'],
            ['name' => 'view_asset_images', 'description' => 'View asset images'],
            ['name' => 'get_asset_thumbnails', 'description' => 'Get asset thumbnails'],
            
            // Search Management
            ['name' => 'global_search', 'description' => 'Use global search functionality'],
            ['name' => 'advanced_search', 'description' => 'Use advanced search features'],
            ['name' => 'search_suggestions', 'description' => 'Get search suggestions'],
            ['name' => 'search_filter_options', 'description' => 'Get search filter options'],
            ['name' => 'recent_searches', 'description' => 'View recent searches'],
            ['name' => 'popular_searches', 'description' => 'View popular searches'],
            
            // Keyboard Shortcuts
            ['name' => 'use_keyboard_shortcuts', 'description' => 'Use keyboard shortcuts'],
            ['name' => 'view_keyboard_shortcuts', 'description' => 'View keyboard shortcuts help'],
            ['name' => 'manage_keyboard_shortcuts', 'description' => 'Manage keyboard shortcuts'],
            
            // Mobile Features
            ['name' => 'mobile_access', 'description' => 'Access mobile-optimized views'],
            ['name' => 'mobile_asset_management', 'description' => 'Manage assets on mobile'],
            ['name' => 'mobile_qr_scanner', 'description' => 'Use QR scanner on mobile'],
            
            // Export/Import Enhanced
            ['name' => 'export_assets_excel', 'description' => 'Export assets to Excel'],
            ['name' => 'export_assets_pdf', 'description' => 'Export assets to PDF'],
            ['name' => 'export_assets_csv', 'description' => 'Export assets to CSV'],
            ['name' => 'preview_exports', 'description' => 'Preview export data'],
            ['name' => 'download_exports', 'description' => 'Download exported files'],
            
            // Dark Mode
            ['name' => 'toggle_dark_mode', 'description' => 'Toggle dark mode'],
            ['name' => 'manage_theme_preferences', 'description' => 'Manage theme preferences'],
            
            // Additional missing permissions found in routes
            ['name' => 'bulk_create_assets', 'description' => 'Create assets in bulk'],
            ['name' => 'bulk_store_assets', 'description' => 'Store bulk asset data'],
            ['name' => 'print_asset_labels', 'description' => 'Print asset labels'],
            ['name' => 'generate_asset_tags', 'description' => 'Generate unique asset tags'],
            ['name' => 'check_asset_uniqueness', 'description' => 'Check asset tag uniqueness'],
            ['name' => 'get_asset_vendor', 'description' => 'Get asset vendor information'],
            ['name' => 'print_employee_assets', 'description' => 'Print employee asset reports'],
            ['name' => 'export_asset_categories', 'description' => 'Export asset categories'],
            ['name' => 'import_asset_categories', 'description' => 'Import asset categories'],
            ['name' => 'export_asset_assignments', 'description' => 'Export asset assignments'],
            ['name' => 'import_asset_assignments', 'description' => 'Import asset assignments'],
            ['name' => 'return_asset_assignments', 'description' => 'Return asset assignments'],
            ['name' => 'send_assignment_reminders', 'description' => 'Send assignment reminders'],
            ['name' => 'clear_old_logs', 'description' => 'Clear old system logs'],
            ['name' => 'export_logs', 'description' => 'Export system logs'],
            ['name' => 'show_timeline', 'description' => 'Show asset timeline details'],
            ['name' => 'show_maintenance_details', 'description' => 'Show maintenance record details'],
            ['name' => 'export_maintenance_pdf', 'description' => 'Export maintenance records as PDF'],
            ['name' => 'show_disposal_details', 'description' => 'Show disposal record details'],
            ['name' => 'export_disposal_pdf', 'description' => 'Export disposal records as PDF']
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name'], 'guard_name' => 'web'],
                array_merge($permission, ['guard_name' => 'web'])
            );
        }
    }
}
