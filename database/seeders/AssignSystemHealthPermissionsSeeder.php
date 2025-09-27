<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class AssignSystemHealthPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $permissions = [
            'view_system_health',
            'manage_system_health',
            'clear_system_cache',
            'view_performance_metrics'
        ];

        // Assign permissions to all users
        $users = User::all();
        
        foreach ($users as $user) {
            foreach ($permissions as $permission) {
                $user->givePermissionTo($permission);
            }
        }

        $this->command->info('System Health permissions assigned to all users.');
    }
}
