<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Maintenance;
use App\Models\Asset;
use App\Models\Vendor;
use Carbon\Carbon;

class MaintenanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assets = Asset::all();
        $vendors = Vendor::all();
        
        if ($assets->isEmpty() || $vendors->isEmpty()) {
            $this->command->warn('No assets or vendors found. Skipping maintenance seeding.');
            return;
        }
        
        $maintenanceTypes = [
            'Preventive Maintenance',
            'Corrective Maintenance', 
            'Hardware Repair',
            'Software Update',
            'Component Replacement',
            'Performance Optimization',
            'Security Patch',
            'Cleaning and Inspection'
        ];
        
        $issues = [
            'Screen flickering and display issues',
            'Keyboard keys not responding properly',
            'Hard drive making unusual noises',
            'Overheating during heavy usage',
            'Network connectivity problems',
            'Software crashes and freezing',
            'Battery not holding charge',
            'USB ports not functioning',
            'Audio output distorted',
            'Performance degradation over time'
        ];
        
        $repairActions = [
            'Replaced faulty display panel',
            'Cleaned keyboard and replaced switches',
            'Replaced hard drive with SSD',
            'Cleaned internal fans and applied thermal paste',
            'Updated network drivers and firmware',
            'Reinstalled operating system and software',
            'Replaced battery unit',
            'Repaired USB port connections',
            'Replaced audio drivers and speakers',
            'Optimized system performance and cleaned registry'
        ];
        
        $statuses = ['Scheduled', 'In Progress', 'Completed', 'On Hold', 'Cancelled'];
        
        // Create maintenance records for about 30% of assets
        $maintenanceCount = (int) ($assets->count() * 0.3);
        
        for ($i = 0; $i < $maintenanceCount; $i++) {
            $asset = $assets->random();
            $vendor = $vendors->random();
            $status = fake()->randomElement($statuses);
            
            $startDate = fake()->dateTimeBetween('-6 months', 'now');
            $endDate = null;
            
            if (in_array($status, ['Completed', 'Cancelled'])) {
                $endDate = fake()->dateTimeBetween($startDate, 'now');
            }
            
            Maintenance::create([
                'asset_id' => $asset->id,
                'vendor_id' => $vendor->id,
                'issue_reported' => fake()->randomElement($issues),
                'repair_action' => $status === 'Completed' ? fake()->randomElement($repairActions) : null,
                'cost' => fake()->randomFloat(2, 50, 2000),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $status,
                'remarks' => fake()->optional(0.7)->sentence(),
                'created_at' => $startDate,
                'updated_at' => $endDate ?? $startDate
            ]);
        }
        
        $this->command->info('Created ' . $maintenanceCount . ' maintenance records.');
    }
}
