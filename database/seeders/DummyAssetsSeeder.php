<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Vendor;
use App\Models\Computer;
use App\Models\Monitor;
use App\Models\Printer;
use App\Models\Peripheral;
use Carbon\Carbon;

class DummyAssetsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create categories
        $computerCategory = AssetCategory::firstOrCreate(
            ['name' => 'Computer Hardware'],
            ['description' => 'Computer and laptop devices', 'color' => '#007bff']
        );

        $monitorCategory = AssetCategory::firstOrCreate(
            ['name' => 'Monitors'],
            ['description' => 'Display monitors', 'color' => '#28a745']
        );

        $printerCategory = AssetCategory::firstOrCreate(
            ['name' => 'Printers'],
            ['description' => 'Printing devices', 'color' => '#ffc107']
        );

        $peripheralCategory = AssetCategory::firstOrCreate(
            ['name' => 'Peripherals'],
            ['description' => 'Keyboard, mouse, and other peripherals', 'color' => '#17a2b8']
        );

        // Get or create vendors
        $vendors = [
            'Dell' => Vendor::firstOrCreate(['name' => 'Dell'], [
                'contact_person' => 'John Dell',
                'email' => 'sales@dell.com',
                'phone' => '1-800-DELL',
                'address' => 'Dell Way, Round Rock, TX'
            ]),
            'HP' => Vendor::firstOrCreate(['name' => 'HP'], [
                'contact_person' => 'HP Support',
                'email' => 'sales@hp.com',
                'phone' => '1-800-HP',
                'address' => 'HP Boulevard, Palo Alto, CA'
            ]),
            'Lenovo' => Vendor::firstOrCreate(['name' => 'Lenovo'], [
                'contact_person' => 'Lenovo Sales',
                'email' => 'sales@lenovo.com',
                'phone' => '1-800-LENOVO',
                'address' => 'Lenovo Way, Beijing, China'
            ]),
            'Samsung' => Vendor::firstOrCreate(['name' => 'Samsung'], [
                'contact_person' => 'Samsung Support',
                'email' => 'sales@samsung.com',
                'phone' => '1-800-SAMSUNG',
                'address' => 'Samsung Road, Seoul, South Korea'
            ]),
            'Logitech' => Vendor::firstOrCreate(['name' => 'Logitech'], [
                'contact_person' => 'Logitech Sales',
                'email' => 'sales@logitech.com',
                'phone' => '1-800-LOG',
                'address' => 'Logitech Circle, Lausanne, Switzerland'
            ]),
        ];

        $this->command->info('Creating dummy assets...');

        $timestamp = date('His'); // Hours, Minutes, Seconds

        // Create 10 Computers
        for ($i = 1; $i <= 10; $i++) {
            $asset = Asset::create([
                'asset_tag' => 'COMP-TEST-' . $timestamp . '-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'category_id' => $computerCategory->id,
                'vendor_id' => $vendors['Dell']->id,
                'name' => 'Dell Latitude ' . (5000 + $i * 10),
                'description' => 'Test laptop for bulk assignment',
                'serial_number' => 'DL' . date('Y') . '-' . uniqid(),
                'model' => 'Latitude ' . (5000 + $i * 10),
                'purchase_date' => Carbon::now()->subMonths(rand(1, 12)),
                'warranty_end' => Carbon::now()->addYears(3),
                'cost' => rand(800, 1500) + (rand(0, 99) / 100),
                'po_number' => 'PO-2024-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'entity' => collect(['MIDC', 'PHILTOWER', 'PRIMUS'])->random(),
                'lifespan' => 5,
                'location' => 'IT Storage Room',
                'status' => 'Available',
                'movement' => 'Return',
                'notes' => 'Created for bulk assignment testing',
            ]);

            Computer::create([
                'asset_id' => $asset->id,
                'computer_type' => collect(['Laptop', 'Desktop'])->random(),
                'processor' => 'Intel Core i' . rand(5, 7) . '-' . rand(10000, 13000),
                'memory' => collect(['8GB', '16GB', '32GB'])->random(),
                'storage' => collect(['256GB SSD', '512GB SSD', '1TB SSD'])->random(),
                'operating_system' => 'Windows 11 Pro',
                'graphics_card' => 'Intel Integrated Graphics',
            ]);
        }

        // Create 8 Monitors
        for ($i = 1; $i <= 8; $i++) {
            $asset = Asset::create([
                'asset_tag' => 'MON-TEST-' . $timestamp . '-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'category_id' => $monitorCategory->id,
                'vendor_id' => $vendors['Samsung']->id,
                'name' => 'Samsung Monitor ' . (22 + $i),
                'description' => 'Test monitor for bulk assignment',
                'serial_number' => 'SM' . date('Y') . '-' . uniqid(),
                'model' => 'S' . (22 + $i) . 'F350',
                'purchase_date' => Carbon::now()->subMonths(rand(1, 12)),
                'warranty_end' => Carbon::now()->addYears(2),
                'cost' => rand(150, 350) + (rand(0, 99) / 100),
                'po_number' => 'PO-2024-' . str_pad(100 + $i, 4, '0', STR_PAD_LEFT),
                'entity' => collect(['MIDC', 'PHILTOWER', 'PRIMUS'])->random(),
                'lifespan' => 7,
                'location' => 'IT Storage Room',
                'status' => 'Available',
                'movement' => 'Return',
                'notes' => 'Created for bulk assignment testing',
            ]);

            Monitor::create([
                'asset_id' => $asset->id,
                'size' => (22 + $i) . '"',
                'resolution' => collect(['1920x1080', '2560x1440', '3840x2160'])->random(),
                'panel_type' => collect(['IPS', 'VA', 'TN'])->random(),
            ]);
        }

        // Create 5 Printers
        for ($i = 1; $i <= 5; $i++) {
            $asset = Asset::create([
                'asset_tag' => 'PRT-TEST-' . $timestamp . '-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'category_id' => $printerCategory->id,
                'vendor_id' => $vendors['HP']->id,
                'name' => 'HP LaserJet Pro ' . (1000 + $i * 100),
                'description' => 'Test printer for bulk assignment',
                'serial_number' => 'HP' . date('Y') . '-' . uniqid(),
                'model' => 'LaserJet Pro M' . (1000 + $i * 100),
                'purchase_date' => Carbon::now()->subMonths(rand(1, 12)),
                'warranty_end' => Carbon::now()->addYears(2),
                'cost' => rand(300, 800) + (rand(0, 99) / 100),
                'po_number' => 'PO-2024-' . str_pad(200 + $i, 4, '0', STR_PAD_LEFT),
                'entity' => collect(['MIDC', 'PHILTOWER', 'PRIMUS'])->random(),
                'lifespan' => 5,
                'location' => 'IT Storage Room',
                'status' => 'Available',
                'movement' => 'Return',
                'notes' => 'Created for bulk assignment testing',
            ]);

            Printer::create([
                'asset_id' => $asset->id,
                'type' => collect(['Laser', 'Inkjet'])->random(),
                'color_support' => (bool) rand(0, 1),
                'duplex' => (bool) rand(0, 1),
            ]);
        }

        // Create 7 Peripherals
        $peripheralTypes = ['Keyboard', 'Mouse', 'Headset', 'Webcam', 'USB Hub', 'External HDD', 'Docking Station'];
        for ($i = 1; $i <= 7; $i++) {
            $type = $peripheralTypes[$i - 1];
            $asset = Asset::create([
                'asset_tag' => 'PER-TEST-' . $timestamp . '-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'category_id' => $peripheralCategory->id,
                'vendor_id' => $vendors['Logitech']->id,
                'name' => 'Logitech ' . $type,
                'description' => 'Test ' . strtolower($type) . ' for bulk assignment',
                'serial_number' => 'LG' . date('Y') . '-' . uniqid(),
                'model' => 'MX-' . rand(100, 999),
                'purchase_date' => Carbon::now()->subMonths(rand(1, 12)),
                'warranty_end' => Carbon::now()->addYears(1),
                'cost' => rand(30, 150) + (rand(0, 99) / 100),
                'po_number' => 'PO-2024-' . str_pad(300 + $i, 4, '0', STR_PAD_LEFT),
                'entity' => collect(['MIDC', 'PHILTOWER', 'PRIMUS'])->random(),
                'lifespan' => 3,
                'location' => 'IT Storage Room',
                'status' => 'Available',
                'movement' => 'Return',
                'notes' => 'Created for bulk assignment testing',
            ]);

            Peripheral::create([
                'asset_id' => $asset->id,
                'type' => $type,
                'interface' => collect(['USB', 'Wireless', 'Bluetooth'])->random(),
            ]);
        }

        $this->command->info('✅ Successfully created 30 dummy assets:');
        $this->command->info('   - 10 Computers (COMP-TEST-' . $timestamp . '-01 to COMP-TEST-' . $timestamp . '-10)');
        $this->command->info('   - 8 Monitors (MON-TEST-' . $timestamp . '-01 to MON-TEST-' . $timestamp . '-08)');
        $this->command->info('   - 5 Printers (PRT-TEST-' . $timestamp . '-01 to PRT-TEST-' . $timestamp . '-05)');
        $this->command->info('   - 7 Peripherals (PER-TEST-' . $timestamp . '-01 to PER-TEST-' . $timestamp . '-07)');
        $this->command->info('');
        $this->command->info('All assets are set to "Available" status and ready for bulk assignment testing!');
    }
}

