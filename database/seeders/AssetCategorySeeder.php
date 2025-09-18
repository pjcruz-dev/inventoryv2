<?php

namespace Database\Seeders;

use App\Models\AssetCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class AssetCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Computer Hardware',
                'description' => 'Desktop computers, laptops, servers, and internal computer components (RAM, CPU, motherboards, etc.)'
            ],
            [
                'name' => 'Monitors',
                'description' => 'Computer monitors, displays, and screen-related equipment'
            ],
            [
                'name' => 'Printers',
                'description' => 'Printers, scanners, multifunction devices, and printing equipment'
            ],
            [
                'name' => 'Peripherals',
                'description' => 'External devices like keyboards, mice, webcams, speakers, and other computer accessories'
            ],
            [
                'name' => 'Network Equipment',
                'description' => 'Routers, switches, access points, cables, and networking hardware'
            ],
            [
                'name' => 'Mobile Devices',
                'description' => 'Smartphones, tablets, mobile hotspots, and portable devices'
            ],
            [
                'name' => 'Office Equipment',
                'description' => 'Furniture, projectors, whiteboards, and general office equipment'
            ],
            [
                'name' => 'Software Licenses',
                'description' => 'Software licenses, subscriptions, and digital assets'
            ],
            [
                'name' => 'Storage Devices',
                'description' => 'External hard drives, USB drives, NAS devices, and storage equipment'
            ],
            [
                'name' => 'Audio/Video Equipment',
                'description' => 'Cameras, microphones, headsets, conference equipment, and AV devices'
            ]
        ];

        foreach ($categories as $categoryData) {
            $category = AssetCategory::firstOrCreate(
                ['name' => $categoryData['name']],
                ['description' => $categoryData['description']]
            );

            if ($category->wasRecentlyCreated) {
                Log::info('Asset category created via seeder', [
                    'category_id' => $category->id,
                    'category_name' => $category->name
                ]);
                $this->command->info("Created category: {$category->name}");
            } else {
                $this->command->info("Category already exists: {$category->name}");
            }
        }

        $this->command->info('Asset categories seeding completed!');
    }
}