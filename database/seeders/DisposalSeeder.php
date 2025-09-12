<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Disposal;
use App\Models\Asset;
use App\Models\User;
use Carbon\Carbon;

class DisposalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assets = Asset::all();
        $users = User::whereIn('role_id', [1, 2])->get(); // Admin and Manager roles
        
        if ($assets->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No assets or authorized users found. Skipping disposal seeding.');
            return;
        }
        
        $disposalTypes = [
            'End of Life',
            'Damaged Beyond Repair',
            'Obsolete Technology',
            'Security Risk',
            'Cost of Repair Exceeds Value',
            'Upgrade Replacement',
            'Theft/Loss',
            'Donation',
            'Recycling',
            'Trade-in'
        ];
        
        $remarks = [
            'Asset reached end of useful life cycle',
            'Hardware failure beyond economical repair',
            'Technology no longer supported by vendor',
            'Security vulnerabilities cannot be patched',
            'Repair costs exceed replacement value',
            'Replaced with newer model for efficiency',
            'Asset reported missing during audit',
            'Donated to local educational institution',
            'Sent to certified e-waste recycling facility',
            'Traded in for credit toward new equipment'
        ];
        
        // Create disposal records for about 10% of assets
        $disposalCount = (int) ($assets->count() * 0.1);
        
        for ($i = 0; $i < $disposalCount; $i++) {
            $asset = $assets->random();
            $approver = $users->random();
            $disposalType = fake()->randomElement($disposalTypes);
            
            $disposalDate = fake()->dateTimeBetween('-1 year', 'now');
            
            // Calculate disposal value based on type
            $disposalValue = null;
            if (in_array($disposalType, ['Trade-in', 'Donation'])) {
                $disposalValue = fake()->randomFloat(2, 10, 500);
            } elseif ($disposalType === 'Recycling') {
                $disposalValue = fake()->randomFloat(2, 5, 50);
            }
            
            Disposal::create([
                'asset_id' => $asset->id,
                'disposal_date' => $disposalDate,
                'disposal_type' => $disposalType,
                'disposal_value' => $disposalValue,
                'approved_by' => $approver->id,
                'remarks' => fake()->randomElement($remarks),
                'created_at' => $disposalDate,
                'updated_at' => $disposalDate
            ]);
            
            // Update asset status to disposed
            $asset->update([
                'status' => 'Disposed',
                'movement' => 'Disposed'
            ]);
        }
        
        $this->command->info('Created ' . $disposalCount . ' disposal records.');
    }
}
