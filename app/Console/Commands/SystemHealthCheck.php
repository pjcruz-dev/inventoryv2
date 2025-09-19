<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Vendor;
use App\Models\Department;
use App\Services\CacheService;
use App\Services\ErrorHandlingService;

class SystemHealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:health-check {--fix : Attempt to fix issues found}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform comprehensive system health check and optionally fix issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Starting comprehensive system health check...');
        
        $issues = [];
        $fixes = [];
        
        // Check database connectivity
        $this->checkDatabaseConnectivity($issues, $fixes);
        
        // Check essential models
        $this->checkEssentialModels($issues, $fixes);
        
        // Check cache system
        $this->checkCacheSystem($issues, $fixes);
        
        // Check services
        $this->checkServices($issues, $fixes);
        
        // Check file permissions
        $this->checkFilePermissions($issues, $fixes);
        
        // Check configuration
        $this->checkConfiguration($issues, $fixes);
        
        // Display results
        $this->displayResults($issues, $fixes);
        
        // Apply fixes if requested
        if ($this->option('fix') && !empty($fixes)) {
            $this->applyFixes($fixes);
        }
        
        return $issues ? 1 : 0;
    }
    
    private function checkDatabaseConnectivity(&$issues, &$fixes)
    {
        $this->line('📊 Checking database connectivity...');
        
        try {
            DB::connection()->getPdo();
            $this->info('✅ Database connection: OK');
        } catch (\Exception $e) {
            $issues[] = "Database connection failed: " . $e->getMessage();
            $this->error('❌ Database connection: FAILED');
        }
        
        // Check if essential tables exist
        $tables = ['users', 'assets', 'asset_categories', 'vendors', 'departments'];
        foreach ($tables as $table) {
            try {
                DB::table($table)->count();
                $this->info("✅ Table '{$table}': OK");
            } catch (\Exception $e) {
                $issues[] = "Table '{$table}' is missing or inaccessible";
                $this->error("❌ Table '{$table}': FAILED");
                $fixes[] = "Run migration for table '{$table}': php artisan migrate";
            }
        }
    }
    
    private function checkEssentialModels(&$issues, &$fixes)
    {
        $this->line('🏗️  Checking essential models...');
        
        try {
            $userCount = User::count();
            $assetCount = Asset::count();
            $categoryCount = AssetCategory::count();
            $vendorCount = Vendor::count();
            $departmentCount = Department::count();
            
            $this->info("✅ Users: {$userCount}");
            $this->info("✅ Assets: {$assetCount}");
            $this->info("✅ Categories: {$categoryCount}");
            $this->info("✅ Vendors: {$vendorCount}");
            $this->info("✅ Departments: {$departmentCount}");
            
            // Check for admin user
            $adminExists = User::whereHas('roles', function($q) {
                $q->where('name', 'admin');
            })->exists();
            
            if (!$adminExists) {
                $issues[] = "No admin user found";
                $this->error('❌ Admin user: MISSING');
                $fixes[] = "Create admin user: php artisan db:seed";
            } else {
                $this->info('✅ Admin user: EXISTS');
            }
            
        } catch (\Exception $e) {
            $issues[] = "Model check failed: " . $e->getMessage();
            $this->error('❌ Model check: FAILED');
        }
    }
    
    private function checkCacheSystem(&$issues, &$fixes)
    {
        $this->line('💾 Checking cache system...');
        
        try {
            // Test cache write/read
            $testKey = 'health_check_test';
            $testValue = 'test_value_' . time();
            
            Cache::put($testKey, $testValue, 60);
            $retrieved = Cache::get($testKey);
            
            if ($retrieved === $testValue) {
                $this->info('✅ Cache system: OK');
                Cache::forget($testKey);
            } else {
                $issues[] = "Cache system not working properly";
                $this->error('❌ Cache system: FAILED');
                $fixes[] = "Clear cache: php artisan cache:clear";
            }
            
            // Test cache service
            $cacheService = new CacheService();
            $cacheService->cacheDashboardStats(['test' => 'value']);
            $stats = $cacheService->getDashboardStats();
            
            if ($stats && $stats['test'] === 'value') {
                $this->info('✅ Cache service: OK');
            } else {
                $issues[] = "Cache service not working properly";
                $this->error('❌ Cache service: FAILED');
            }
            
        } catch (\Exception $e) {
            $issues[] = "Cache check failed: " . $e->getMessage();
            $this->error('❌ Cache check: FAILED');
        }
    }
    
    private function checkServices(&$issues, &$fixes)
    {
        $this->line('⚙️  Checking services...');
        
        try {
            // Test error handling service
            $errorService = new ErrorHandlingService();
            $errorService->addFieldError('test', 'Test error', 'validation');
            $errors = $errorService->getFieldErrors('test');
            
            if (count($errors) > 0) {
                $this->info('✅ Error handling service: OK');
            } else {
                $issues[] = "Error handling service not working";
                $this->error('❌ Error handling service: FAILED');
            }
            
        } catch (\Exception $e) {
            $issues[] = "Service check failed: " . $e->getMessage();
            $this->error('❌ Service check: FAILED');
        }
    }
    
    private function checkFilePermissions(&$issues, &$fixes)
    {
        $this->line('📁 Checking file permissions...');
        
        $directories = [
            storage_path(),
            storage_path('app'),
            storage_path('logs'),
            storage_path('framework'),
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
        ];
        
        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                $issues[] = "Directory missing: {$dir}";
                $this->error("❌ Directory missing: {$dir}");
                $fixes[] = "Create directory: mkdir -p {$dir} && chmod 755 {$dir}";
            } elseif (!is_writable($dir)) {
                $issues[] = "Directory not writable: {$dir}";
                $this->error("❌ Directory not writable: {$dir}");
                $fixes[] = "Fix permissions: chmod 755 {$dir}";
            } else {
                $this->info("✅ Directory writable: {$dir}");
            }
        }
    }
    
    private function checkConfiguration(&$issues, &$fixes)
    {
        $this->line('⚙️  Checking configuration...');
        
        $requiredConfigs = [
            'APP_KEY' => config('app.key'),
            'DB_CONNECTION' => config('database.default'),
            'CACHE_DRIVER' => config('cache.default'),
            'SESSION_DRIVER' => config('session.driver'),
        ];
        
        foreach ($requiredConfigs as $key => $value) {
            if (empty($value)) {
                $issues[] = "Configuration missing: {$key}";
                $this->error("❌ Configuration missing: {$key}");
                $fixes[] = "Set configuration: {$key} in .env file";
            } else {
                $this->info("✅ Configuration OK: {$key}");
            }
        }
    }
    
    private function displayResults($issues, $fixes)
    {
        $this->line('');
        $this->line('📋 HEALTH CHECK RESULTS');
        $this->line('======================');
        
        if (empty($issues)) {
            $this->info('🎉 All systems are healthy! No issues found.');
        } else {
            $this->error('⚠️  Found ' . count($issues) . ' issue(s):');
            foreach ($issues as $issue) {
                $this->line("   • {$issue}");
            }
            
            if (!empty($fixes)) {
                $this->line('');
                $this->comment('🔧 Suggested fixes:');
                foreach ($fixes as $fix) {
                    $this->line("   • {$fix}");
                }
            }
        }
        
        $this->line('');
    }
    
    private function applyFixes($fixes)
    {
        $this->line('');
        $this->info('🔧 Applying fixes...');
        
        foreach ($fixes as $fix) {
            $this->line("   Executing: {$fix}");
            // Note: In a real implementation, you would execute the fix commands here
            // For safety, we're just displaying what would be done
        }
        
        $this->info('✅ Fixes applied (simulated)');
    }
}
