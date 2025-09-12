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
        $firstNames = [
            'John', 'Jane', 'Michael', 'Sarah', 'David', 'Emily', 'Robert', 'Lisa', 'James', 'Maria',
            'William', 'Jennifer', 'Richard', 'Linda', 'Charles', 'Elizabeth', 'Thomas', 'Barbara', 'Christopher', 'Susan',
            'Daniel', 'Jessica', 'Matthew', 'Karen', 'Anthony', 'Nancy', 'Mark', 'Betty', 'Donald', 'Helen',
            'Steven', 'Sandra', 'Paul', 'Donna', 'Andrew', 'Carol', 'Joshua', 'Ruth', 'Kenneth', 'Sharon',
            'Kevin', 'Michelle', 'Brian', 'Laura', 'George', 'Sarah', 'Timothy', 'Kimberly', 'Ronald', 'Deborah'
        ];
        
        $lastNames = [
            'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez',
            'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin',
            'Lee', 'Perez', 'Thompson', 'White', 'Harris', 'Sanchez', 'Clark', 'Ramirez', 'Lewis', 'Robinson',
            'Walker', 'Young', 'Allen', 'King', 'Wright', 'Scott', 'Torres', 'Nguyen', 'Hill', 'Flores',
            'Green', 'Adams', 'Nelson', 'Baker', 'Hall', 'Rivera', 'Campbell', 'Mitchell', 'Carter', 'Roberts'
        ];
        
        $positions = [
            'Software Developer', 'System Administrator', 'Business Analyst', 'Project Manager', 'Data Analyst',
            'HR Specialist', 'Finance Analyst', 'Marketing Coordinator', 'Sales Representative', 'Operations Manager',
            'IT Support Specialist', 'Quality Assurance', 'Product Manager', 'Customer Service Rep', 'Accountant',
            'Administrative Assistant', 'Network Engineer', 'Database Administrator', 'Security Analyst', 'Web Developer'
        ];
        
        // Create predefined admin users first
        $adminUsers = [
            [
                'employee_no' => 'EMP001',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'department_id' => 1,
                'position' => 'System Administrator',
                'email' => 'admin@company.com',
                'password' => Hash::make('123123123'),
                'role_id' => 1, // Super Admin
                'status' => 1
            ],
            [
                'employee_no' => 'EMP002',
                'first_name' => 'John',
                'last_name' => 'Manager',
                'department_id' => 1,
                'position' => 'IT Manager',
                'email' => 'john.manager@company.com',
                'password' => Hash::make('123123123'),
                'role_id' => 2, // Admin
                'status' => 1
            ]
        ];
        
        foreach ($adminUsers as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
        
        // Generate 98 additional users to reach 100 total
        for ($i = 3; $i <= 100; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $departmentId = rand(1, 7); // 7 departments available
            $position = $positions[array_rand($positions)];
            
            // Role distribution: 2 Super Admin, 8 Admin, 15 Manager, 15 IT Support, 60 User
            if ($i <= 2) {
                $roleId = 1; // Super Admin
            } elseif ($i <= 10) {
                $roleId = 2; // Admin
            } elseif ($i <= 25) {
                $roleId = 3; // Manager
            } elseif ($i <= 40) {
                $roleId = 5; // IT Support
            } else {
                $roleId = 4; // User
            }
            
            $email = strtolower($firstName . '.' . $lastName . $i . '@company.com');
            
            User::firstOrCreate(
                ['email' => $email],
                [
                    'employee_no' => 'EMP' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'department_id' => $departmentId,
                    'position' => $position,
                    'email' => $email,
                    'password' => Hash::make('123123123'),
                    'role_id' => $roleId,
                    'status' => 1
                ]
            );
        }
    }
}
