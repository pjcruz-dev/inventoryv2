<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ImportExportController;
use App\Services\TemplateGenerationService;
use App\Models\User;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Vendor;
use App\Models\Department;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class TestImportExport extends Command
{
    protected $signature = 'test:import-export {--module=assets}';
    protected $description = 'Test import/export functionality for all modules';

    public function handle()
    {
        $this->info('Starting Import/Export Functionality Tests...');
        
        // Create a test user for authentication context
        $testUser = User::first();
        if (!$testUser) {
            $this->error('No users found in database. Please create a user first.');
            return 1;
        }
        
        Auth::login($testUser);
        $this->info('Authenticated as: ' . $testUser->email);
        
        $module = $this->option('module');
        $modules = $module === 'all' ? ['assets', 'users', 'computers', 'departments', 'vendors'] : [$module];
        
        foreach ($modules as $testModule) {
            $this->info("\n=== Testing Module: {$testModule} ===");
            
            // Test 1: Template Generation
            $this->testTemplateGeneration($testModule);
            
            // Test 2: Template Download
            $this->testTemplateDownload($testModule);
            
            // Test 3: Export Functionality
            $this->testExportFunctionality($testModule);
            
            // Test 4: Import Validation
            $this->testImportValidation($testModule);
        }
        
        $this->info('\n=== All Tests Completed ===' );
        return 0;
    }
    
    private function testTemplateGeneration($module)
    {
        $this->info("Testing template generation for {$module}...");
        
        try {
            $templateService = new TemplateGenerationService();
            $template = $templateService->generateTemplate($module);
            
            if (isset($template['headers']) && isset($template['sample_data'])) {
                $this->info("✓ Template generated successfully");
                $this->info("  - Headers: " . count($template['headers']) . " columns");
                $this->info("  - Sample data: " . count($template['sample_data']) . " rows");
                
                // Extract header names safely
                $headerNames = [];
                foreach ($template['headers'] as $key => $header) {
                    if (is_array($header) && isset($header['name'])) {
                        $headerNames[] = $header['name'];
                    } else {
                        $headerNames[] = $key;
                    }
                }
                $this->info("  - Headers: " . implode(', ', array_slice($headerNames, 0, 5)) . (count($headerNames) > 5 ? '...' : ''));
            } else {
                $this->error("✗ Template generation failed - missing required fields");
            }
        } catch (\Exception $e) {
            $this->error("✗ Template generation failed: " . $e->getMessage());
        }
    }
    
    private function testTemplateDownload($module)
    {
        $this->info("Testing template download for {$module}...");
        
        try {
            $controller = new ImportExportController(new TemplateGenerationService());
            $response = $controller->downloadTemplate($module);
            
            if ($response->getStatusCode() === 200) {
                $this->info("✓ Template download successful");
                $this->info("  - Content-Type: " . $response->headers->get('Content-Type'));
                $this->info("  - Content-Disposition: " . $response->headers->get('Content-Disposition'));
                
                // Check content length
                $content = $response->getContent();
                $this->info("  - Content size: " . strlen($content) . " bytes");
                
                // Verify it's valid CSV
                $lines = explode("\n", $content);
                $this->info("  - CSV lines: " . count($lines));
                
                if (count($lines) > 1) {
                    $headers = str_getcsv($lines[0]);
                    $this->info("  - CSV headers: " . count($headers) . " columns");
                }
            } else {
                $this->error("✗ Template download failed with status: " . $response->getStatusCode());
            }
        } catch (\Exception $e) {
            $this->error("✗ Template download failed: " . $e->getMessage());
        }
    }
    
    private function testExportFunctionality($module)
    {
        $this->info("Testing export functionality for {$module}...");
        
        try {
            $controller = new ImportExportController(new TemplateGenerationService());
            $response = $controller->export($module);
            
            if ($response->getStatusCode() === 200) {
                $this->info("✓ Export successful");
                $this->info("  - Content-Type: " . $response->headers->get('Content-Type'));
                
                $content = $response->getContent();
                $this->info("  - Export size: " . strlen($content) . " bytes");
                
                // Count exported records
                $lines = explode("\n", trim($content));
                $recordCount = count($lines) - 1; // Subtract header row
                $this->info("  - Records exported: " . max(0, $recordCount));
            } else {
                $this->error("✗ Export failed with status: " . $response->getStatusCode());
            }
        } catch (\Exception $e) {
            $this->error("✗ Export failed: " . $e->getMessage());
        }
    }
    
    private function testImportValidation($module)
    {
        $this->info("Testing import validation for {$module}...");
        
        try {
            // Create a test CSV file
            $testCsvPath = $this->createTestCsvFile($module);
            
            if (!$testCsvPath) {
                $this->error("✗ Could not create test CSV file");
                return;
            }
            
            // Create a mock uploaded file
            $uploadedFile = new UploadedFile(
                $testCsvPath,
                'test_' . $module . '.csv',
                'text/csv',
                null,
                true
            );
            
            // Create a mock request
            $request = new Request();
            $request->files->set('csv_file', $uploadedFile);
            $request->merge(['validate_only' => true]);
            
            $controller = new ImportExportController(new TemplateGenerationService());
            $response = $controller->validateImport($request, $module);
            
            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getContent(), true);
                $this->info("✓ Import validation completed");
                $this->info("  - Success: " . ($data['success'] ? 'Yes' : 'No'));
                $this->info("  - Total rows: " . ($data['summary']['total'] ?? 0));
                $this->info("  - Errors: " . ($data['summary']['errors'] ?? 0));
                $this->info("  - Warnings: " . ($data['summary']['warnings'] ?? 0));
            } else {
                $this->error("✗ Import validation failed with status: " . $response->getStatusCode());
            }
            
            // Clean up test file
            if (file_exists($testCsvPath)) {
                unlink($testCsvPath);
            }
            
        } catch (\Exception $e) {
            $this->error("✗ Import validation failed: " . $e->getMessage());
        }
    }
    
    private function createTestCsvFile($module)
    {
        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        
        $filePath = $tempDir . '/test_' . $module . '_' . time() . '.csv';
        
        // Generate test data based on module
        $testData = $this->getTestData($module);
        
        if (!$testData) {
            return null;
        }
        
        $csvContent = implode("\n", $testData);
        
        if (file_put_contents($filePath, $csvContent) === false) {
            return null;
        }
        
        return $filePath;
    }
    
    private function getTestData($module)
    {
        $testData = [
            'assets' => [
                'asset_tag,category_name,vendor_name,name,description,serial_number,purchase_date,warranty_end,cost,status',
                'TEST001,Computer Hardware,Dell Inc,Test Computer,Test Description,SN123456,2024-01-15,2027-01-15,50000.00,Available'
            ],
            'users' => [
                'employee_id,first_name,last_name,email,department_name,position,role,status',
                'EMP001,John,Doe,john.doe@test.com,IT Department,Developer,Admin,Active'
            ],
            'computers' => [
                'asset_tag,category_name,vendor_name,name,description,serial_number,purchase_date,warranty_end,cost,status,processor,ram,storage,os',
                'COMP001,Computer Hardware,Dell Inc,Test Computer,Test Description,SN123456,2024-01-15,2027-01-15,50000.00,Available,Intel i7,16GB,512GB SSD,Windows 11'
            ],
            'departments' => [
                'name,description,contact_email',
                'Test Department,Test Description,test@company.com'
            ],
            'vendors' => [
                'name,contact_person,email,phone,address',
                'Test Vendor,John Smith,vendor@test.com,123-456-7890,123 Test St'
            ]
        ];
        
        return $testData[$module] ?? null;
    }
}