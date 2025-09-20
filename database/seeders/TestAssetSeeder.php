<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class TestAssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Testing Asset Seeder...');
        
        try {
            // Run the AssetSeeder
            $this->call(AssetSeeder::class);
            
            $this->command->info('✅ Asset Seeder completed successfully!');
            $this->command->info('📊 Summary:');
            $this->command->info('   - 50 assets with related records (computers, monitors, printers, peripherals)');
            $this->command->info('   - 50 standalone assets (network equipment, mobile devices, office equipment, storage, software)');
            $this->command->info('   - Total: 100 assets created');
            
        } catch (\Exception $e) {
            $this->command->error('❌ Asset Seeder failed: ' . $e->getMessage());
            $this->command->error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}
