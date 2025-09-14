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
            ['role_id' => 1, 'permission_id' => 5],  // assign_assets
            ['role_id' => 1, 'permission_id' => 6],  // view_users
            ['role_id' => 1, 'permission_id' => 7],  // create_users
            ['role_id' => 1, 'permission_id' => 8],  // edit_users
            ['role_id' => 1, 'permission_id' => 9],  // delete_users
            ['role_id' => 1, 'permission_id' => 10], // view_reports
            ['role_id' => 1, 'permission_id' => 11], // manage_transfers
            ['role_id' => 1, 'permission_id' => 12], // manage_maintenance
            ['role_id' => 1, 'permission_id' => 13], // manage_disposals
            ['role_id' => 1, 'permission_id' => 14], // view_logs
            ['role_id' => 1, 'permission_id' => 15], // view_roles
            ['role_id' => 1, 'permission_id' => 16], // manage_roles
            ['role_id' => 1, 'permission_id' => 17], // view_permissions
            ['role_id' => 1, 'permission_id' => 18], // manage_permissions
            ['role_id' => 1, 'permission_id' => 19], // system_admin
            ['role_id' => 1, 'permission_id' => 20], // import_export_access
            ['role_id' => 1, 'permission_id' => 21], // template_download
            ['role_id' => 1, 'permission_id' => 22], // data_export
            ['role_id' => 1, 'permission_id' => 23], // data_import
            ['role_id' => 1, 'permission_id' => 24], // bulk_export
            ['role_id' => 1, 'permission_id' => 25], // import_validation
            ['role_id' => 1, 'permission_id' => 26], // import_status
            ['role_id' => 1, 'permission_id' => 27], // import_history
            ['role_id' => 1, 'permission_id' => 28], // import_details
            ['role_id' => 1, 'permission_id' => 29], // error_reports
            ['role_id' => 1, 'permission_id' => 30], // error_download
            ['role_id' => 1, 'permission_id' => 31], // serial_generation
            ['role_id' => 1, 'permission_id' => 32], // serial_validation
            ['role_id' => 1, 'permission_id' => 33], // serial_stats
            ['role_id' => 1, 'permission_id' => 34], // field_validation
            ['role_id' => 1, 'permission_id' => 35], // data_lookup
            ['role_id' => 1, 'permission_id' => 36], // field_mapping
            ['role_id' => 1, 'permission_id' => 37], // import_progress
            ['role_id' => 1, 'permission_id' => 38], // admin_access
            ['role_id' => 1, 'permission_id' => 39], // view_maintenance
            ['role_id' => 1, 'permission_id' => 40], // create_maintenance
            ['role_id' => 1, 'permission_id' => 41], // edit_maintenance
            ['role_id' => 1, 'permission_id' => 42], // delete_maintenance
            ['role_id' => 1, 'permission_id' => 43], // view_disposal
            ['role_id' => 1, 'permission_id' => 44], // create_disposal
            ['role_id' => 1, 'permission_id' => 45], // edit_disposal
            ['role_id' => 1, 'permission_id' => 46], // delete_disposal
            ['role_id' => 1, 'permission_id' => 47], // edit_maintenance
            ['role_id' => 1, 'permission_id' => 48], // delete_maintenance
            ['role_id' => 1, 'permission_id' => 49], // view_disposal
            ['role_id' => 1, 'permission_id' => 50], // create_disposal
            ['role_id' => 1, 'permission_id' => 51], // edit_disposal
            ['role_id' => 1, 'permission_id' => 52], // delete_disposal
            
            // Admin - Most permissions except system admin
            ['role_id' => 2, 'permission_id' => 1],  // view_assets
            ['role_id' => 2, 'permission_id' => 2],  // create_assets
            ['role_id' => 2, 'permission_id' => 3],  // edit_assets
            ['role_id' => 2, 'permission_id' => 4],  // delete_assets
            ['role_id' => 2, 'permission_id' => 5],  // assign_assets
            ['role_id' => 2, 'permission_id' => 6],  // view_users
            ['role_id' => 2, 'permission_id' => 7],  // create_users
            ['role_id' => 2, 'permission_id' => 8],  // edit_users
            ['role_id' => 2, 'permission_id' => 10], // manage_user_roles
            ['role_id' => 2, 'permission_id' => 11], // assign_roles
            ['role_id' => 2, 'permission_id' => 12], // remove_roles
            ['role_id' => 2, 'permission_id' => 13], // bulk_assign_roles
            ['role_id' => 2, 'permission_id' => 14], // view_user_roles
            ['role_id' => 2, 'permission_id' => 15], // view_reports
            ['role_id' => 2, 'permission_id' => 16], // manage_transfers
            ['role_id' => 2, 'permission_id' => 17], // manage_maintenance
            ['role_id' => 2, 'permission_id' => 18], // manage_disposals
            ['role_id' => 2, 'permission_id' => 19], // view_logs
            ['role_id' => 2, 'permission_id' => 20], // view_roles
            ['role_id' => 2, 'permission_id' => 21], // manage_roles
            ['role_id' => 2, 'permission_id' => 22], // view_permissions
            ['role_id' => 2, 'permission_id' => 24], // manage_permissions
            ['role_id' => 2, 'permission_id' => 25], // import_export_access
            ['role_id' => 2, 'permission_id' => 26], // template_download
            ['role_id' => 2, 'permission_id' => 27], // template_preview
            ['role_id' => 2, 'permission_id' => 28], // data_export
            ['role_id' => 2, 'permission_id' => 29], // data_import
            ['role_id' => 2, 'permission_id' => 30], // bulk_export
            ['role_id' => 2, 'permission_id' => 31], // import_validation
            ['role_id' => 2, 'permission_id' => 32], // import_status
            ['role_id' => 2, 'permission_id' => 33], // import_history
            ['role_id' => 2, 'permission_id' => 34], // import_details
            ['role_id' => 2, 'permission_id' => 35], // error_reports
            ['role_id' => 2, 'permission_id' => 36], // error_download
            
            // Manager - View and approve permissions
            ['role_id' => 3, 'permission_id' => 1],  // view_assets
            ['role_id' => 3, 'permission_id' => 5],  // assign_assets
            ['role_id' => 3, 'permission_id' => 6],  // view_users
            ['role_id' => 3, 'permission_id' => 15], // view_reports
            ['role_id' => 3, 'permission_id' => 16], // manage_transfers
            ['role_id' => 3, 'permission_id' => 18], // manage_disposals
            ['role_id' => 3, 'permission_id' => 25], // import_export_access
            ['role_id' => 3, 'permission_id' => 26], // template_download
            ['role_id' => 3, 'permission_id' => 28], // data_export
            
            // User - Basic view permissions
            ['role_id' => 4, 'permission_id' => 1],  // view_assets
            
            // IT Support - Technical permissions
            ['role_id' => 5, 'permission_id' => 1],  // view_assets
            ['role_id' => 5, 'permission_id' => 3],  // edit_assets
            ['role_id' => 5, 'permission_id' => 5],  // assign_assets
            ['role_id' => 5, 'permission_id' => 17], // manage_maintenance
            ['role_id' => 5, 'permission_id' => 25], // import_export_access
            ['role_id' => 5, 'permission_id' => 26], // template_download
            ['role_id' => 5, 'permission_id' => 28], // data_export
            ['role_id' => 5, 'permission_id' => 29], // data_import
        ];

        foreach ($rolePermissions as $rolePermission) {
            RolePermission::firstOrCreate(
                ['role_id' => $rolePermission['role_id'], 'permission_id' => $rolePermission['permission_id']],
                $rolePermission
            );
        }
    }
}
