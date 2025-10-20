<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EssentialDataSeeder extends Seeder
{
    /**
     * Run the database seeds for essential data only.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding essential data only...');
        
        $this->call([
            // Core system data required for admin access
            RoleSeeder::class,
            PermissionSeeder::class,
            SystemHealthPermissionsSeeder::class,
            SecurityPermissionsSeeder::class,
            ReportsPermissionsSeeder::class,
            RolePermissionSeeder::class,
            DepartmentSeeder::class,
            
            // Asset categories
            AssetCategorySeeder::class,
            
            // Admin user data
            UserSeeder::class,
        ]);
        
        $this->command->info('✅ Essential data seeding completed!');
        $this->command->info('✓ Roles and permissions configured');
        $this->command->info('✓ Admin user created with full access');
        $this->command->info('✓ Core system functionality ready');
    }
}
