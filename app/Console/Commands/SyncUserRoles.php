<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class SyncUserRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:sync-roles {--force : Force sync even if user already has roles}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync users with their Spatie roles based on role_id';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting user role synchronization...');
        
        $force = $this->option('force');
        $synced = 0;
        $skipped = 0;
        $errors = 0;
        
        $users = User::with('roles')->get();
        $roles = Role::all()->keyBy('id');
        
        foreach ($users as $user) {
            try {
                // Skip if user already has roles and not forcing
                if (!$force && $user->roles->count() > 0) {
                    $skipped++;
                    continue;
                }
                
                // Get the role name from role_id
                if (!$user->role_id || !isset($roles[$user->role_id])) {
                    $this->warn("User {$user->id} ({$user->first_name} {$user->last_name}) has invalid role_id: {$user->role_id}");
                    $errors++;
                    continue;
                }
                
                $roleName = $roles[$user->role_id]->name;
                
                // Assign the role using Spatie
                $user->syncRoles([$roleName]);
                
                $this->line("✓ Synced {$user->first_name} {$user->last_name} with role: {$roleName}");
                $synced++;
                
            } catch (\Exception $e) {
                $this->error("Failed to sync user {$user->id}: {$e->getMessage()}");
                $errors++;
            }
        }
        
        $this->info("\nSynchronization completed!");
        $this->info("Synced: {$synced} users");
        $this->info("Skipped: {$skipped} users");
        $this->info("Errors: {$errors} users");
        
        // Clear permission cache
        $this->call('permission:cache-reset');
        
        return Command::SUCCESS;
    }
}