<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Log;

class LogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $logs = [
            [
                'category' => 'Asset',
                'asset_id' => 1,
                'user_id' => 1,
                'role_id' => 1,
                'department_id' => 1,
                'event_type' => 'Asset Assigned',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'remarks' => 'Asset COMP001 (Dell OptiPlex 7090) assigned to Admin User on 2024-01-15'
            ],
            [
                'category' => 'Asset',
                'asset_id' => 2,
                'user_id' => 2,
                'role_id' => 2,
                'department_id' => 1,
                'event_type' => 'Asset Assigned',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'remarks' => 'Asset COMP002 (HP EliteBook 850) assigned to John Manager on 2024-02-20'
            ],
            [
                'category' => 'Asset',
                'asset_id' => 1,
                'user_id' => 1,
                'role_id' => 1,
                'department_id' => 1,
                'event_type' => 'Asset Reassigned',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'remarks' => 'Asset COMP001 (Dell OptiPlex 7090) reassigned from Admin User to Jane Smith on 2024-03-01'
            ]
        ];

        foreach ($logs as $log) {
            Log::create($log);
        }
    }
}
