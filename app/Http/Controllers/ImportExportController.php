<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Asset;
use App\Models\Computer;
use App\Models\Monitor;
use App\Models\Printer;
use App\Models\Peripheral;
use App\Models\Department;
use App\Models\Vendor;
use App\Models\AssetCategory;
use App\Models\Role;
use App\Services\TemplateGenerationService;
use Exception;

class ImportExportController extends Controller
{
    protected $templateService;

    public function __construct(TemplateGenerationService $templateService)
    {
        $this->templateService = $templateService;
    }

    /**
     * Download comprehensive CSV template for specified module
     */
    public function downloadTemplate($module)
    {
        $validModules = ['users', 'assets', 'computers', 'monitors', 'printers', 'peripherals', 'departments', 'vendors'];
        
        if (!in_array($module, $validModules)) {
            return response()->json(['error' => 'Invalid module specified'], 400);
        }

        try {
            // Generate comprehensive template data
            $templateData = $this->templateService->generateTemplate($module);
            
            // Create CSV content with enhanced headers and validation info
            $csvContent = $this->generateEnhancedCsvContent($templateData);
            
            $filename = $module . '_template_' . date('Y-m-d_H-i-s') . '.csv';
            
            // Log template download
            Log::info('Template downloaded', [
                'module' => $module,
                'user_id' => auth()->id(),
                'filename' => $filename,
                'timestamp' => now()
            ]);
            
            return Response::make($csvContent, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'X-Template-Version' => '2.0',
                'X-Generated-At' => now()->toISOString()
            ]);
        } catch (Exception $e) {
            Log::error('Template generation failed', [
                'module' => $module,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return response()->json(['error' => 'Failed to generate template'], 500);
        }
    }

    /**
     * Generate enhanced CSV content with validation info
     */
    private function generateEnhancedCsvContent($templateData)
    {
        $output = fopen('php://temp', 'r+');
        
        // Add validation info as comments
        fwrite($output, "# Template Version: 2.0\n");
        fwrite($output, "# Generated: " . now()->toISOString() . "\n");
        fwrite($output, "# Required fields are marked with *\n");
        fwrite($output, "# \n");
        
        // Extract header names from the template headers structure
        $headerNames = [];
        foreach ($templateData['headers'] as $key => $headerInfo) {
            $headerNames[] = $headerInfo['name'] ?? $key;
        }
        
        // Add headers
        fputcsv($output, $headerNames);
        
        // Add sample data rows
        foreach ($templateData['sample_data'] as $row) {
            // Convert associative array to indexed array in correct order
            $rowData = [];
            foreach (array_keys($templateData['headers']) as $key) {
                $rowData[] = $row[$key] ?? '';
            }
            fputcsv($output, $rowData);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }

    /**
     * Get sample data for CSV template (legacy method)
     */
    private function getSampleData($module)
    {
        $samples = [
            'users' => 'EMP001,12345,John,Doe,john.doe@company.com,IT Department,Software Developer,Admin,Active',
            'assets' => 'AST001,Computer Hardware,Dell Inc,Dell OptiPlex 7090,Desktop Computer,ABC123456,2024-01-15,2027-01-15,50000.00,Available',
            'computers' => 'AST001,Computer Hardware,Dell Inc,Dell OptiPlex 7090,Desktop Computer,ABC123456,2024-01-15,2027-01-15,50000.00,Available,Intel Core i7-11700,16GB DDR4,512GB SSD,Windows 11 Pro',
            'monitors' => 'MON001,Monitors & Displays,Samsung,Samsung 24" Monitor,24-inch LED Monitor,MON123456,2024-01-15,2027-01-15,15000.00,Available,24",1920x1080,IPS',
            'printers' => 'PRT001,Printers & Scanners,HP,HP LaserJet Pro,Laser Printer,PRT123456,2024-01-15,2027-01-15,25000.00,Available,Laser,1,1',
            'peripherals' => 'PER001,Peripherals,Logitech,Wireless Mouse,Optical Mouse,PER123456,2024-01-15,2027-01-15,2000.00,Available,Mouse,Wireless',
            'departments' => 'Information Technology,IT Department managing all technology assets,admin@company.com',
            'vendors' => 'Dell Technologies,John Smith,sales@dell.com,+1-800-DELL,Round Rock TX USA'
        ];

        return $samples[$module] ?? '';
    }

    /**
     * Import data from CSV file with comprehensive validation
     */
    public function import(Request $request, $module)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // Increased to 10MB
            'validate_only' => 'boolean' // Option to validate without importing
        ]);

        $file = $request->file('csv_file');
        $validateOnly = $request->boolean('validate_only', false);
        $path = $file->getRealPath();
        
        // Read CSV file
        $csvData = file($path);
        if (empty($csvData)) {
            return redirect()->route('import-export.results')
                ->with('import_errors', [[
                    'row' => 1,
                    'field' => 'file',
                    'message' => 'CSV file is empty or could not be read.',
                    'value' => ''
                ]]);
        }

        $data = array_map('str_getcsv', $csvData);
        $headers = array_shift($data);
        
        // Clean headers (remove BOM and trim)
        $headers = array_map(function($header) {
            return trim(str_replace("\xEF\xBB\xBF", '', $header));
        }, $headers);
        
        // Validate header
        $requiredFields = $this->getRequiredFields($module);
        $missingFields = array_diff($requiredFields, $headers);
        
        if (!empty($missingFields)) {
            return redirect()->route('import-export.results')
                ->with('import_errors', [[
                    'row' => 1,
                    'field' => 'header',
                    'message' => 'Missing required columns: ' . implode(', ', $missingFields),
                    'value' => implode(', ', $headers)
                ]]);
        }

        $errors = [];
        $warnings = [];
        $duplicates = [];
        $successCount = 0;
        $totalRows = count($data);

        // Pre-validation phase - check for duplicates within file
        $serialNumbers = [];
        $assetTags = [];
        $emails = [];
        
        foreach ($data as $index => $row) {
            $rowNumber = $index + 2;
            $rowData = array_combine($headers, array_pad($row, count($headers), ''));
            
            // Check for duplicate serial numbers within file
            if (!empty($rowData['serial_number'])) {
                if (in_array($rowData['serial_number'], $serialNumbers)) {
                    $duplicates[] = [
                        'row' => $rowNumber,
                        'field' => 'serial_number',
                        'message' => "Duplicate serial number '{$rowData['serial_number']}' found in file",
                        'value' => $rowData['serial_number']
                    ];
                } else {
                    $serialNumbers[] = $rowData['serial_number'];
                }
            }
            
            // Check for duplicate asset tags within file
            if (!empty($rowData['asset_tag'])) {
                if (in_array($rowData['asset_tag'], $assetTags)) {
                    $duplicates[] = [
                        'row' => $rowNumber,
                        'field' => 'asset_tag',
                        'message' => "Duplicate asset tag '{$rowData['asset_tag']}' found in file",
                        'value' => $rowData['asset_tag']
                    ];
                } else {
                    $assetTags[] = $rowData['asset_tag'];
                }
            }
            
            // Check for duplicate emails within file
            if (!empty($rowData['email'])) {
                if (in_array($rowData['email'], $emails)) {
                    $duplicates[] = [
                        'row' => $rowNumber,
                        'field' => 'email',
                        'message' => "Duplicate email '{$rowData['email']}' found in file",
                        'value' => $rowData['email']
                    ];
                } else {
                    $emails[] = $rowData['email'];
                }
            }
        }

        // If validation only, perform validation without importing
        if ($validateOnly) {
            foreach ($data as $index => $row) {
                $rowNumber = $index + 2;
                $rowData = array_combine($headers, array_pad($row, count($headers), ''));
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    $warnings[] = [
                        'row' => $rowNumber,
                        'message' => 'Empty row will be skipped',
                        'field' => 'general'
                    ];
                    continue;
                }
                
                try {
                    $this->validateImportRow($module, $rowData, $rowNumber);
                } catch (\Illuminate\Validation\ValidationException $e) {
                    foreach ($e->errors() as $field => $messages) {
                        foreach ($messages as $message) {
                            $errors[] = [
                                'row' => $rowNumber,
                                'field' => $field,
                                'message' => $message,
                                'value' => $rowData[$field] ?? ''
                            ];
                        }
                    }
                } catch (\Exception $e) {
                    $errors[] = [
                        'row' => $rowNumber,
                        'field' => 'general',
                        'message' => $e->getMessage(),
                        'value' => ''
                    ];
                }
            }
            
            $allErrors = array_merge($errors, $duplicates);
            $summary = [
                'total' => $totalRows,
                'validation_only' => true,
                'errors' => count($allErrors),
                'warnings' => count($warnings),
                'duplicates' => count($duplicates)
            ];
            
            return redirect()->route('import-export.results')
                ->with('import_errors', $allErrors)
                ->with('import_warnings', $warnings)
                ->with('import_summary', $summary)
                ->with('validation_message', 'Validation completed. ' . (empty($allErrors) ? 'No errors found.' : count($allErrors) . ' errors found.'));
        }

        // Check for duplicates before proceeding with import
        if (!empty($duplicates)) {
            return redirect()->route('import-export.results')
                ->with('import_errors', $duplicates)
                ->with('import_summary', [
                    'total' => $totalRows,
                    'successful' => 0,
                    'failed' => count($duplicates),
                    'warnings' => 0
                ]);
        }

        DB::beginTransaction();
        
        // Log import start
        Log::info('Import started', [
            'module' => $module,
            'user_id' => auth()->id(),
            'total_rows' => $totalRows,
            'filename' => $file->getClientOriginalName()
        ]);

        try {
            foreach ($data as $index => $row) {
                $rowNumber = $index + 2; // +2 because we removed headers and arrays are 0-indexed
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    $warnings[] = [
                        'row' => $rowNumber,
                        'message' => 'Empty row skipped',
                        'field' => 'general'
                    ];
                    continue;
                }
                
                $rowData = array_combine($headers, array_pad($row, count($headers), ''));
                
                try {
                    $this->processImportRow($module, $rowData, $rowNumber);
                    $successCount++;
                } catch (\Illuminate\Validation\ValidationException $e) {
                    foreach ($e->errors() as $field => $messages) {
                        foreach ($messages as $message) {
                            $errors[] = [
                                'row' => $rowNumber,
                                'field' => $field,
                                'message' => $message,
                                'value' => $rowData[$field] ?? ''
                            ];
                        }
                    }
                } catch (\Exception $e) {
                    $errors[] = [
                        'row' => $rowNumber,
                        'field' => 'general',
                        'message' => $e->getMessage(),
                        'value' => ''
                    ];
                }
            }

            if (empty($errors)) {
                DB::commit();
                
                // Log successful import
                Log::info('Import completed successfully', [
                    'module' => $module,
                    'user_id' => auth()->id(),
                    'imported_records' => $successCount,
                    'warnings' => count($warnings)
                ]);
            } else {
                DB::rollback();
                
                // Log failed import
                Log::error('Import failed', [
                    'module' => $module,
                    'user_id' => auth()->id(),
                    'errors' => count($errors),
                    'processed' => $successCount
                ]);
            }

            // Prepare session data
            $summary = [
                'total' => $totalRows,
                'successful' => $successCount,
                'failed' => count($errors),
                'warnings' => count($warnings)
            ];

            if (count($errors) > 0) {
                return redirect()->route('import-export.results')
                    ->with('import_errors', $errors)
                    ->with('import_warnings', $warnings)
                    ->with('import_summary', $summary);
            }

            $successMessage = "Successfully imported {$successCount} {$module}.";
            if (count($warnings) > 0) {
                $successMessage .= " {$summary['warnings']} warnings generated.";
            }

            return redirect()->route('import-export.results')
                ->with('import_success', $successMessage)
                ->with('import_warnings', $warnings)
                ->with('import_summary', $summary);

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Import exception', [
                'module' => $module,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('import-export.results')
                ->with('import_errors', [[
                    'row' => 0,
                    'field' => 'general',
                    'message' => 'Import failed: ' . $e->getMessage(),
                    'value' => ''
                ]]);
        }
    }

    /**
     * Get required fields for module validation
     */
    private function getRequiredFields($module)
    {
        $requiredFields = [
            'users' => ['employee_no', 'first_name', 'last_name', 'email', 'department_name'],
            'assets' => ['asset_tag', 'category_name', 'vendor_name', 'name'],
            'computers' => ['asset_tag', 'category_name', 'vendor_name', 'name', 'processor', 'ram', 'storage', 'os'],
            'monitors' => ['asset_tag', 'category_name', 'vendor_name', 'name', 'size', 'resolution'],
            'printers' => ['asset_tag', 'category_name', 'vendor_name', 'name', 'type'],
            'peripherals' => ['asset_tag', 'category_name', 'vendor_name', 'name', 'type', 'interface'],
            'departments' => ['name'],
            'vendors' => ['name']
        ];

        return $requiredFields[$module] ?? [];
    }

    /**
     * Validate individual import row based on module
     */
    private function validateImportRow($module, $data, $rowNumber)
    {
        switch ($module) {
            case 'users':
                $this->validateUser($data, $rowNumber);
                break;
            case 'assets':
                $this->validateAsset($data, $rowNumber);
                break;
            case 'computers':
                $this->validateComputer($data, $rowNumber);
                break;
            case 'monitors':
                $this->validateMonitor($data, $rowNumber);
                break;
            case 'printers':
                $this->validatePrinter($data, $rowNumber);
                break;
            case 'peripherals':
                $this->validatePeripheral($data, $rowNumber);
                break;
            case 'departments':
                $this->validateDepartment($data, $rowNumber);
                break;
            case 'vendors':
                $this->validateVendor($data, $rowNumber);
                break;
            default:
                throw new \Exception('Invalid module specified.');
        }
    }

    /**
     * Process individual import row based on module
     */
    private function processImportRow($module, $data, $rowNumber)
    {
        switch ($module) {
            case 'users':
                $this->importUser($data, $rowNumber);
                break;
            case 'assets':
                $this->importAsset($data, $rowNumber);
                break;
            case 'computers':
                $this->importComputer($data, $rowNumber);
                break;
            case 'monitors':
                $this->importMonitor($data, $rowNumber);
                break;
            case 'printers':
                $this->importPrinter($data, $rowNumber);
                break;
            case 'peripherals':
                $this->importPeripheral($data, $rowNumber);
                break;
            case 'departments':
                $this->importDepartment($data, $rowNumber);
                break;
            case 'vendors':
                $this->importVendor($data, $rowNumber);
                break;
            default:
                throw new \Exception('Invalid module specified.');
        }
    }

    /**
     * Import user data
     */
    private function importUser($data, $rowNumber)
    {
        // Validate required fields
        $validator = Validator::make($data, [
            'employee_no' => 'required|unique:users,employee_no',
            'employee_id' => 'nullable|unique:users,employee_id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'department_name' => 'required|string'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Find department by name
        $department = Department::where('name', $data['department_name'])->first();
        if (!$department) {
            throw new \Exception("Department '{$data['department_name']}' not found.");
        }

        // Find role by name (optional)
        $role = null;
        if (!empty($data['role_name'])) {
            $role = Role::where('name', $data['role_name'])->first();
            if (!$role) {
                throw new \Exception("Role '{$data['role_name']}' not found.");
            }
        }

        User::create([
            'employee_no' => $data['employee_no'],
            'employee_id' => $data['employee_id'] ?? null,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'department_id' => $department->id,
            'position' => $data['position'] ?? null,
            'role_id' => $role ? $role->id : null,
            'status' => $data['status'] ?? 'Active',
            'password' => bcrypt('password123') // Default password
        ]);
    }

    /**
     * Import asset data
     */
    private function importAsset($data, $rowNumber)
    {
        // Validate required fields
        $validator = Validator::make($data, [
            'asset_tag' => 'required|unique:assets,asset_tag',
            'category_name' => 'required|string',
            'vendor_name' => 'required|string',
            'name' => 'required|string|max:255',
            'purchase_date' => 'nullable|date',
            'warranty_end' => 'nullable|date',
            'cost' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Find category by name
        $category = AssetCategory::where('name', $data['category_name'])->first();
        if (!$category) {
            throw new \Exception("Category '{$data['category_name']}' not found.");
        }

        // Find vendor by name
        $vendor = Vendor::where('name', $data['vendor_name'])->first();
        if (!$vendor) {
            throw new \Exception("Vendor '{$data['vendor_name']}' not found.");
        }

        Asset::create([
            'asset_tag' => $data['asset_tag'],
            'category_id' => $category->id,
            'vendor_id' => $vendor->id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'serial_number' => $data['serial_number'] ?? null,
            'purchase_date' => $data['purchase_date'] ? date('Y-m-d', strtotime($data['purchase_date'])) : null,
            'warranty_end' => $data['warranty_end'] ? date('Y-m-d', strtotime($data['warranty_end'])) : null,
            'cost' => $data['cost'] ?? 0,
            'status' => $data['status'] ?? 'Active',
            'movement' => 'New Arrival'
        ]);
    }

    /**
     * Import computer data
     */
    private function importComputer($data, $rowNumber)
    {
        // First create the asset
        $asset = $this->createAssetFromData($data, $rowNumber);

        // Validate computer-specific fields
        $validator = Validator::make($data, [
            'processor' => 'required|string|max:255',
            'ram' => 'required|string|max:255',
            'storage' => 'required|string|max:255',
            'os' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Then create the computer record
        Computer::create([
            'asset_id' => $asset->id,
            'processor' => $data['processor'],
            'ram' => $data['ram'],
            'storage' => $data['storage'],
            'os' => $data['os']
        ]);
    }

    /**
     * Import monitor data
     */
    private function importMonitor($data, $rowNumber)
    {
        // First create the asset
        $asset = $this->createAssetFromData($data, $rowNumber);

        // Validate monitor-specific fields
        $validator = Validator::make($data, [
            'size' => 'required|string|max:255',
            'resolution' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Then create the monitor record
        Monitor::create([
            'asset_id' => $asset->id,
            'size' => $data['size'],
            'resolution' => $data['resolution'],
            'panel_type' => $data['panel_type'] ?? null
        ]);
    }

    /**
     * Import printer data
     */
    private function importPrinter($data, $rowNumber)
    {
        // First create the asset
        $asset = $this->createAssetFromData($data, $rowNumber);

        // Validate printer-specific fields
        $validator = Validator::make($data, [
            'type' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Then create the printer record
        Printer::create([
            'asset_id' => $asset->id,
            'type' => $data['type'],
            'color_support' => filter_var($data['color_support'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'duplex' => filter_var($data['duplex'] ?? false, FILTER_VALIDATE_BOOLEAN)
        ]);
    }

    /**
     * Import peripheral data
     */
    private function importPeripheral($data, $rowNumber)
    {
        // First create the asset
        $asset = $this->createAssetFromData($data, $rowNumber);

        // Validate peripheral-specific fields
        $validator = Validator::make($data, [
            'type' => 'required|string|max:255',
            'interface' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Then create the peripheral record
        Peripheral::create([
            'asset_id' => $asset->id,
            'type' => $data['type'],
            'interface' => $data['interface']
        ]);
    }

    /**
     * Import department data
     */
    private function importDepartment($data, $rowNumber)
    {
        // Validate required fields
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:departments,name'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        $manager = null;
        if (!empty($data['manager_email'])) {
            $manager = User::where('email', $data['manager_email'])->first();
            if (!$manager) {
                throw new \Exception("Manager with email '{$data['manager_email']}' not found.");
            }
        }

        Department::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'manager_id' => $manager ? $manager->id : null
        ]);
    }

    /**
     * Import vendor data
     */
    private function importVendor($data, $rowNumber)
    {
        // Validate required fields
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:vendors,name',
            'email' => 'nullable|email'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        Vendor::create([
            'name' => $data['name'],
            'contact_person' => $data['contact_person'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null
        ]);
    }

    /**
     * Helper method to create asset from data
     */
    private function createAssetFromData($data, $rowNumber)
    {
        // Validate required fields
        $validator = Validator::make($data, [
            'asset_tag' => 'required|unique:assets,asset_tag',
            'category_name' => 'required|string',
            'vendor_name' => 'required|string',
            'name' => 'required|string|max:255',
            'purchase_date' => 'nullable|date',
            'warranty_end' => 'nullable|date',
            'cost' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Find category by name
        $category = AssetCategory::where('name', $data['category_name'])->first();
        if (!$category) {
            throw new \Exception("Category '{$data['category_name']}' not found.");
        }

        // Find vendor by name
        $vendor = Vendor::where('name', $data['vendor_name'])->first();
        if (!$vendor) {
            throw new \Exception("Vendor '{$data['vendor_name']}' not found.");
        }

        return Asset::create([
            'asset_tag' => $data['asset_tag'],
            'category_id' => $category->id,
            'vendor_id' => $vendor->id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'serial_number' => $data['serial_number'] ?? null,
            'purchase_date' => $data['purchase_date'] ? date('Y-m-d', strtotime($data['purchase_date'])) : null,
            'warranty_end' => $data['warranty_end'] ? date('Y-m-d', strtotime($data['warranty_end'])) : null,
            'cost' => $data['cost'] ?? 0,
            'status' => $data['status'] ?? 'Active',
            'movement' => 'New Arrival'
        ]);
    }

    /**
     * Export data to CSV
     */
    public function export($module)
    {
        $data = $this->getExportData($module);
        $filename = $module . '_export_' . date('Y-m-d_H-i-s') . '.csv';

        $csvContent = $this->arrayToCsv($data);

        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Get export data based on module
     */
    private function getExportData($module)
    {
        switch ($module) {
            case 'users':
                return User::with(['department', 'role'])
                    ->get()
                    ->map(function ($user) {
                        return [
                            'employee_no' => $user->employee_no,
                            'employee_id' => $user->employee_id,
                            'first_name' => $user->first_name,
                            'last_name' => $user->last_name,
                            'email' => $user->email,
                            'department_name' => $user->department->name ?? '',
                            'position' => $user->position,
                            'role_name' => $user->role->name ?? '',
                            'status' => $user->status
                        ];
                    })->toArray();

            case 'assets':
                return Asset::with(['category', 'vendor'])
                    ->get()
                    ->map(function ($asset) {
                        return [
                            'asset_tag' => $asset->asset_tag,
                            'category_name' => $asset->category->name ?? '',
                            'vendor_name' => $asset->vendor->name ?? '',
                            'name' => $asset->name,
                            'description' => $asset->description,
                            'serial_number' => $asset->serial_number,
                            'purchase_date' => $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : '',
                            'warranty_end' => $asset->warranty_end ? $asset->warranty_end->format('Y-m-d') : '',
                            'cost' => $asset->cost,
                            'status' => $asset->status
                        ];
                    })->toArray();

            case 'computers':
                return Computer::with(['asset.category', 'asset.vendor'])
                    ->get()
                    ->map(function ($computer) {
                        return [
                            'asset_tag' => $computer->asset->asset_tag,
                            'category_name' => $computer->asset->category->name ?? '',
                            'vendor_name' => $computer->asset->vendor->name ?? '',
                            'name' => $computer->asset->name,
                            'description' => $computer->asset->description,
                            'serial_number' => $computer->asset->serial_number,
                            'purchase_date' => $computer->asset->purchase_date ? $computer->asset->purchase_date->format('Y-m-d') : '',
                            'warranty_end' => $computer->asset->warranty_end ? $computer->asset->warranty_end->format('Y-m-d') : '',
                            'cost' => $computer->asset->cost,
                            'status' => $computer->asset->status,
                            'movement' => $computer->asset->movement,
                            'processor' => $computer->processor,
                            'ram' => $computer->ram,
                            'storage' => $computer->storage,
                            'os' => $computer->os
                        ];
                    })->toArray();

            case 'monitors':
                return Monitor::with(['asset.category', 'asset.vendor'])
                    ->get()
                    ->map(function ($monitor) {
                        return [
                            'asset_tag' => $monitor->asset->asset_tag,
                            'category_name' => $monitor->asset->category->name ?? '',
                            'vendor_name' => $monitor->asset->vendor->name ?? '',
                            'name' => $monitor->asset->name,
                            'description' => $monitor->asset->description,
                            'serial_number' => $monitor->asset->serial_number,
                            'purchase_date' => $monitor->asset->purchase_date ? $monitor->asset->purchase_date->format('Y-m-d') : '',
                            'warranty_end' => $monitor->asset->warranty_end ? $monitor->asset->warranty_end->format('Y-m-d') : '',
                            'cost' => $monitor->asset->cost,
                            'status' => $monitor->asset->status,
                            'movement' => $monitor->asset->movement,
                            'size' => $monitor->size,
                            'resolution' => $monitor->resolution,
                            'panel_type' => $monitor->panel_type
                        ];
                    })->toArray();

            case 'printers':
                return Printer::with(['asset.category', 'asset.vendor'])
                    ->get()
                    ->map(function ($printer) {
                        return [
                            'asset_tag' => $printer->asset->asset_tag,
                            'category_name' => $printer->asset->category->name ?? '',
                            'vendor_name' => $printer->asset->vendor->name ?? '',
                            'name' => $printer->asset->name,
                            'description' => $printer->asset->description,
                            'serial_number' => $printer->asset->serial_number,
                            'purchase_date' => $printer->asset->purchase_date ? $printer->asset->purchase_date->format('Y-m-d') : '',
                            'warranty_end' => $printer->asset->warranty_end ? $printer->asset->warranty_end->format('Y-m-d') : '',
                            'cost' => $printer->asset->cost,
                            'status' => $printer->asset->status,
                            'movement' => $printer->asset->movement,
                            'type' => $printer->type,
                            'color_support' => $printer->color_support ? '1' : '0',
                            'duplex' => $printer->duplex ? '1' : '0'
                        ];
                    })->toArray();

            case 'peripherals':
                return Peripheral::with(['asset.category', 'asset.vendor'])
                    ->get()
                    ->map(function ($peripheral) {
                        return [
                            'asset_tag' => $peripheral->asset->asset_tag,
                            'category_name' => $peripheral->asset->category->name ?? '',
                            'vendor_name' => $peripheral->asset->vendor->name ?? '',
                            'name' => $peripheral->asset->name,
                            'description' => $peripheral->asset->description,
                            'serial_number' => $peripheral->asset->serial_number,
                            'purchase_date' => $peripheral->asset->purchase_date ? $peripheral->asset->purchase_date->format('Y-m-d') : '',
                            'warranty_end' => $peripheral->asset->warranty_end ? $peripheral->asset->warranty_end->format('Y-m-d') : '',
                            'cost' => $peripheral->asset->cost,
                            'status' => $peripheral->asset->status,
                            'type' => $peripheral->type,
                            'interface' => $peripheral->interface
                        ];
                    })->toArray();

            case 'departments':
                return Department::with('manager')
                    ->get()
                    ->map(function ($department) {
                        return [
                            'name' => $department->name,
                            'description' => $department->description,
                            'manager_email' => $department->manager->email ?? ''
                        ];
                    })->toArray();

            case 'vendors':
                return Vendor::all()
                    ->map(function ($vendor) {
                        return [
                            'name' => $vendor->name,
                            'contact_person' => $vendor->contact_person,
                            'email' => $vendor->email,
                            'phone' => $vendor->phone,
                            'address' => $vendor->address
                        ];
                    })->toArray();

            default:
                return [];
        }
    }

    /**
     * Convert array to CSV format
     */
    private function arrayToCsv($data)
    {
        if (empty($data)) {
            return '';
        }

        $output = fopen('php://temp', 'r+');
        
        // Add headers
        fputcsv($output, array_keys($data[0]));
        
        // Add data rows
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }

    /**
     * Validation methods for each module
     */
    private function validateUser($data, $rowNumber)
    {
        $validator = Validator::make($data, [
            'employee_no' => 'required|unique:users,employee_no',
            'employee_id' => 'nullable|unique:users,employee_id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'department_name' => 'required|string'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Check if department exists
        $department = Department::where('name', $data['department_name'])->first();
        if (!$department) {
            throw new \Exception("Department '{$data['department_name']}' not found.");
        }

        // Check if role exists (if provided)
        if (!empty($data['role_name'])) {
            $role = Role::where('name', $data['role_name'])->first();
            if (!$role) {
                throw new \Exception("Role '{$data['role_name']}' not found.");
            }
        }
    }

    private function validateAsset($data, $rowNumber)
    {
        $validator = Validator::make($data, [
            'asset_tag' => 'required|unique:assets,asset_tag',
            'category_name' => 'required|string',
            'vendor_name' => 'required|string',
            'name' => 'required|string|max:255',
            'purchase_date' => 'nullable|date',
            'warranty_end' => 'nullable|date',
            'cost' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Check if category exists
        $category = AssetCategory::where('name', $data['category_name'])->first();
        if (!$category) {
            throw new \Exception("Category '{$data['category_name']}' not found.");
        }

        // Check if vendor exists
        $vendor = Vendor::where('name', $data['vendor_name'])->first();
        if (!$vendor) {
            throw new \Exception("Vendor '{$data['vendor_name']}' not found.");
        }
    }

    private function validateComputer($data, $rowNumber)
    {
        $this->validateAsset($data, $rowNumber);
        
        $validator = Validator::make($data, [
            'processor' => 'required|string|max:255',
            'ram' => 'required|string|max:255',
            'storage' => 'required|string|max:255',
            'os' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }

    private function validateMonitor($data, $rowNumber)
    {
        $this->validateAsset($data, $rowNumber);
        
        $validator = Validator::make($data, [
            'size' => 'required|string|max:255',
            'resolution' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }

    private function validatePrinter($data, $rowNumber)
    {
        $this->validateAsset($data, $rowNumber);
        
        $validator = Validator::make($data, [
            'type' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }

    private function validatePeripheral($data, $rowNumber)
    {
        $this->validateAsset($data, $rowNumber);
        
        $validator = Validator::make($data, [
            'type' => 'required|string|max:255',
            'interface' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }

    private function validateDepartment($data, $rowNumber)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:departments,name'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Check if manager exists (if provided)
        if (!empty($data['manager_email'])) {
            $manager = User::where('email', $data['manager_email'])->first();
            if (!$manager) {
                throw new \Exception("Manager with email '{$data['manager_email']}' not found.");
            }
        }
    }

    private function validateVendor($data, $rowNumber)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:vendors,name',
            'email' => 'nullable|email'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }

    /**
     * Show import results page
     */
    public function showResults()
    {
        return view('import-results');
    }
}