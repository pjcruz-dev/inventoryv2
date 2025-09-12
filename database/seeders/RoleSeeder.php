<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'description' => 'Full system access with all permissions'
            ],
            [
                'name' => 'Admin',
                'description' => 'Administrative access to manage inventory'
            ],
            [
                'name' => 'Manager',
                'description' => 'Managerial access to view and approve transactions'
            ],
            [
                'name' => 'User',
                'description' => 'Basic user access to view assigned assets'
            ],
            [
                'name' => 'IT Support',
                'description' => 'Technical support access for maintenance and repairs'
            ]
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name'], 'guard_name' => 'web'],
                array_merge($role, ['guard_name' => 'web'])
            );
        }
    }
}
