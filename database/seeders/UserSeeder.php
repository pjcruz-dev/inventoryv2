<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the Super Admin role
        $superAdminRole = \App\Models\Role::where('name', 'Super Admin')->first();
        if (!$superAdminRole) {
            $this->command->error('Super Admin role not found. Please run RoleSeeder first.');
            return;
        }

        // Get the first available department
        $department = \App\Models\Department::first();
        if (!$department) {
            $this->command->error('No departments found. Please run DepartmentSeeder first.');
            return;
        }

        // Create only the admin user as requested
        $adminUser = [
            'employee_no' => 'EMP001',
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'department_id' => $department->id,
            'position' => 'System Administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123123123'),
            'role_id' => $superAdminRole->id,
            'status' => 1
        ];
        
        $user = User::firstOrCreate(
            ['email' => $adminUser['email']],
            $adminUser
        );
        
        // Assign Spatie role to the user
        if (!$user->hasRole('Super Admin')) {
            $user->assignRole('Super Admin');
        }
        
        $this->command->info('✓ Super Admin user created: admin@gmail.com');
        $this->command->info('✓ Super Admin role assigned to user');
    }
}
