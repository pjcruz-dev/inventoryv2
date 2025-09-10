<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Printer;
use App\Models\Asset;

class PrinterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $printers = [
            [
                'asset_tag' => 'PRT001', // Canon imageRUNNER ADVANCE
                'type' => 'Multifunction',
                'color_support' => true,
                'duplex' => true
            ],
            [
                'asset_tag' => 'PRT002', // HP LaserJet Pro M404dn
                'type' => 'Laser',
                'color_support' => false,
                'duplex' => true
            ],
            [
                'asset_tag' => 'PRT003', // Canon PIXMA TR8620
                'type' => 'Inkjet',
                'color_support' => true,
                'duplex' => true
            ],
            [
                'asset_tag' => 'PRT004', // Brother HL-L3270CDW
                'type' => 'Laser',
                'color_support' => true,
                'duplex' => true
            ],
            [
                'asset_tag' => 'PRT005', // Epson EcoTank ET-4760
                'type' => 'Inkjet',
                'color_support' => true,
                'duplex' => true
            ],
            [
                'asset_tag' => 'PRT006', // HP Color LaserJet Pro M454dw
                'type' => 'Laser',
                'color_support' => true,
                'duplex' => true
            ],
            [
                'asset_tag' => 'PRT007', // Canon MAXIFY GX7020
                'type' => 'Inkjet',
                'color_support' => true,
                'duplex' => true
            ],
            [
                'asset_tag' => 'PRT008', // Brother MFC-L8900CDW
                'type' => 'Multifunction',
                'color_support' => true,
                'duplex' => true
            ],
            [
                'asset_tag' => 'PRT009', // Epson WorkForce Pro WF-4830
                'type' => 'Multifunction',
                'color_support' => true,
                'duplex' => true
            ],
            [
                'asset_tag' => 'PRT010', // HP OfficeJet Pro 9015e
                'type' => 'Multifunction',
                'color_support' => true,
                'duplex' => true
            ],
            [
                'asset_tag' => 'PRT011', // Canon imageCLASS MF445dw
                'type' => 'Multifunction',
                'color_support' => false,
                'duplex' => true
            ],
            [
                'asset_tag' => 'PRT012', // Brother DCP-L2550DW
                'type' => 'Multifunction',
                'color_support' => false,
                'duplex' => true
            ],
            [
                'asset_tag' => 'PRT013', // Epson Expression Premium XP-7100
                'type' => 'Multifunction',
                'color_support' => true,
                'duplex' => false
            ],
            [
                'asset_tag' => 'PRT014', // HP LaserJet Enterprise M507dn
                'type' => 'Laser',
                'color_support' => false,
                'duplex' => true
            ],
            [
                'asset_tag' => 'PRT015', // Canon SELPHY CP1300
                'type' => 'Photo',
                'color_support' => true,
                'duplex' => false
            ]
        ];

        foreach ($printers as $printer) {
            $asset = Asset::where('asset_tag', $printer['asset_tag'])->first();
            if ($asset) {
                $printerData = [
                    'asset_id' => $asset->id,
                    'type' => $printer['type'],
                    'color_support' => $printer['color_support'],
                    'duplex' => $printer['duplex']
                ];
                Printer::firstOrCreate(
                    ['asset_id' => $asset->id],
                    $printerData
                );
            }
        }
    }
}
