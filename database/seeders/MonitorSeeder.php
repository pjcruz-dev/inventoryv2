<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Monitor;
use App\Models\Asset;

class MonitorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $monitors = [
            [
                'asset_tag' => 'MON001', // Dell UltraSharp U2720Q
                'size' => '27 inch',
                'resolution' => '3840x2160',
                'panel_type' => 'IPS'
            ],
            [
                'asset_tag' => 'MON002', // HP E24 G5 Monitor
                'size' => '24 inch',
                'resolution' => '1920x1080',
                'panel_type' => 'IPS'
            ],
            [
                'asset_tag' => 'MON003', // Lenovo ThinkVision P27h-20
                'size' => '27 inch',
                'resolution' => '2560x1440',
                'panel_type' => 'IPS'
            ],
            [
                'asset_tag' => 'MON004', // ASUS ProArt PA278QV
                'size' => '27 inch',
                'resolution' => '2560x1440',
                'panel_type' => 'IPS'
            ],
            [
                'asset_tag' => 'MON005', // Acer Predator XB273K
                'size' => '27 inch',
                'resolution' => '3840x2160',
                'panel_type' => 'IPS'
            ],
            [
                'asset_tag' => 'MON006', // Samsung Odyssey G7
                'size' => '32 inch',
                'resolution' => '2560x1440',
                'panel_type' => 'VA'
            ],
            [
                'asset_tag' => 'MON007', // LG UltraWide 34WN80C
                'size' => '34 inch',
                'resolution' => '3440x1440',
                'panel_type' => 'IPS'
            ],
            [
                'asset_tag' => 'MON008', // Dell S2722DC
                'size' => '27 inch',
                'resolution' => '2560x1440',
                'panel_type' => 'IPS'
            ],
            [
                'asset_tag' => 'MON009', // HP Z27k G3 4K
                'size' => '27 inch',
                'resolution' => '3840x2160',
                'panel_type' => 'IPS'
            ],
            [
                'asset_tag' => 'MON010', // ASUS TUF Gaming VG27AQ
                'size' => '27 inch',
                'resolution' => '2560x1440',
                'panel_type' => 'IPS'
            ],
            [
                'asset_tag' => 'MON011', // Acer Nitro XV272U
                'size' => '27 inch',
                'resolution' => '2560x1440',
                'panel_type' => 'IPS'
            ],
            [
                'asset_tag' => 'MON012', // Samsung M7 Smart Monitor
                'size' => '32 inch',
                'resolution' => '3840x2160',
                'panel_type' => 'VA'
            ],
            [
                'asset_tag' => 'MON013', // LG 27UP850-W
                'size' => '27 inch',
                'resolution' => '3840x2160',
                'panel_type' => 'IPS'
            ],
            [
                'asset_tag' => 'MON014', // Dell P2423D
                'size' => '24 inch',
                'resolution' => '2560x1440',
                'panel_type' => 'IPS'
            ],
            [
                'asset_tag' => 'MON015', // HP EliteDisplay E243
                'size' => '24 inch',
                'resolution' => '1920x1080',
                'panel_type' => 'IPS'
            ],
            [
                'asset_tag' => 'MON016', // ASUS VP28UQG
                'size' => '28 inch',
                'resolution' => '3840x2160',
                'panel_type' => 'TN'
            ]
        ];

        foreach ($monitors as $monitor) {
            $asset = Asset::where('asset_tag', $monitor['asset_tag'])->first();
            if ($asset) {
                $monitorData = [
                    'asset_id' => $asset->id,
                    'size' => $monitor['size'],
                    'resolution' => $monitor['resolution'],
                    'panel_type' => $monitor['panel_type']
                ];
                Monitor::firstOrCreate(
                    ['asset_id' => $asset->id],
                    $monitorData
                );
            }
        }
    }
}
