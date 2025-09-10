<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Peripheral;
use App\Models\Asset;

class PeripheralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $peripherals = [
            [
                'asset_tag' => 'PER001', // Logitech MX Master 3S
                'type' => 'Mouse',
                'interface' => 'Wireless'
            ],
            [
                'asset_tag' => 'PER002', // Logitech MX Keys Advanced
                'type' => 'Keyboard',
                'interface' => 'Wireless'
            ],
            [
                'asset_tag' => 'PER003', // Logitech Brio 4K Webcam
                'type' => 'Webcam',
                'interface' => 'USB'
            ],
            // Additional Keyboard Records
            [
                'asset_tag' => 'PER004', // Razer BlackWidow V3
                'type' => 'Keyboard',
                'interface' => 'USB'
            ],
            [
                'asset_tag' => 'PER005', // Corsair K95 RGB Platinum
                'type' => 'Keyboard',
                'interface' => 'USB'
            ],
            [
                'asset_tag' => 'PER006', // Microsoft Surface Keyboard
                'type' => 'Keyboard',
                'interface' => 'Wireless'
            ],
            [
                'asset_tag' => 'PER007', // Apple Magic Keyboard
                'type' => 'Keyboard',
                'interface' => 'Wireless'
            ],
            [
                'asset_tag' => 'PER008', // Logitech G915 TKL
                'type' => 'Keyboard',
                'interface' => 'Wireless'
            ],
            [
                'asset_tag' => 'PER009', // HP Pavilion Wireless Keyboard 600
                'type' => 'Keyboard',
                'interface' => 'Wireless'
            ],
            [
                'asset_tag' => 'PER010', // Dell KB216 Wired Keyboard
                'type' => 'Keyboard',
                'interface' => 'USB'
            ],
            [
                'asset_tag' => 'PER011', // ASUS ROG Strix Scope
                'type' => 'Keyboard',
                'interface' => 'USB'
            ],
            [
                'asset_tag' => 'PER012', // Lenovo ThinkPad TrackPoint Keyboard II
                'type' => 'Keyboard',
                'interface' => 'Wireless'
            ],
            [
                'asset_tag' => 'PER013', // Logitech K380 Multi-Device
                'type' => 'Keyboard',
                'interface' => 'Wireless'
            ],
            // Additional Mouse Records
            [
                'asset_tag' => 'PER014', // Razer DeathAdder V3
                'type' => 'Mouse',
                'interface' => 'USB'
            ],
            [
                'asset_tag' => 'PER015', // Corsair Dark Core RGB Pro
                'type' => 'Mouse',
                'interface' => 'Wireless'
            ],
            [
                'asset_tag' => 'PER016', // Microsoft Surface Precision Mouse
                'type' => 'Mouse',
                'interface' => 'Wireless'
            ],
            [
                'asset_tag' => 'PER017', // Apple Magic Mouse 2
                'type' => 'Mouse',
                'interface' => 'Wireless'
            ],
            [
                'asset_tag' => 'PER018', // Logitech G Pro X Superlight
                'type' => 'Mouse',
                'interface' => 'Wireless'
            ],
            [
                'asset_tag' => 'PER019', // HP Z3700 Wireless Mouse
                'type' => 'Mouse',
                'interface' => 'Wireless'
            ],
            [
                'asset_tag' => 'PER020', // Dell MS116 Optical Mouse
                'type' => 'Mouse',
                'interface' => 'USB'
            ]
        ];

        foreach ($peripherals as $peripheral) {
            $asset = Asset::where('asset_tag', $peripheral['asset_tag'])->first();
            if ($asset) {
                $peripheralData = [
                    'asset_id' => $asset->id,
                    'type' => $peripheral['type'],
                    'interface' => $peripheral['interface']
                ];
                Peripheral::firstOrCreate(['asset_id' => $asset->id], $peripheralData);
            }
        }
    }
}
