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
        $users = [
            [
                'employee_no' => 'EMP001',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'department_id' => 1, // Information Technology
                'position' => 'System Administrator',
                'email' => 'admin@company.com',
                'password' => Hash::make('password123'),
                'role_id' => 1, // Super Admin
                'status' => 1
            ],
            [
                'employee_no' => 'EMP002',
                'first_name' => 'John',
                'last_name' => 'Manager',
                'department_id' => 1, // Information Technology
                'position' => 'IT Manager',
                'email' => 'john.manager@company.com',
                'password' => Hash::make('password123'),
                'role_id' => 2, // Admin
                'status' => 1
            ],
            [
                'employee_no' => 'EMP003',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'department_id' => 2, // Human Resources
                'position' => 'HR Specialist',
                'email' => 'jane.smith@company.com',
                'password' => Hash::make('password123'),
                'role_id' => 4, // User
                'status' => 1
            ],
            [
                'employee_no' => 'EMP004',
                'first_name' => 'Mike',
                'last_name' => 'Support',
                'department_id' => 1, // Information Technology
                'position' => 'IT Support Specialist',
                'email' => 'mike.support@company.com',
                'password' => Hash::make('password123'),
                'role_id' => 5, // IT Support
                'status' => 1
            ],
            [
                'employee_no' => 'EMP005',
                'first_name' => 'Sarah',
                'last_name' => 'Finance',
                'department_id' => 3, // Finance
                'position' => 'Finance Manager',
                'email' => 'sarah.finance@company.com',
                'password' => Hash::make('password123'),
                'role_id' => 3, // Manager
                'status' => 1
            ]
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
