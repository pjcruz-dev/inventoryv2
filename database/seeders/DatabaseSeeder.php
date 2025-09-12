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
        // Seed in order of dependencies
        $this->call([
            // Core system data
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            DepartmentSeeder::class,
            
            // User data
            UserSeeder::class,
            
            // Asset foundation data
            AssetCategorySeeder::class,
            VendorSeeder::class,
            
            // Asset data
            AssetSeeder::class,
            
            // Asset relationships and activities
            AssetAssignmentSeeder::class,
            AssetTimelineSeeder::class,
            MaintenanceSeeder::class,
            DisposalSeeder::class,
            
            // Activity logs (must be last to capture all activities)
            LogSeeder::class,
        ]);
        
        $this->command->info('\n=== Database Seeding Completed Successfully! ===');
        $this->command->info('✓ 100 Users created with proper role assignments');
        $this->command->info('✓ 200 Assets created across 5 categories');
        $this->command->info('✓ Asset assignments and timeline events generated');
        $this->command->info('✓ Comprehensive activity logs created');
        $this->command->info('✓ All relationships and dependencies established');
    }
}
