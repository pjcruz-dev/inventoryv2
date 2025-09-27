<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AuditLog;
use App\Models\User;

class SampleAuditDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        
        if (!$user) {
            $this->command->info('No users found. Please create a user first.');
            return;
        }

        // Create sample audit data
        $auditData = [
            [
                'user_id' => $user->id,
                'action' => 'auth_login',
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'details' => ['login_method' => 'email', 'success' => true],
                'timestamp' => now()
            ],
            [
                'user_id' => $user->id,
                'action' => 'created',
                'model_type' => 'Asset',
                'model_id' => 1,
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'details' => ['asset_name' => 'Test Asset', 'asset_tag' => 'TEST-001'],
                'timestamp' => now()->subMinutes(30)
            ],
            [
                'user_id' => $user->id,
                'action' => 'failed_login_attempt',
                'ip_address' => '192.168.1.101',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'details' => ['email' => 'test@example.com', 'reason' => 'invalid_password'],
                'timestamp' => now()->subHours(2)
            ],
            [
                'user_id' => $user->id,
                'action' => 'updated',
                'model_type' => 'User',
                'model_id' => $user->id,
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'details' => ['field' => 'password', 'reason' => 'password_change'],
                'timestamp' => now()->subHours(1)
            ],
            [
                'user_id' => $user->id,
                'action' => 'file_upload',
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'details' => ['filename' => 'assets_import.csv', 'file_size' => '2048', 'file_type' => 'text/csv'],
                'timestamp' => now()->subMinutes(45)
            ],
            [
                'user_id' => $user->id,
                'action' => 'suspicious_activity',
                'ip_address' => '192.168.1.102',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'details' => ['type' => 'multiple_failed_logins', 'attempts' => 5],
                'timestamp' => now()->subMinutes(15)
            ]
        ];

        foreach ($auditData as $data) {
            AuditLog::create($data);
        }

        $this->command->info('Sample audit data created successfully!');
    }
}
