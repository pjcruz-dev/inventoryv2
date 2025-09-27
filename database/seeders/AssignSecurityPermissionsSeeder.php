<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AssignSecurityPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users and assign security audit permissions
        $users = User::all();
        
        $permissions = [
            'view_security_audit',
            'export_security_audit',
            'manage_security_audit'
        ];
        
        foreach ($users as $user) {
            $user->givePermissionTo($permissions);
            $this->command->info("Permissions assigned to user: {$user->name} (ID: {$user->id})");
        }
        
        $this->command->info('Security audit permissions assigned to all users.');
    }
}
