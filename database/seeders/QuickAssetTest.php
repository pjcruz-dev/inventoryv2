<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssetCategory;
use App\Models\Vendor;
use App\Models\Department;
use App\Models\Asset;
use App\Models\Computer;

class QuickAssetTest extends Seeder
{
    /**
     * Run a quick test to verify the AssetSeeder will work
     */
    public function run(): void
    {
        $this->command->info('Running Quick Asset Test...');
        
        // Test computer type values
        $computerTypes = ['Desktop', 'Laptop', 'Server', 'Workstation'];
        $this->command->info('Valid computer types: ' . implode(', ', $computerTypes));
        
        // Test if we can create a computer with valid data
        try {
            // Get or create required data
            $category = AssetCategory::firstOrCreate(
                ['name' => 'Computer Hardware'],
                ['description' => 'Test category']
            );
            
            $vendor = Vendor::firstOrCreate(
                ['name' => 'Test Vendor'],
                [
                    'contact_person' => 'Test Person',
                    'email' => 'test@vendor.com',
                    'phone' => '123-456-7890',
                    'address' => 'Test Address'
                ]
            );
            
            $department = Department::firstOrCreate(
                ['name' => 'Test Department'],
                [
                    'description' => 'Test department',
                    'code' => 'TEST'
                ]
            );
            
            // Create a test asset
            $asset = Asset::create([
                'asset_tag' => 'TEST-0001',
                'category_id' => $category->id,
                'vendor_id' => $vendor->id,
                'name' => 'Test Computer',
                'description' => 'Test computer for validation',
                'serial_number' => 'TEST-SN-001',
                'purchase_date' => now()->subDays(30),
                'warranty_end' => now()->addYears(3),
                'cost' => 1000.00,
                'po_number' => 'PO-TEST-001',
                'entity' => 'PRIMUS',
                'lifespan' => 5,
                'location' => 'Test Location',
                'status' => 'Active',
                'movement' => 'Deployed Tagged',
                'department_id' => $department->id,
            ]);
            
            // Test computer creation with valid computer_type
            $computer = Computer::create([
                'asset_id' => $asset->id,
                'processor' => 'Intel Core i5-10400',
                'memory' => '16GB DDR4',
                'storage' => '512GB SSD',
                'operating_system' => 'Windows 11 Pro',
                'graphics_card' => 'Intel UHD Graphics 630',
                'computer_type' => 'Desktop', // Valid value
            ]);
            
            $this->command->info('✅ Test successful! Asset and Computer created successfully.');
            $this->command->info("Asset ID: {$asset->id}, Computer ID: {$computer->id}");
            
            // Clean up test data
            $computer->delete();
            $asset->delete();
            $this->command->info('🧹 Test data cleaned up.');
            
        } catch (\Exception $e) {
            $this->command->error('❌ Test failed: ' . $e->getMessage());
            throw $e;
        }
        
        $this->command->info('🎉 Quick test completed successfully! AssetSeeder should work now.');
    }
}
