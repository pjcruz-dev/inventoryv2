<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AssetTimeline;
use App\Models\Asset;
use App\Models\User;
use App\Models\AssetAssignment;
use Carbon\Carbon;

class AssetTimelineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $assets = Asset::all();
        $assignments = AssetAssignment::all();
        
        if ($users->isEmpty() || $assets->isEmpty()) {
            $this->command->warn('No users or assets found. Please run UserSeeder and AssetSeeder first.');
            return;
        }
        
        $statusTypes = [
            'Available' => 'Asset is available for assignment',
            'Assigned' => 'Asset has been assigned to a user',
            'In Use' => 'Asset is currently being used',
            'Maintenance' => 'Asset is under maintenance',
            'Repair' => 'Asset is being repaired',
            'Retired' => 'Asset has been retired from service',
            'Disposed' => 'Asset has been disposed of',
            'Lost' => 'Asset has been reported as lost',
            'Stolen' => 'Asset has been reported as stolen'
        ];
        
        $actionTypes = [
            'Created' => 'Asset was created in the system',
            'Updated' => 'Asset information was updated',
            'Assigned' => 'Asset was assigned to a user',
            'Returned' => 'Asset was returned by user',
            'Transferred' => 'Asset was transferred between users',
            'Maintenance Started' => 'Asset maintenance began',
            'Maintenance Completed' => 'Asset maintenance was completed',
            'Repair Started' => 'Asset repair began',
            'Repair Completed' => 'Asset repair was completed',
            'Status Changed' => 'Asset status was changed',
            'Location Changed' => 'Asset location was updated',
            'Retired' => 'Asset was retired from service',
            'Disposed' => 'Asset was disposed of'
        ];
        
        $locations = [
            'Office Floor 1', 'Office Floor 2', 'Office Floor 3',
            'IT Department', 'HR Department', 'Finance Department',
            'Marketing Department', 'Sales Department',
            'Warehouse', 'Storage Room', 'Conference Room A',
            'Conference Room B', 'Reception Area', 'Executive Office'
        ];
        
        foreach ($assets as $asset) {
            $timelineEvents = [];
            $currentDate = Carbon::parse($asset->purchase_date);
            
            // Asset creation event
            AssetTimeline::create([
                'asset_id' => $asset->id,
                'action' => 'created',
                'from_user_id' => null,
                'to_user_id' => null,
                'from_department_id' => null,
                'to_department_id' => null,
                'notes' => "Asset {$asset->asset_tag} ({$asset->name}) was created and added to inventory",
                'old_values' => null,
                'new_values' => json_encode(['status' => 'Available', 'location' => 'Warehouse']),
                'performed_by' => 1,
                'performed_at' => $currentDate,
                'created_at' => $currentDate,
            ]);
            
            $currentStatus = 'Available';
            $currentLocation = 'Warehouse';
            $currentAssignedTo = null;
            
            // Generate random timeline events based on asset age
            $assetAge = Carbon::now()->diffInDays($currentDate);
            $eventCount = min(rand(3, 8), floor($assetAge / 30)); // More events for older assets
            
            for ($i = 0; $i < $eventCount; $i++) {
                $currentDate = $currentDate->copy()->addDays(rand(15, 60));
                
                if ($currentDate > Carbon::now()) break;
                
                $actionType = array_rand($actionTypes);
                $oldStatus = $currentStatus;
                $oldLocation = $currentLocation;
                $oldAssignedTo = $currentAssignedTo;
                
                switch ($actionType) {
                    case 'Assigned':
                        if ($currentStatus === 'Available') {
                            $newAssignedTo = $users->random()->id;
                            $timelineEvents[] = [
                                'asset_id' => $asset->id,
                                'action' => 'assigned',
                                'from_user_id' => null,
                                'to_user_id' => $newAssignedTo,
                                'from_department_id' => null,
                                'to_department_id' => null,
                                'notes' => "Asset assigned to " . $users->find($newAssignedTo)->first_name . " " . $users->find($newAssignedTo)->last_name,
                                'old_values' => json_encode(['status' => $oldStatus, 'location' => $oldLocation, 'assigned_to' => $oldAssignedTo]),
                                'new_values' => json_encode(['status' => 'Assigned', 'location' => $locations[array_rand($locations)], 'assigned_to' => $newAssignedTo]),
                                'performed_by' => $users->where('role_id', '<=', 3)->random()->id,
                                'performed_at' => $currentDate->copy(),
                                'created_at' => $currentDate->copy()
                            ];
                            $currentStatus = 'Assigned';
                            $currentAssignedTo = $newAssignedTo;
                            $currentLocation = $timelineEvents[count($timelineEvents) - 1]['new_location'];
                        }
                        break;
                        
                    case 'Returned':
                        if ($currentStatus === 'Assigned' && $currentAssignedTo) {
                            $timelineEvents[] = [
                                'asset_id' => $asset->id,
                                'action' => 'returned',
                                'from_user_id' => $currentAssignedTo,
                                'to_user_id' => null,
                                'from_department_id' => null,
                                'to_department_id' => null,
                                'notes' => "Asset returned by " . $users->find($currentAssignedTo)->first_name . " " . $users->find($currentAssignedTo)->last_name,
                                'old_values' => json_encode(['status' => $oldStatus, 'location' => $oldLocation, 'assigned_to' => $oldAssignedTo]),
                                'new_values' => json_encode(['status' => 'Available', 'location' => 'Storage Room', 'assigned_to' => null]),
                                'performed_by' => $currentAssignedTo,
                                'performed_at' => $currentDate->copy(),
                                'created_at' => $currentDate->copy()
                            ];
                            $currentStatus = 'Available';
                            $currentAssignedTo = null;
                            $currentLocation = 'Storage Room';
                        }
                        break;
                        
                    case 'Maintenance Started':
                        if (in_array($currentStatus, ['Available', 'Assigned'])) {
                            $timelineEvents[] = [
                                'asset_id' => $asset->id,
                                'action' => 'maintenance_started',
                                'from_user_id' => $oldAssignedTo,
                                'to_user_id' => $oldAssignedTo,
                                'from_department_id' => null,
                                'to_department_id' => null,
                                'notes' => 'Scheduled maintenance started',
                                'old_values' => json_encode(['status' => $oldStatus, 'location' => $oldLocation]),
                                'new_values' => json_encode(['status' => 'Maintenance', 'location' => 'IT Department']),
                                'performed_by' => $users->where('role_id', 5)->first()->id ?? 1,
                                'performed_at' => $currentDate->copy(),
                                'created_at' => $currentDate->copy()
                            ];
                            $currentStatus = 'Maintenance';
                            $currentLocation = 'IT Department';
                        }
                        break;
                        
                    case 'Maintenance Completed':
                        if ($currentStatus === 'Maintenance') {
                            $newStatus = $oldAssignedTo ? 'Assigned' : 'Available';
                            $timelineEvents[] = [
                                'asset_id' => $asset->id,
                                'action' => 'maintenance_completed',
                                'from_user_id' => $oldAssignedTo,
                                'to_user_id' => $oldAssignedTo,
                                'from_department_id' => null,
                                'to_department_id' => null,
                                'notes' => 'Maintenance completed successfully',
                                'old_values' => json_encode(['status' => $oldStatus, 'location' => $oldLocation]),
                                'new_values' => json_encode(['status' => $newStatus, 'location' => $oldAssignedTo ? $locations[array_rand($locations)] : 'Storage Room']),
                                'performed_by' => $users->where('role_id', 5)->first()->id ?? 1,
                                'performed_at' => $currentDate->copy()->addHours(rand(2, 8)),
                                'created_at' => $currentDate->copy()->addHours(rand(2, 8))
                            ];
                            $currentStatus = $newStatus;
                            $currentLocation = $timelineEvents[count($timelineEvents) - 1]['new_location'];
                        }
                        break;
                        
                    case 'Location Changed':
                        $newLocation = $locations[array_rand($locations)];
                        if ($newLocation !== $currentLocation) {
                            $timelineEvents[] = [
                                'asset_id' => $asset->id,
                                'action' => 'location_changed',
                                'from_user_id' => $currentAssignedTo,
                                'to_user_id' => $currentAssignedTo,
                                'from_department_id' => null,
                                'to_department_id' => null,
                                'notes' => "Asset moved from {$oldLocation} to {$newLocation}",
                                'old_values' => json_encode(['location' => $oldLocation]),
                                'new_values' => json_encode(['location' => $newLocation]),
                                'performed_by' => $users->where('role_id', '<=', 3)->random()->id,
                                'performed_at' => $currentDate->copy(),
                                'created_at' => $currentDate->copy()
                            ];
                            $currentLocation = $newLocation;
                        }
                        break;
                }
            }
            
            // Create timeline events for this asset
            foreach ($timelineEvents as $event) {
                AssetTimeline::create($event);
            }
        }
        
        // Generate timeline events for assignments
        foreach ($assignments as $assignment) {
            $asset = $assets->find($assignment->asset_id);
            $user = $users->find($assignment->user_id);
            $assignedBy = $users->find($assignment->assigned_by);
            
            if ($asset && $user && $assignedBy) {
                // Assignment timeline event
                AssetTimeline::create([
                    'asset_id' => $asset->id,
                    'action' => 'assigned',
                    'from_user_id' => null,
                    'to_user_id' => $user->id,
                    'from_department_id' => null,
                    'to_department_id' => null,
                    'notes' => "Asset {$asset->asset_tag} assigned to {$user->first_name} {$user->last_name} by {$assignedBy->first_name} {$assignedBy->last_name}",
                    'old_values' => json_encode(['status' => 'Available', 'location' => 'Storage Room', 'assigned_to' => null]),
                    'new_values' => json_encode(['status' => 'Assigned', 'location' => $locations[array_rand($locations)], 'assigned_to' => $user->id]),
                    'performed_by' => $assignment->assigned_by,
                    'performed_at' => Carbon::parse($assignment->assigned_date),
                    'created_at' => Carbon::parse($assignment->assigned_date)
                ]);
                
                // Return timeline event if applicable
                if ($assignment->return_date) {
                    AssetTimeline::create([
                        'asset_id' => $asset->id,
                        'action' => 'returned',
                        'from_user_id' => $user->id,
                        'to_user_id' => null,
                        'from_department_id' => null,
                        'to_department_id' => null,
                        'notes' => "Asset {$asset->asset_tag} returned by {$user->first_name} {$user->last_name}",
                        'old_values' => json_encode(['status' => 'Assigned', 'location' => $locations[array_rand($locations)], 'assigned_to' => $user->id]),
                        'new_values' => json_encode(['status' => 'Available', 'location' => 'Storage Room', 'assigned_to' => null]),
                        'performed_by' => $assignment->assigned_by,
                        'performed_at' => Carbon::parse($assignment->return_date),
                        'created_at' => Carbon::parse($assignment->return_date)
                    ]);
                }
            }
        }
        
        $this->command->info('Asset timeline events created successfully!');
        $this->command->info('Total timeline events: ' . AssetTimeline::count());
        $this->command->info('Assignment events: ' . AssetTimeline::where('action', 'assigned')->count());
        $this->command->info('Return events: ' . AssetTimeline::where('action', 'returned')->count());
        $this->command->info('Maintenance events: ' . AssetTimeline::whereIn('action', ['maintenance_started', 'maintenance_completed'])->count());
    }
}