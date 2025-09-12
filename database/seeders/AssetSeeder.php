<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Asset;
use Carbon\Carbon;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asset models for each category
        $laptopModels = [
            'Dell Latitude 7420', 'HP EliteBook 850 G8', 'Lenovo ThinkPad X1 Carbon',
            'ASUS ZenBook Pro', 'Acer Swift 3', 'MacBook Pro 14"', 'Surface Laptop 4',
            'Dell XPS 13', 'HP Spectre x360', 'Lenovo Yoga 9i'
        ];
        
        $desktopModels = [
            'Dell OptiPlex 7090', 'HP EliteDesk 800 G8', 'Lenovo ThinkCentre M90q',
            'ASUS ExpertCenter D7', 'Acer Veriton X', 'iMac 24"', 'Surface Studio 2',
            'Dell Precision 3660'
        ];
        
        $monitorModels = [
            'Dell UltraSharp U2720Q', 'HP E24 G5', 'Lenovo ThinkVision P27h',
            'ASUS ProArt PA278QV', 'Acer Predator XB273K', 'Samsung Odyssey G7',
            'LG UltraWide 34WN80C', 'BenQ PD3220U'
        ];
        
        $printerModels = [
            'HP LaserJet Pro M404dn', 'Canon imageRUNNER ADVANCE', 'Brother HL-L3270CDW',
            'Epson EcoTank ET-4760', 'Xerox VersaLink C405', 'Ricoh SP 330DN'
        ];
        
        $peripheralModels = [
            'Logitech MX Master 3S', 'Logitech MX Keys Advanced', 'Logitech Brio 4K Webcam',
            'Dell Wireless Keyboard', 'HP USB-C Dock', 'Jabra Evolve2 65'
        ];
        
        $assetCounter = 1;
        
        // Generate 50 Laptops
        for ($i = 1; $i <= 50; $i++) {
            Asset::firstOrCreate(
                ['asset_tag' => 'LAP' . str_pad($i, 3, '0', STR_PAD_LEFT)],
                [
                    'category_id' => 1, // Computer Hardware
                    'vendor_id' => rand(1, 10),
                    'name' => $laptopModels[array_rand($laptopModels)],
                    'description' => 'Business laptop for mobile work',
                    'serial_number' => 'LAP' . str_pad($i, 6, '0', STR_PAD_LEFT),
                    'purchase_date' => Carbon::now()->subDays(rand(30, 365))->format('Y-m-d'),
                    'warranty_end' => Carbon::now()->addYears(3)->format('Y-m-d'),
                    'cost' => rand(1200, 2500) + (rand(0, 99) / 100),
                    'status' => ['active', 'maintenance', 'retired'][rand(0, 2)]
                ]
            );
        }
        
        // Generate 40 Desktops
        for ($i = 1; $i <= 40; $i++) {
            Asset::firstOrCreate(
                ['asset_tag' => 'DSK' . str_pad($i, 3, '0', STR_PAD_LEFT)],
                [
                    'category_id' => 1, // Computer Hardware
                    'vendor_id' => rand(1, 10),
                    'name' => $desktopModels[array_rand($desktopModels)],
                    'description' => 'Desktop computer for office use',
                    'serial_number' => 'DSK' . str_pad($i, 6, '0', STR_PAD_LEFT),
                    'purchase_date' => Carbon::now()->subDays(rand(30, 365))->format('Y-m-d'),
                    'warranty_end' => Carbon::now()->addYears(3)->format('Y-m-d'),
                    'cost' => rand(800, 1800) + (rand(0, 99) / 100),
                    'status' => ['active', 'maintenance', 'retired'][rand(0, 2)]
                ]
            );
        }
        
        // Generate 60 Monitors
        for ($i = 1; $i <= 60; $i++) {
            Asset::firstOrCreate(
                ['asset_tag' => 'MON' . str_pad($i, 3, '0', STR_PAD_LEFT)],
                [
                    'category_id' => 2, // Monitors & Displays
                    'vendor_id' => rand(1, 10),
                    'name' => $monitorModels[array_rand($monitorModels)],
                    'description' => 'Professional display monitor',
                    'serial_number' => 'MON' . str_pad($i, 6, '0', STR_PAD_LEFT),
                    'purchase_date' => Carbon::now()->subDays(rand(30, 365))->format('Y-m-d'),
                    'warranty_end' => Carbon::now()->addYears(3)->format('Y-m-d'),
                    'cost' => rand(200, 800) + (rand(0, 99) / 100),
                    'status' => ['active', 'maintenance', 'retired'][rand(0, 2)]
                ]
            );
        }
        
        // Generate 30 Printers
        for ($i = 1; $i <= 30; $i++) {
            Asset::firstOrCreate(
                ['asset_tag' => 'PRT' . str_pad($i, 3, '0', STR_PAD_LEFT)],
                [
                    'category_id' => 3, // Printers & Scanners
                    'vendor_id' => rand(1, 10),
                    'name' => $printerModels[array_rand($printerModels)],
                    'description' => 'Office printer for document printing',
                    'serial_number' => 'PRT' . str_pad($i, 6, '0', STR_PAD_LEFT),
                    'purchase_date' => Carbon::now()->subDays(rand(30, 365))->format('Y-m-d'),
                    'warranty_end' => Carbon::now()->addYears(2)->format('Y-m-d'),
                    'cost' => rand(200, 2500) + (rand(0, 99) / 100),
                    'status' => ['active', 'maintenance', 'retired'][rand(0, 2)]
                ]
            );
        }
        
        // Generate 20 Peripherals
        for ($i = 1; $i <= 20; $i++) {
            Asset::firstOrCreate(
                ['asset_tag' => 'PER' . str_pad($i, 3, '0', STR_PAD_LEFT)],
                [
                    'category_id' => 4, // Peripherals
                    'vendor_id' => rand(1, 10),
                    'name' => $peripheralModels[array_rand($peripheralModels)],
                    'description' => 'Computer peripheral device',
                    'serial_number' => 'PER' . str_pad($i, 6, '0', STR_PAD_LEFT),
                    'purchase_date' => Carbon::now()->subDays(rand(30, 365))->format('Y-m-d'),
                    'warranty_end' => Carbon::now()->addYears(2)->format('Y-m-d'),
                    'cost' => rand(50, 300) + (rand(0, 99) / 100),
                    'status' => ['active', 'maintenance', 'retired'][rand(0, 2)]
                ]
            );
        }
    }
}
