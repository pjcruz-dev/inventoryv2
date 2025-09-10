<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AssetCategory;

class AssetCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Computer Hardware', 'description' => 'Desktop computers, laptops, and workstations'],
            ['name' => 'Monitors & Displays', 'description' => 'Computer monitors and display devices'],
            ['name' => 'Printers & Scanners', 'description' => 'Printing and scanning equipment'],
            ['name' => 'Peripherals', 'description' => 'Keyboards, mice, and other input devices'],
            ['name' => 'Network Equipment', 'description' => 'Routers, switches, and networking hardware'],
            ['name' => 'Mobile Devices', 'description' => 'Smartphones, tablets, and mobile equipment'],
            ['name' => 'Audio Visual', 'description' => 'Projectors, speakers, and AV equipment'],
            ['name' => 'Furniture', 'description' => 'Office furniture and fixtures']
        ];

        foreach ($categories as $category) {
            AssetCategory::create($category);
        }
    }
}
