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
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            DepartmentSeeder::class,
            UserSeeder::class,
            AssetCategorySeeder::class,
            VendorSeeder::class,
            AssetSeeder::class,
            ComputerSeeder::class,
            MonitorSeeder::class,
            PrinterSeeder::class,
            PeripheralSeeder::class,
            LogSeeder::class,
            // Note: Other seeders (Transfer, Maintenance, Disposal)
            // can be added here when sample data is needed
        ]);
    }
}
