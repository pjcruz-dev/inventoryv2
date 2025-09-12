<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AssetAssignment;
use App\Models\AssetAssignmentConfirmation;
use App\Models\Asset;
use App\Models\User;
use Carbon\Carbon;

class AssetAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users and assets
        $users = User::all();
        $assets = Asset::where('status', 'active')->get();
        
        if ($users->isEmpty() || $assets->isEmpty()) {
            $this->command->warn('No users or assets found. Please run UserSeeder and AssetSeeder first.');
            return;
        }
        
        $assignmentCounter = 1;
        $assignedAssets = [];
        
        // Assign assets to users (ensuring each asset is assigned to only one user)
        foreach ($assets as $asset) {
            // Skip if asset is already assigned
            if (in_array($asset->id, $assignedAssets)) {
                continue;
            }
            
            // Get a random user
            $user = $users->random();
            
            // Create assignment
            $assignment = AssetAssignment::firstOrCreate(
                [
                    'asset_id' => $asset->id,
                    'user_id' => $user->id
                ],
                [
                    'assigned_date' => Carbon::now()->subDays(rand(1, 90))->format('Y-m-d'),
                    'return_date' => null,
                    'notes' => 'Asset assigned for daily work use',
                    'status' => 'confirmed',
                    'assigned_by' => $users->where('role_id', 1)->first()->id ?? 1, // Assign by admin
                ]
            );
            
            // Create confirmation for the assignment (80% chance)
            if (rand(1, 100) <= 80) {
                AssetAssignmentConfirmation::firstOrCreate(
                    [
                        'asset_id' => $asset->id,
                        'user_id' => $user->id
                    ],
                    [
                        'confirmation_token' => \Illuminate\Support\Str::random(64),
                        'status' => 'pending',
                        'assigned_at' => $assignment->assigned_date
                    ]
                );
            }
            
            $assignedAssets[] = $asset->id;
            $assignmentCounter++;
        }
        
        // Create some historical assignments (returned assets)
        $historicalCount = min(50, $assets->count());
        for ($i = 1; $i <= $historicalCount; $i++) {
            $asset = $assets->random();
            $user = $users->random();
            
            $assignedDate = Carbon::now()->subDays(rand(180, 730));
            $returnedDate = $assignedDate->copy()->addDays(rand(30, 365));
            
            $assignment = AssetAssignment::create([
                'asset_id' => $asset->id,
                'user_id' => $user->id,
                'assigned_date' => $assignedDate->format('Y-m-d'),
                'return_date' => $returnedDate->format('Y-m-d'),
                'notes' => 'Historical assignment - asset returned',
                'status' => 'returned',
                'assigned_by' => $users->where('role_id', 1)->first()->id ?? 1,
            ]);
            
            // Create confirmation for historical assignment
            AssetAssignmentConfirmation::create([
                'asset_id' => $asset->id,
                'user_id' => $user->id,
                'confirmation_token' => \Illuminate\Support\Str::random(64),
                'status' => 'pending',
                'assigned_at' => $assignedDate->format('Y-m-d')
            ]);
        }
        
        $this->command->info('Asset assignments created successfully!');
        $this->command->info('Active assignments: ' . AssetAssignment::where('status', 'active')->count());
        $this->command->info('Historical assignments: ' . AssetAssignment::where('status', 'returned')->count());
        $this->command->info('Total confirmations: ' . AssetAssignmentConfirmation::count());
    }
}