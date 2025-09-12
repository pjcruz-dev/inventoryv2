<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Log;
use App\Models\Asset;
use App\Models\User;
use App\Models\AssetAssignment;
use Carbon\Carbon;

class LogSeeder extends Seeder
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
        
        $eventTypes = [
            'Asset' => ['Asset Created', 'Asset Updated', 'Asset Assigned', 'Asset Returned', 'Asset Reassigned', 'Asset Maintenance', 'Asset Disposed'],
            'User' => ['User Login', 'User Logout', 'User Created', 'User Updated', 'User Deactivated'],
            'System' => ['System Backup', 'System Update', 'Database Migration', 'Security Scan'],
            'Authentication' => ['Login Success', 'Login Failed', 'Password Changed', 'Account Locked']
        ];
        
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/121.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
        ];
        
        $ipAddresses = [
            '192.168.1.10', '192.168.1.15', '192.168.1.20', '192.168.1.25', '192.168.1.30',
            '10.0.0.5', '10.0.0.10', '10.0.0.15', '172.16.0.5', '172.16.0.10'
        ];
        
        // Generate Asset-related logs
        foreach ($assets as $asset) {
            // Asset creation log
            Log::create([
                'category' => 'Asset',
                'asset_id' => $asset->id,
                'user_id' => $users->where('role_id', 1)->first()->id ?? 1,
                'role_id' => 1,
                'department_id' => 1,
                'event_type' => 'Asset Created',
                'ip_address' => $ipAddresses[array_rand($ipAddresses)],
                'user_agent' => $userAgents[array_rand($userAgents)],
                'remarks' => "Asset {$asset->asset_tag} ({$asset->name}) created in the system",
                'created_at' => Carbon::parse($asset->purchase_date)->addHours(rand(1, 24))
            ]);
            
            // Random maintenance logs (30% chance)
            if (rand(1, 100) <= 30) {
                Log::create([
                    'category' => 'Asset',
                    'asset_id' => $asset->id,
                    'user_id' => $users->where('role_id', 5)->first()->id ?? 1, // IT Support
                    'role_id' => 5,
                    'department_id' => 1,
                    'event_type' => 'Asset Maintenance',
                    'ip_address' => $ipAddresses[array_rand($ipAddresses)],
                    'user_agent' => $userAgents[array_rand($userAgents)],
                    'remarks' => "Maintenance performed on asset {$asset->asset_tag} ({$asset->name})",
                    'created_at' => Carbon::parse($asset->purchase_date)->addDays(rand(30, 365))
                ]);
            }
        }
        
        // Generate Assignment-related logs
        foreach ($assignments as $assignment) {
            $asset = $assets->find($assignment->asset_id);
            $user = $users->find($assignment->user_id);
            
            if ($asset && $user) {
                // Assignment log
                Log::create([
                    'category' => 'Asset',
                    'asset_id' => $asset->id,
                    'user_id' => $assignment->assigned_by,
                    'role_id' => $users->find($assignment->assigned_by)->role_id ?? 1,
                    'department_id' => $user->department_id,
                    'event_type' => 'Asset Assigned',
                    'ip_address' => $ipAddresses[array_rand($ipAddresses)],
                    'user_agent' => $userAgents[array_rand($userAgents)],
                    'remarks' => "Asset {$asset->asset_tag} ({$asset->name}) assigned to {$user->first_name} {$user->last_name}",
                    'created_at' => Carbon::parse($assignment->assigned_date)->addHours(rand(1, 6))
                ]);
                
                // Return log if asset was returned
                if ($assignment->return_date) {
                    Log::create([
                        'category' => 'Asset',
                        'asset_id' => $asset->id,
                        'user_id' => $assignment->assigned_by,
                        'role_id' => $users->find($assignment->assigned_by)->role_id ?? 1,
                        'department_id' => $user->department_id,
                        'event_type' => 'Asset Returned',
                        'ip_address' => $ipAddresses[array_rand($ipAddresses)],
                        'user_agent' => $userAgents[array_rand($userAgents)],
                        'remarks' => "Asset {$asset->asset_tag} ({$asset->name}) returned by {$user->first_name} {$user->last_name}",
                        'created_at' => Carbon::parse($assignment->return_date)->addHours(rand(1, 6))
                    ]);
                }
            }
        }
        
        // Generate User activity logs
        foreach ($users as $user) {
            // User creation log
            Log::create([
                'category' => 'User',
                'user_id' => 1, // Created by admin
                'role_id' => 1,
                'department_id' => $user->department_id,
                'event_type' => 'User Created',
                'ip_address' => $ipAddresses[array_rand($ipAddresses)],
                'user_agent' => $userAgents[array_rand($userAgents)],
                'remarks' => "User account created for {$user->first_name} {$user->last_name} ({$user->employee_no})",
                'created_at' => Carbon::now()->subDays(rand(1, 90))
            ]);
            
            // Random login logs (generate 1-5 login logs per user)
            $loginCount = rand(1, 5);
            for ($i = 0; $i < $loginCount; $i++) {
                Log::create([
                    'category' => 'Authentication',
                    'user_id' => $user->id,
                    'role_id' => $user->role_id,
                    'department_id' => $user->department_id,
                    'event_type' => 'Login Success',
                    'ip_address' => $ipAddresses[array_rand($ipAddresses)],
                    'user_agent' => $userAgents[array_rand($userAgents)],
                    'remarks' => "User {$user->first_name} {$user->last_name} logged in successfully",
                    'created_at' => Carbon::now()->subDays(rand(1, 30))->addHours(rand(8, 18))
                ]);
            }
        }
        
        // Generate System logs
        $systemLogs = [
            ['event' => 'System Backup', 'remark' => 'Daily system backup completed successfully'],
            ['event' => 'Database Migration', 'remark' => 'Database migration executed successfully'],
            ['event' => 'Security Scan', 'remark' => 'Security vulnerability scan completed'],
            ['event' => 'System Update', 'remark' => 'System maintenance and updates applied']
        ];
        
        foreach ($systemLogs as $sysLog) {
            for ($i = 0; $i < rand(3, 8); $i++) {
                Log::create([
                    'category' => 'System',
                    'user_id' => 1, // System admin
                    'role_id' => 1,
                    'department_id' => 1,
                    'event_type' => $sysLog['event'],
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'System/1.0',
                    'remarks' => $sysLog['remark'],
                    'created_at' => Carbon::now()->subDays(rand(1, 60))
                ]);
            }
        }
        
        $this->command->info('Activity logs created successfully!');
        $this->command->info('Total logs: ' . Log::count());
        $this->command->info('Asset logs: ' . Log::where('category', 'Asset')->count());
        $this->command->info('User logs: ' . Log::where('category', 'User')->count());
        $this->command->info('Authentication logs: ' . Log::where('category', 'Authentication')->count());
        $this->command->info('System logs: ' . Log::where('category', 'System')->count());
    }
}
