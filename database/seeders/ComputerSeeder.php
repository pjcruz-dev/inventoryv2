<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Computer;

class ComputerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Computer::create([
            'asset_id' => 1,
            'processor' => 'Intel Core i7-11700',
            'ram' => '16GB DDR4',
            'storage' => '512GB SSD',
            'os' => 'Windows 11 Pro',
        ]);

        Computer::create([
            'asset_id' => 2,
            'processor' => 'Intel Core i5-1135G7',
            'ram' => '8GB DDR4',
            'storage' => '256GB SSD',
            'os' => 'Windows 11 Pro',
        ]);
    }
}
