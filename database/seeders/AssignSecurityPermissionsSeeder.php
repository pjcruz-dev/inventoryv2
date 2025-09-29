<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class AssignSecurityPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions if they don't exist
        $permissions = [
            'view_security_audit',
            'export_security_audit',
            'manage_security_audit'
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
            $this->command->info("Permissions assigned to user: {$user->name} (ID: {$user->id})");
        }

        // Also assign to roles
        $roles = Role::all();
        foreach ($roles as $role) {
            foreach ($permissions as $permission) {
                $role->givePermissionTo($permission);
            }
        }
        
        $this->command->info('Security audit permissions assigned to all users and roles successfully!');
    }
}
