<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed only essential data for admin functionality
        $this->call([
            // Core system data required for admin access
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            DepartmentSeeder::class,
            
            // Asset categories
            AssetCategorySeeder::class,
            
            // Admin user data
            UserSeeder::class,
        ]);
        
        $this->command->info('\n=== Essential Database Seeding Completed! ===');
        $this->command->info('✓ Roles and permissions configured');
        $this->command->info('✓ Admin user created with full access');
        $this->command->info('✓ Core system functionality ready');
    }
}
