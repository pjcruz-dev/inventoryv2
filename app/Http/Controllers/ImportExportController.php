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
        $this->middleware('auth');
        $this->middleware('permission:import_export_access');
        $this->templateService = $templateService;
    }

    /**
     * Show the improved import/export interface
     */
    public function interface()
    {
        return view('import-export.interface');
    }

    /**
     * Show the enhanced import/export interface (legacy)
     */
    public function enhancedInterface()
    {
        return view('import-export.enhanced-interface');
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
     * Download public template (no authentication required)
     */
    public function downloadPublicTemplate($module)
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
            
            // Log public template download (no user_id since not authenticated)
            Log::info('Public template downloaded', [
                'module' => $module,
                'filename' => $filename,
                'timestamp' => now(),
                'ip_address' => request()->ip()
            ]);
            
            return Response::make($csvContent, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'X-Template-Version' => '2.0',
                'X-Generated-At' => now()->toISOString()
            ]);
        } catch (Exception $e) {
            Log::error('Public template generation failed', [
                'module' => $module,
                'error' => $e->getMessage(),
                'ip_address' => request()->ip()
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
        
        // Extract field keys from the template headers structure
        $headerNames = array_keys($templateData['headers']);
        
        // Add headers - ensure they match exactly what validation expects
        fputcsv($output, $headerNames);
        
        // Add sample data rows
        foreach ($templateData['sample_data'] as $row) {
            // Convert associative array to indexed array in correct order
            $rowData = [];
            foreach ($headerNames as $key) {
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
        
        // Validate file exists and is readable
        if (!file_exists($path) || !is_readable($path)) {
            return redirect()->route('import-export.results')
                ->with('import_errors', [[
                    'row' => 1,
                    'field' => 'file',
                    'message' => 'File does not exist or is not readable.',
                    'value' => ''
                ]]);
        }
        
        // Read CSV file with error handling
        try {
            $csvData = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if ($csvData === false || empty($csvData)) {
                return redirect()->route('import-export.results')
                    ->with('import_errors', [[
                        'row' => 1,
                        'field' => 'file',
                        'message' => 'CSV file is empty or could not be read.',
                        'value' => ''
                    ]]);
            }
            
            // Filter out comment lines (lines starting with #)
            $csvData = array_filter($csvData, function($line) {
                return !empty(trim($line)) && !str_starts_with(trim($line), '#');
            });
            
            // Reindex array after filtering
            $csvData = array_values($csvData);
            
            if (empty($csvData)) {
                return redirect()->route('import-export.results')
                    ->with('import_errors', [[
                        'row' => 1,
                        'field' => 'file',
                        'message' => 'No valid CSV data found after filtering comments.',
                        'value' => ''
                    ]]);
            }
        } catch (\Exception $e) {
            return redirect()->route('import-export.results')
                ->with('import_errors', [[
                    'row' => 1,
                    'field' => 'file',
                    'message' => 'Error reading CSV file: ' . $e->getMessage(),
                    'value' => ''
                ]]);
        }

        // Parse CSV data with error handling
        try {
            $data = [];
            foreach ($csvData as $lineNumber => $line) {
                $parsedLine = str_getcsv($line);
                if ($parsedLine === false) {
                    return redirect()->route('import-export.results')
                        ->with('import_errors', [[
                            'row' => $lineNumber + 1,
                            'field' => 'csv_parsing',
                            'message' => 'Error parsing CSV line.',
                            'value' => substr($line, 0, 100) . '...'
                        ]]);
                }
                $data[] = $parsedLine;
            }
            
            if (empty($data)) {
                return redirect()->route('import-export.results')
                    ->with('import_errors', [[
                        'row' => 1,
                        'field' => 'file',
                        'message' => 'No valid CSV data found.',
                        'value' => ''
                    ]]);
            }
            
            $headers = array_shift($data);
        } catch (\Exception $e) {
            return redirect()->route('import-export.results')
                ->with('import_errors', [[
                    'row' => 1,
                    'field' => 'csv_parsing',
                    'message' => 'Error parsing CSV data: ' . $e->getMessage(),
                    'value' => ''
                ]]);
        }
        
        // Clean headers (remove BOM and trim)
        $headers = array_map(function($header) {
            // Remove various BOM types
            $header = str_replace(["\xEF\xBB\xBF", "\xFF\xFE", "\xFE\xFF"], '', $header);
            // Trim whitespace and normalize
            return trim($header);
        }, $headers);
        
        // Validate headers are not empty
        if (empty($headers) || empty(array_filter($headers))) {
            return redirect()->route('import-export.results')
                ->with('import_errors', [[
                    'row' => 1,
                    'field' => 'header',
                    'message' => 'CSV headers are missing or empty.',
                    'value' => ''
                ]]);
        }
        
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

    /**
     * Get template preview for specified module
     */
    public function getTemplatePreview($module)
    {
        try {
            $headers = $this->getRequiredFields($module);
            $sampleData = $this->getSampleData($module);
            
            return response()->json([
                'success' => true,
                'headers' => $headers,
                'sample_data' => $sampleData,
                'module' => $module
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating template preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk export multiple modules
     */
    public function bulkExport(Request $request)
    {
        $request->validate([
            'modules' => 'required|array',
            'modules.*' => 'string|in:users,assets,computers,monitors,printers,peripherals,departments,vendors'
        ]);

        try {
            $modules = $request->input('modules');
            $exportData = [];
            
            foreach ($modules as $module) {
                $data = $this->getExportData($module);
                $exportData[$module] = $data;
            }
            
            // Create a zip file with multiple CSV files
            $zipFileName = 'bulk_export_' . date('Y-m-d_H-i-s') . '.zip';
            $zipPath = storage_path('app/temp/' . $zipFileName);
            
            // Ensure temp directory exists
            if (!file_exists(dirname($zipPath))) {
                mkdir(dirname($zipPath), 0755, true);
            }
            
            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
                foreach ($exportData as $module => $data) {
                    $csvContent = $this->arrayToCsv($data);
                    $zip->addFromString($module . '_export.csv', $csvContent);
                }
                $zip->close();
                
                return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
            } else {
                throw new \Exception('Could not create zip file');
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get import status for ongoing imports
     */
    public function getImportStatus(Request $request)
    {
        try {
            // This would typically check a job queue or cache for import progress
            // For now, return a simple status
            return response()->json([
                'success' => true,
                'status' => 'completed',
                'message' => 'Import status check completed'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking import status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get import history for the current user
     */
    public function getImportHistory(Request $request)
    {
        try {
            // This would typically fetch from a database table storing import history
            // For now, return empty history
            return response()->json([
                'success' => true,
                'history' => [],
                'message' => 'Import history retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving import history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Convert array data to CSV format
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
     * Safely parse date string to Y-m-d format
     * 
     * @param string|null $dateString
     * @return string|null
     */
    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }

        // Try to parse the date
        $timestamp = strtotime($dateString);
        
        // If strtotime fails, try common date formats
        if ($timestamp === false) {
            $formats = [
                'Y-m-d',
                'd/m/Y',
                'm/d/Y',
                'd-m-Y',
                'm-d-Y',
                'Y/m/d',
                'd.m.Y',
                'm.d.Y'
            ];
            
            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, $dateString);
                if ($date && $date->format($format) === $dateString) {
                    return $date->format('Y-m-d');
                }
            }
            
            // If all parsing attempts fail, return null
            return null;
        }
        
        return date('Y-m-d', $timestamp);
    }

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
            case 'asset_categories':
                $this->validateAssetCategory($data, $rowNumber);
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
            case 'asset_categories':
                $this->importAssetCategory($data, $rowNumber);
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
        // Check for existing users with same employee_id or email_address
        $existingUser = null;
        
        if (!empty($data['employee_id'])) {
            $existingUser = User::where('employee_id', $data['employee_id'])->first();
            if ($existingUser) {
                throw new \Exception("User with employee ID '{$data['employee_id']}' already exists. Skipping duplicate entry.");
            }
        }
        
        if (!empty($data['email_address'])) {
            $existingUser = User::where('email', $data['email_address'])->first();
            if ($existingUser) {
                throw new \Exception("User with email '{$data['email_address']}' already exists. Skipping duplicate entry.");
            }
        }

        // Validate required fields
        $validator = Validator::make($data, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email_address' => 'required|email|max:255',
            'employee_id' => 'required|string|max:255',
            'department' => 'required|string'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Find department by name
        $department = Department::where('name', $data['department'])->first();
        if (!$department) {
            throw new \Exception("Department '{$data['department']}' not found.");
        }

        // Find role by name (optional)
        $role = null;
        if (!empty($data['role'])) {
            $role = Role::where('name', $data['role'])->first();
            if (!$role) {
                throw new \Exception("Role '{$data['role']}' not found.");
            }
        }

        User::create([
            'employee_id' => $data['employee_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email_address'],
            'department_id' => $department->id,
            'company' => $data['company'] ?? null,
            'position' => $data['job_title'] ?? null,
            'phone' => $data['phone_number'] ?? null,
            'role_id' => $role ? $role->id : null,
            'status' => $data['status'] ?? 'Active',
            'password' => bcrypt($data['password'] ?? 'password123')
        ]);
    }

    /**
     * Import asset data
     */
    private function importAsset($data, $rowNumber)
    {
        // Validate required fields (asset_tag is now optional for auto-generation)
        $validator = Validator::make($data, [
            'asset_tag' => 'nullable|string|max:255',
            'asset_name' => 'required|string|max:255',
            'category' => 'required|string',
            'vendor' => 'required|string',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Find category by name
        $category = AssetCategory::where('name', $data['category'])->first();
        if (!$category) {
            throw new \Exception("Category '{$data['category']}' not found.");
        }

        // Find vendor by name
        $vendor = Vendor::where('name', $data['vendor'])->first();
        if (!$vendor) {
            throw new \Exception("Vendor '{$data['vendor']}' not found.");
        }

        // Auto-generate unique asset tag if not provided or if it already exists
        $assetTag = $data['asset_tag'] ?? '';
        if (empty($assetTag) || Asset::where('asset_tag', $assetTag)->exists()) {
            $assetTag = $this->generateUniqueAssetTag($category);
        }

        Asset::create([
            'asset_tag' => $assetTag,
            'name' => $data['asset_name'],
            'category_id' => $category->id,
            'vendor_id' => $vendor->id,
            'status' => $data['status'] ?? 'Active',
            'movement' => $data['movement'] ?? 'New Arrival',
            'model' => $data['model'] ?? null,
            'serial_number' => $data['serial_number'] ?? null,
            'purchase_date' => $this->parseDate($data['purchase_date'] ?? null),
            'cost' => $data['purchase_cost'] ?? 0,
            'po_number' => $data['po_number'] ?? null,
            'entity' => $data['entity'] ?? null,
            'lifespan' => $data['lifespan'] ?? null,
            'location' => $data['location'] ?? null,
            'description' => $data['notes'] ?? null
        ]);
    }

    /**
     * Generate unique asset tag for import
     */
    private function generateUniqueAssetTag($category)
    {
        $prefix = strtoupper(substr($category->name, 0, 3));
        $counter = 1;
        
        do {
            $assetTag = $prefix . '-' . str_pad($counter, 6, '0', STR_PAD_LEFT);
            $counter++;
        } while (Asset::where('asset_tag', $assetTag)->exists());
        
        return $assetTag;
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
            'memory_ram' => 'required|string|max:255',
            'storage' => 'required|string|max:255',
            'operating_system' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Then create the computer record
        Computer::create([
            'asset_id' => $asset->id,
            'processor' => $data['processor'],
            'ram' => $data['memory_ram'],
            'storage' => $data['storage'],
            'graphics_card' => $data['graphics_card'] ?? null,
            'computer_type' => $data['computer_type'] ?? null,
            'os' => $data['operating_system']
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
            'printer_type' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Then create the printer record
        Printer::create([
            'asset_id' => $asset->id,
            'type' => $data['printer_type'],
            'color_support' => filter_var($data['color_support'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'duplex' => filter_var($data['duplex_printing'] ?? false, FILTER_VALIDATE_BOOLEAN)
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
            'peripheral_type' => 'required|string|max:255',
            'interface' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Then create the peripheral record
        Peripheral::create([
            'asset_id' => $asset->id,
            'type' => $data['peripheral_type'],
            'interface' => $data['interface']
        ]);
    }

    /**
     * Import department data
     */
    private function importDepartment($data, $rowNumber)
    {
        // Check if department with this name already exists
        $existingDepartment = Department::where('name', $data['department_name'])->first();
        
        if ($existingDepartment) {
            // Skip duplicate department names with a warning
            throw new \Exception("Department with name '{$data['department_name']}' already exists. Skipping duplicate entry.");
        }

        // Validate required fields (removed unique constraint since we handle it above)
        $validator = Validator::make($data, [
            'department_name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        Department::create([
            'name' => $data['department_name'],
            'code' => $data['department_code'] ?? null,
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'Active',
            'location' => $data['location'] ?? null,
            'budget' => $data['budget'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null
        ]);
    }

    /**
     * Import vendor data
     */
    private function importVendor($data, $rowNumber)
    {
        // Check if vendor with this name already exists
        $existingVendor = Vendor::where('name', $data['vendor_name'])->first();
        
        if ($existingVendor) {
            // Skip duplicate vendor names with a warning
            throw new \Exception("Vendor with name '{$data['vendor_name']}' already exists. Skipping duplicate entry.");
        }

        // Validate required fields (removed unique constraint since we handle it above)
        $validator = Validator::make($data, [
            'vendor_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'tax_id' => 'nullable|string|max:50',
            'payment_terms' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Validate phone number format (if provided)
        if (!empty($data['phone'])) {
            $cleanPhone = preg_replace('/[\s\-\(\)\+]/', '', $data['phone']);
            if (!preg_match('/^\d{7,15}$/', $cleanPhone)) {
                throw new \Exception("Invalid phone number format '{$data['phone']}'. Use format like '+1234567890' or '123-456-7890'.");
            }
        }

        // Validate status if provided
        if (!empty($data['status'])) {
            $validStatuses = ['Active', 'Inactive', 'Preferred', 'Blacklisted', 'Under Review'];
            if (!in_array($data['status'], $validStatuses)) {
                throw new \Exception("Invalid status '{$data['status']}'. Valid statuses: " . implode(', ', $validStatuses));
            }
        }

        // Validate tax ID format (if provided)
        if (!empty($data['tax_id'])) {
            // Basic validation for tax ID (customize based on your country's format)
            if (!preg_match('/^[A-Z0-9\-]{5,20}$/', $data['tax_id'])) {
                throw new \Exception("Invalid tax ID format '{$data['tax_id']}'. Use alphanumeric format with dashes.");
            }
        }

        // Validate payment terms if provided
        if (!empty($data['payment_terms'])) {
            $validTerms = ['Net 30', 'Net 60', 'Net 90', 'COD', 'Prepaid', '2/10 Net 30', 'Due on Receipt'];
            if (!in_array($data['payment_terms'], $validTerms)) {
                throw new \Exception("Invalid payment terms '{$data['payment_terms']}'. Valid terms: " . implode(', ', $validTerms));
            }
        }

        Vendor::create([
            'name' => $data['vendor_name'],
            'contact_person' => $data['contact_person'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null
        ]);
    }

    /**
     * Import asset category data
     */
    private function importAssetCategory($data, $rowNumber)
    {
        // Check if asset category with this name already exists
        $existingCategory = AssetCategory::where('name', $data['category_name'])->first();
        
        if ($existingCategory) {
            // Skip duplicate category names with a warning
            throw new \Exception("Asset category with name '{$data['category_name']}' already exists. Skipping duplicate entry.");
        }

        // Validate required fields
        $validator = Validator::make($data, [
            'category_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'category_code' => 'nullable|string|max:50',
            'depreciation_rate' => 'nullable|numeric|min:0|max:100',
            'useful_life' => 'nullable|integer|min:1|max:50',
            'status' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Check for duplicate category code (if provided)
        if (!empty($data['category_code'])) {
            $existingCode = AssetCategory::where('code', $data['category_code'])->first();
            if ($existingCode) {
                throw new \Exception("Asset category with code '{$data['category_code']}' already exists.");
            }
        }

        // Validate status if provided
        if (!empty($data['status'])) {
            $validStatuses = ['Active', 'Inactive', 'Deprecated'];
            if (!in_array($data['status'], $validStatuses)) {
                throw new \Exception("Invalid status '{$data['status']}'. Valid statuses: " . implode(', ', $validStatuses));
            }
        }

        // Validate category code format (if provided)
        if (!empty($data['category_code'])) {
            if (!preg_match('/^[A-Z0-9]{2,10}$/', $data['category_code'])) {
                throw new \Exception("Invalid category code format '{$data['category_code']}'. Use 2-10 uppercase alphanumeric characters.");
            }
        }

        // Validate depreciation rate and useful life consistency
        if (!empty($data['depreciation_rate']) && !empty($data['useful_life'])) {
            $calculatedRate = 100 / $data['useful_life'];
            $tolerance = 5; // 5% tolerance
            if (abs($data['depreciation_rate'] - $calculatedRate) > $tolerance) {
                throw new \Exception("Depreciation rate ({$data['depreciation_rate']}%) and useful life ({$data['useful_life']} years) are inconsistent.");
            }
        }

        AssetCategory::create([
            'name' => $data['category_name'],
            'description' => $data['description'] ?? null
        ]);
    }

    /**
     * Helper method to create asset from data
     */
    private function createAssetFromData($data, $rowNumber)
    {
        // Check if asset with this tag already exists
        $existingAsset = Asset::where('asset_tag', $data['asset_tag'])->first();
        
        if ($existingAsset) {
            // Skip duplicate asset tags with a warning
            throw new \Exception("Asset with tag '{$data['asset_tag']}' already exists. Skipping duplicate entry.");
        }

        // Validate required fields
        $validator = Validator::make($data, [
            'asset_id' => 'required|string|max:255',
            'asset_name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Find asset by asset_id or asset_name
        $asset = null;
        if (!empty($data['asset_id'])) {
            $asset = Asset::where('asset_tag', $data['asset_id'])->first();
        }
        if (!$asset && !empty($data['asset_name'])) {
            $asset = Asset::where('name', $data['asset_name'])->first();
        }
        
        if (!$asset) {
            throw new \Exception("Asset with ID/Name '{$data['asset_id']}'/'{$data['asset_name']}' not found.");
        }

        return $asset;
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
                            'first_name' => $user->first_name,
                            'last_name' => $user->last_name,
                            'email_address' => $user->email,
                            'employee_id' => $user->employee_id,
                            'department' => $user->department->name ?? '',
                            'company' => $user->company,
                            'job_title' => $user->position,
                            'phone_number' => $user->phone,
                            'status' => $user->status,
                            'role' => $user->role->name ?? '',
                            'password' => '',
                            'confirm_password' => ''
                        ];
                    })->toArray();

            case 'assets':
                return Asset::with(['category', 'vendor', 'assignedUser.department'])
                    ->get()
                    ->map(function ($asset) {
                        return [
                            'asset_tag' => $asset->asset_tag,
                            'asset_name' => $asset->name,
                            'category' => $asset->category->name ?? '',
                            'status' => $asset->status,
                            'movement' => $asset->movement,
                            'model' => $asset->model,
                            'serial_number' => $asset->serial_number,
                            'vendor' => $asset->vendor->name ?? '',
                            'purchase_date' => $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : '',
                            'purchase_cost' => $asset->cost,
                            'po_number' => $asset->po_number,
                            'entity' => $asset->entity,
                            'lifespan' => $asset->lifespan,
                            'location' => $asset->location,
                            'notes' => $asset->description
                        ];
                    })->toArray();

            case 'computers':
                return Computer::with(['asset.category', 'asset.vendor', 'asset.assignedUser.department'])
                    ->get()
                    ->map(function ($computer) {
                        return [
                            'asset_id' => $computer->asset->asset_tag,
                            'asset_name' => $computer->asset->name,
                            'processor' => $computer->processor,
                            'memory_ram' => $computer->ram,
                            'storage' => $computer->storage,
                            'graphics_card' => $computer->graphics_card,
                            'computer_type' => $computer->computer_type,
                            'operating_system' => $computer->os
                        ];
                    })->toArray();

            case 'monitors':
                return Monitor::with(['asset.category', 'asset.vendor', 'asset.assignedUser.department'])
                    ->get()
                    ->map(function ($monitor) {
                        return [
                            'asset_id' => $monitor->asset->asset_tag,
                            'asset_name' => $monitor->asset->name,
                            'size' => $monitor->size,
                            'resolution' => $monitor->resolution,
                            'panel_type' => $monitor->panel_type
                        ];
                    })->toArray();

            case 'printers':
                return Printer::with(['asset.category', 'asset.vendor', 'asset.assignedUser.department'])
                    ->get()
                    ->map(function ($printer) {
                        return [
                            'asset_id' => $printer->asset->asset_tag,
                            'asset_name' => $printer->asset->name,
                            'printer_type' => $printer->type,
                            'color_support' => $printer->color_support ? 'Yes' : 'No',
                            'duplex_printing' => $printer->duplex ? 'Yes' : 'No'
                        ];
                    })->toArray();

            case 'peripherals':
                return Peripheral::with(['asset.category', 'asset.vendor', 'asset.assignedUser.department'])
                    ->get()
                    ->map(function ($peripheral) {
                        return [
                            'asset_id' => $peripheral->asset->asset_tag,
                            'asset_name' => $peripheral->asset->name,
                            'peripheral_type' => $peripheral->type,
                            'interface' => $peripheral->interface
                        ];
                    })->toArray();

            case 'departments':
                return Department::with('manager')
                    ->get()
                    ->map(function ($department) {
                        return [
                            'department_name' => $department->name,
                            'department_code' => $department->code,
                            'description' => $department->description,
                            'status' => $department->status,
                            'location' => $department->location,
                            'budget' => $department->budget,
                            'phone' => $department->phone,
                            'email' => $department->email
                        ];
                    })->toArray();

            case 'vendors':
                return Vendor::all()
                    ->map(function ($vendor) {
                        return [
                            'vendor_name' => $vendor->name,
                            'contact_person' => $vendor->contact_person,
                            'email' => $vendor->email,
                            'phone' => $vendor->phone,
                            'address' => $vendor->address
                        ];
                    })->toArray();

            case 'asset_categories':
                return AssetCategory::all()
                    ->map(function ($category) {
                        return [
                            'category_name' => $category->name,
                            'description' => $category->description
                        ];
                    })->toArray();

            default:
                return [];
        }
    }



    /**
     * Validation methods for each module
     */
    private function validateUser($data, $rowNumber)
    {
        // Check for existing users with same employee_no, employee_id, or email
        $existingUser = null;
        
        if (!empty($data['employee_no'])) {
            $existingUser = User::where('employee_no', $data['employee_no'])->first();
            if ($existingUser) {
                throw new \Exception("User with employee number '{$data['employee_no']}' already exists. Skipping duplicate entry.");
            }
        }
        
        if (!empty($data['employee_id'])) {
            $existingUser = User::where('employee_id', $data['employee_id'])->first();
            if ($existingUser) {
                throw new \Exception("User with employee ID '{$data['employee_id']}' already exists. Skipping duplicate entry.");
            }
        }
        
        if (!empty($data['email_address'])) {
            $existingUser = User::where('email', $data['email_address'])->first();
            if ($existingUser) {
                throw new \Exception("User with email '{$data['email_address']}' already exists. Skipping duplicate entry.");
            }
        }

        $validator = Validator::make($data, [
            'employee_id' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email_address' => 'required|email|max:255',
            'department' => 'required|string',
            'company' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'status' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|max:255'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Check if department exists
        $department = Department::where('name', $data['department'])->first();
        if (!$department) {
            throw new \Exception("Department '{$data['department']}' not found.");
        }

        // Check if role exists (if provided)
        if (!empty($data['role'])) {
            $role = Role::where('name', $data['role'])->first();
            if (!$role) {
                throw new \Exception("Role '{$data['role']}' not found.");
            }
        }

        // Validate phone number format (if provided)
        if (!empty($data['phone'])) {
            // Remove common separators and check if it's numeric
            $cleanPhone = preg_replace('/[\s\-\(\)\+]/', '', $data['phone']);
            if (!preg_match('/^\d{7,15}$/', $cleanPhone)) {
                throw new \Exception("Invalid phone number format '{$data['phone']}'. Use format like '+1234567890' or '123-456-7890'.");
            }
        }

        // Validate status if provided
        if (!empty($data['status'])) {
            $validStatuses = ['Active', 'Inactive', 'Suspended', 'Terminated', 'On Leave'];
            if (!in_array($data['status'], $validStatuses)) {
                throw new \Exception("Invalid status '{$data['status']}'. Valid statuses: " . implode(', ', $validStatuses));
            }
        }

        // Validate employee ID format (customize based on your organization's format)
        if (!empty($data['employee_id'])) {
            // Example: EMP001, EMP-001, or numeric IDs
            if (!preg_match('/^[A-Z]{2,4}[\-]?\d{3,6}$|^\d{3,8}$/', $data['employee_id'])) {
                throw new \Exception("Invalid employee ID format '{$data['employee_id']}'. Use format like 'EMP001' or '123456'.");
            }
        }

        // Validate email domain (if you want to restrict to company domains)
        if (!empty($data['email_address'])) {
            $emailDomain = substr(strrchr($data['email_address'], '@'), 1);
            // You can add domain validation here if needed
            // $allowedDomains = ['company.com', 'organization.org'];
            // if (!in_array($emailDomain, $allowedDomains)) {
            //     throw new \Exception("Email domain '{$emailDomain}' is not allowed.");
            // }
        }

        // Validate password strength (if provided)
        if (!empty($data['password'])) {
            if (strlen($data['password']) < 8) {
                throw new \Exception("Password must be at least 8 characters long.");
            }
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $data['password'])) {
                throw new \Exception("Password must contain at least one uppercase letter, one lowercase letter, and one number.");
            }
        }
    }

    private function validateAsset($data, $rowNumber)
    {
        // Check if asset with this tag already exists
        $existingAsset = Asset::where('asset_tag', $data['asset_tag'])->first();
        
        if ($existingAsset) {
            // Skip duplicate asset tags with a warning
            throw new \Exception("Asset with tag '{$data['asset_tag']}' already exists. Skipping duplicate entry.");
        }

        // Check for duplicate serial numbers if provided
        if (!empty($data['serial_number'])) {
            $existingSerial = Asset::where('serial_number', $data['serial_number'])->first();
            if ($existingSerial) {
                throw new \Exception("Asset with serial number '{$data['serial_number']}' already exists. Skipping duplicate entry.");
            }
        }

        $validator = Validator::make($data, [
            'asset_tag' => 'required|string|max:255',
            'asset_name' => 'required|string|max:255',
            'category' => 'required|string',
            'vendor' => 'required|string',
            'purchase_date' => 'nullable|date|before_or_equal:today',
            'purchase_cost' => 'nullable|numeric|min:0|max:999999999.99',
            'serial_number' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'po_number' => 'nullable|string|max:255',
            'entity' => 'nullable|string|max:255',
            'lifespan' => 'nullable|integer|min:1|max:50',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Check if category exists
        $category = AssetCategory::where('name', $data['category'])->first();
        if (!$category) {
            throw new \Exception("Category '{$data['category']}' not found.");
        }

        // Check if vendor exists
        $vendor = Vendor::where('name', $data['vendor'])->first();
        if (!$vendor) {
            throw new \Exception("Vendor '{$data['vendor']}' not found.");
        }

        // Validate status if provided
        if (!empty($data['status'])) {
            $validStatuses = ['Active', 'Inactive', 'In Repair', 'Disposed', 'Lost', 'Stolen', 'Reserved'];
            if (!in_array($data['status'], $validStatuses)) {
                throw new \Exception("Invalid status '{$data['status']}'. Valid statuses: " . implode(', ', $validStatuses));
            }
        }

        // Validate purchase date is not in the future
        if (!empty($data['purchase_date'])) {
            $purchaseDate = \Carbon\Carbon::parse($data['purchase_date']);
            if ($purchaseDate->isFuture()) {
                throw new \Exception("Purchase date '{$data['purchase_date']}' cannot be in the future.");
            }
        }

        // Validate lifespan is reasonable
        if (!empty($data['lifespan']) && ($data['lifespan'] < 1 || $data['lifespan'] > 50)) {
            throw new \Exception("Invalid lifespan '{$data['lifespan']}'. Must be between 1 and 50 years.");
        }

        // Check if location exists (if provided)
        if (!empty($data['location'])) {
            // You can add location validation here if you have a locations table
            // $location = Location::where('name', $data['location'])->first();
            // if (!$location) {
            //     throw new \Exception("Location '{$data['location']}' not found.");
            // }
        }
    }

    private function validateComputer($data, $rowNumber)
    {
        // For computers, we validate the asset reference instead of full asset data
        $validator = Validator::make($data, [
            'asset_id' => 'required|string|max:255',
            'asset_name' => 'required|string|max:255',
            'processor' => 'required|string|max:255',
            'memory_ram' => 'required|string|max:255',
            'storage' => 'required|string|max:255',
            'operating_system' => 'required|string|max:255',
            'graphics_card' => 'nullable|string|max:255',
            'computer_type' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Check if asset exists by asset_id or asset_name
        $asset = null;
        if (!empty($data['asset_id'])) {
            $asset = Asset::where('asset_tag', $data['asset_id'])->first();
        }
        if (!$asset && !empty($data['asset_name'])) {
            $asset = Asset::where('name', $data['asset_name'])->first();
        }
        
        if (!$asset) {
            throw new \Exception("Asset with ID/Tag '{$data['asset_id']}' or name '{$data['asset_name']}' not found.");
        }

        // Check if computer record already exists for this asset
        $existingComputer = Computer::where('asset_id', $asset->id)->first();
        if ($existingComputer) {
            throw new \Exception("Computer record for asset '{$data['asset_id']}' already exists. Skipping duplicate entry.");
        }

        // Validate memory format (e.g., "8GB", "16 GB")
        if (!empty($data['memory_ram']) && !preg_match('/^\d+\s*(GB|MB|TB)$/i', trim($data['memory_ram']))) {
            throw new \Exception("Invalid memory format '{$data['memory_ram']}'. Use format like '8GB' or '16 GB'.");
        }

        // Validate storage format (e.g., "500GB", "1TB")
        if (!empty($data['storage']) && !preg_match('/^\d+\s*(GB|TB|MB)$/i', trim($data['storage']))) {
            throw new \Exception("Invalid storage format '{$data['storage']}'. Use format like '500GB' or '1TB'.");
        }
    }

    private function validateMonitor($data, $rowNumber)
    {
        $validator = Validator::make($data, [
            'asset_id' => 'required|string|max:255',
            'asset_name' => 'required|string|max:255',
            'size' => 'required|string|max:255',
            'resolution' => 'required|string|max:255',
            'panel_type' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Check if asset exists by asset_id or asset_name
        $asset = null;
        if (!empty($data['asset_id'])) {
            $asset = Asset::where('asset_tag', $data['asset_id'])->first();
        }
        if (!$asset && !empty($data['asset_name'])) {
            $asset = Asset::where('name', $data['asset_name'])->first();
        }
        
        if (!$asset) {
            throw new \Exception("Asset with ID/Tag '{$data['asset_id']}' or name '{$data['asset_name']}' not found.");
        }

        // Check if monitor record already exists for this asset
        $existingMonitor = Monitor::where('asset_id', $asset->id)->first();
        if ($existingMonitor) {
            throw new \Exception("Monitor record for asset '{$data['asset_id']}' already exists. Skipping duplicate entry.");
        }

        // Validate size format (e.g., "24", "27", "32")
        if (!empty($data['size']) && !preg_match('/^\d+(\.\d+)?$/', trim($data['size']))) {
            throw new \Exception("Invalid size format '{$data['size']}'. Use numeric value like '24' or '27.5'.");
        }

        // Validate resolution format (e.g., "1920x1080", "2560x1440")
        if (!empty($data['resolution']) && !preg_match('/^\d+x\d+$/i', trim($data['resolution']))) {
            throw new \Exception("Invalid resolution format '{$data['resolution']}'. Use format like '1920x1080' or '2560x1440'.");
        }
    }

    private function validatePrinter($data, $rowNumber)
    {
        $validator = Validator::make($data, [
            'asset_id' => 'required|string|max:255',
            'asset_name' => 'required|string|max:255',
            'printer_type' => 'required|string|max:255',
            'color_support' => 'nullable|boolean',
            'duplex_printing' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Check if asset exists by asset_id or asset_name
        $asset = null;
        if (!empty($data['asset_id'])) {
            $asset = Asset::where('asset_tag', $data['asset_id'])->first();
        }
        if (!$asset && !empty($data['asset_name'])) {
            $asset = Asset::where('name', $data['asset_name'])->first();
        }
        
        if (!$asset) {
            throw new \Exception("Asset with ID/Tag '{$data['asset_id']}' or name '{$data['asset_name']}' not found.");
        }

        // Check if printer record already exists for this asset
        $existingPrinter = Printer::where('asset_id', $asset->id)->first();
        if ($existingPrinter) {
            throw new \Exception("Printer record for asset '{$data['asset_id']}' already exists. Skipping duplicate entry.");
        }

        // Validate printer type
        $validPrinterTypes = ['Inkjet', 'Laser', 'Dot Matrix', 'Thermal', 'LED', '3D Printer'];
        if (!empty($data['printer_type']) && !in_array($data['printer_type'], $validPrinterTypes)) {
            throw new \Exception("Invalid printer type '{$data['printer_type']}'. Valid types: " . implode(', ', $validPrinterTypes));
        }

        // Validate boolean fields
        if (!empty($data['color_support']) && !in_array(strtolower($data['color_support']), ['true', 'false', '1', '0', 'yes', 'no'])) {
            throw new \Exception("Invalid color support value '{$data['color_support']}'. Use: true/false, 1/0, or yes/no.");
        }

        if (!empty($data['duplex_printing']) && !in_array(strtolower($data['duplex_printing']), ['true', 'false', '1', '0', 'yes', 'no'])) {
            throw new \Exception("Invalid duplex printing value '{$data['duplex_printing']}'. Use: true/false, 1/0, or yes/no.");
        }
    }

    private function validatePeripheral($data, $rowNumber)
    {
        $validator = Validator::make($data, [
            'asset_id' => 'required|string|max:255',
            'asset_name' => 'required|string|max:255',
            'peripheral_type' => 'required|string|max:255',
            'interface' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Check if asset exists by asset_id or asset_name
        $asset = null;
        if (!empty($data['asset_id'])) {
            $asset = Asset::where('asset_tag', $data['asset_id'])->first();
        }
        if (!$asset && !empty($data['asset_name'])) {
            $asset = Asset::where('name', $data['asset_name'])->first();
        }
        
        if (!$asset) {
            throw new \Exception("Asset with ID/Tag '{$data['asset_id']}' or name '{$data['asset_name']}' not found.");
        }

        // Check if peripheral record already exists for this asset
        $existingPeripheral = Peripheral::where('asset_id', $asset->id)->first();
        if ($existingPeripheral) {
            throw new \Exception("Peripheral record for asset '{$data['asset_id']}' already exists. Skipping duplicate entry.");
        }

        // Validate peripheral type
        $validPeripheralTypes = ['Mouse', 'Keyboard', 'Speaker', 'Webcam', 'Microphone', 'Headset', 'USB Hub', 'External Drive', 'Scanner', 'Graphics Tablet'];
        if (!empty($data['peripheral_type']) && !in_array($data['peripheral_type'], $validPeripheralTypes)) {
            throw new \Exception("Invalid peripheral type '{$data['peripheral_type']}'. Valid types: " . implode(', ', $validPeripheralTypes));
        }

        // Validate interface type
        $validInterfaces = ['USB', 'USB-C', 'Bluetooth', 'Wireless', 'PS/2', 'Audio Jack', 'HDMI', 'DisplayPort', 'Thunderbolt', 'Ethernet'];
        if (!empty($data['interface']) && !in_array($data['interface'], $validInterfaces)) {
            throw new \Exception("Invalid interface '{$data['interface']}'. Valid interfaces: " . implode(', ', $validInterfaces));
        }
    }

    private function validateDepartment($data, $rowNumber)
    {
        // Check if department with this name already exists
        $existingDepartment = Department::where('name', $data['department_name'])->first();
        
        if ($existingDepartment) {
            // Skip duplicate department names with a warning
            throw new \Exception("Department with name '{$data['department_name']}' already exists. Skipping duplicate entry.");
        }

        $validator = Validator::make($data, [
            'department_name' => 'required|string|max:255',
            'department_code' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'status' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'budget' => 'nullable|numeric|min:0|max:999999999.99',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_email' => 'nullable|email|max:255'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Check for duplicate department code if provided
        if (!empty($data['department_code'])) {
            $existingCode = Department::where('code', $data['department_code'])->first();
            if ($existingCode) {
                throw new \Exception("Department with code '{$data['department_code']}' already exists. Skipping duplicate entry.");
            }
        }

        // Check if manager exists (if provided)
        if (!empty($data['manager_email'])) {
            $manager = User::where('email', $data['manager_email'])->first();
            if (!$manager) {
                throw new \Exception("Manager with email '{$data['manager_email']}' not found.");
            }
        }

        // Validate status if provided
        if (!empty($data['status'])) {
            $validStatuses = ['Active', 'Inactive', 'Restructuring', 'Closed'];
            if (!in_array($data['status'], $validStatuses)) {
                throw new \Exception("Invalid status '{$data['status']}'. Valid statuses: " . implode(', ', $validStatuses));
            }
        }

        // Validate phone number format (if provided)
        if (!empty($data['phone'])) {
            $cleanPhone = preg_replace('/[\s\-\(\)\+]/', '', $data['phone']);
            if (!preg_match('/^\d{7,15}$/', $cleanPhone)) {
                throw new \Exception("Invalid phone number format '{$data['phone']}'. Use format like '+1234567890' or '123-456-7890'.");
            }
        }

        // Validate department code format (if provided)
        if (!empty($data['department_code'])) {
            if (!preg_match('/^[A-Z0-9]{2,10}$/', $data['department_code'])) {
                throw new \Exception("Invalid department code format '{$data['department_code']}'. Use 2-10 uppercase letters/numbers like 'IT' or 'HR01'.");
            }
        }
    }

    private function validateVendor($data, $rowNumber)
    {
        // Check if vendor with this name already exists
        $existingVendor = Vendor::where('name', $data['vendor_name'])->first();
        
        if ($existingVendor) {
            // Skip duplicate vendor names with a warning
            throw new \Exception("Vendor with name '{$data['vendor_name']}' already exists. Skipping duplicate entry.");
        }

        $validator = Validator::make($data, [
            'vendor_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'tax_id' => 'nullable|string|max:50',
            'payment_terms' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Validate phone number format (if provided)
        if (!empty($data['phone'])) {
            $cleanPhone = preg_replace('/[\s\-\(\)\+]/', '', $data['phone']);
            if (!preg_match('/^\d{7,15}$/', $cleanPhone)) {
                throw new \Exception("Invalid phone number format '{$data['phone']}'. Use format like '+1234567890' or '123-456-7890'.");
            }
        }

        // Validate status if provided
        if (!empty($data['status'])) {
            $validStatuses = ['Active', 'Inactive', 'Preferred', 'Blacklisted', 'Under Review'];
            if (!in_array($data['status'], $validStatuses)) {
                throw new \Exception("Invalid status '{$data['status']}'. Valid statuses: " . implode(', ', $validStatuses));
            }
        }

        // Validate tax ID format (if provided)
        if (!empty($data['tax_id'])) {
            // Basic validation for tax ID (customize based on your country's format)
            if (!preg_match('/^[A-Z0-9\-]{5,20}$/', $data['tax_id'])) {
                throw new \Exception("Invalid tax ID format '{$data['tax_id']}'. Use alphanumeric format with dashes.");
            }
        }

        // Validate payment terms if provided
        if (!empty($data['payment_terms'])) {
            $validTerms = ['Net 30', 'Net 60', 'Net 90', 'COD', 'Prepaid', '2/10 Net 30', 'Due on Receipt'];
            if (!in_array($data['payment_terms'], $validTerms)) {
                throw new \Exception("Invalid payment terms '{$data['payment_terms']}'. Valid terms: " . implode(', ', $validTerms));
            }
        }
    }

    private function validateAssetCategory($data, $rowNumber)
    {
        // Check if asset category with this name already exists
        $existingCategory = AssetCategory::where('name', $data['category_name'])->first();
        
        if ($existingCategory) {
            // Skip duplicate category names with a warning
            throw new \Exception("Asset category with name '{$data['category_name']}' already exists. Skipping duplicate entry.");
        }

        $validator = Validator::make($data, [
            'category_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'category_code' => 'nullable|string|max:50',
            'depreciation_rate' => 'nullable|numeric|min:0|max:100',
            'useful_life' => 'nullable|integer|min:1|max:50',
            'status' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Check for duplicate category code (if provided)
        if (!empty($data['category_code'])) {
            $existingCode = AssetCategory::where('code', $data['category_code'])->first();
            if ($existingCode) {
                throw new \Exception("Asset category with code '{$data['category_code']}' already exists.");
            }
        }

        // Validate status if provided
        if (!empty($data['status'])) {
            $validStatuses = ['Active', 'Inactive', 'Deprecated'];
            if (!in_array($data['status'], $validStatuses)) {
                throw new \Exception("Invalid status '{$data['status']}'. Valid statuses: " . implode(', ', $validStatuses));
            }
        }

        // Validate category code format (if provided)
        if (!empty($data['category_code'])) {
            if (!preg_match('/^[A-Z0-9]{2,10}$/', $data['category_code'])) {
                throw new \Exception("Invalid category code format '{$data['category_code']}'. Use 2-10 uppercase alphanumeric characters.");
            }
        }

        // Validate depreciation rate and useful life consistency
        if (!empty($data['depreciation_rate']) && !empty($data['useful_life'])) {
            $calculatedRate = 100 / $data['useful_life'];
            $tolerance = 5; // 5% tolerance
            if (abs($data['depreciation_rate'] - $calculatedRate) > $tolerance) {
                throw new \Exception("Depreciation rate ({$data['depreciation_rate']}%) and useful life ({$data['useful_life']} years) are inconsistent.");
            }
        }
    }

    /**
     * Validate import data without importing
     */
    public function validateImport(Request $request, $module)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240'
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        
        // Read CSV file with error handling
        try {
            $csvData = file($path);
            if (empty($csvData)) {
                return response()->json([
                    'success' => false,
                    'errors' => [[
                        'row' => 1,
                        'field' => 'file',
                        'message' => 'CSV file is empty or could not be read.',
                        'value' => ''
                    ]]
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => [[
                    'row' => 1,
                    'field' => 'file',
                    'message' => 'Failed to read CSV file: ' . $e->getMessage(),
                    'value' => ''
                ]]
            ]);
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
            return response()->json([
                'success' => false,
                'errors' => [[
                    'row' => 1,
                    'field' => 'header',
                    'message' => 'Missing required columns: ' . implode(', ', $missingFields),
                    'value' => implode(', ', $headers)
                ]]
            ]);
        }

        $errors = [];
        $warnings = [];
        $totalRows = count($data);

        // Validate each row
        foreach ($data as $index => $row) {
            $rowNumber = $index + 2;
            
            // Skip empty rows
            if (empty(array_filter($row))) {
                $warnings[] = [
                    'row' => $rowNumber,
                    'message' => 'Empty row will be skipped',
                    'field' => 'general'
                ];
                continue;
            }
            
            $rowData = array_combine($headers, array_pad($row, count($headers), ''));
            
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

        return response()->json([
            'success' => empty($errors),
            'summary' => [
                'total' => $totalRows,
                'errors' => count($errors),
                'warnings' => count($warnings)
            ],
            'errors' => $errors,
            'warnings' => $warnings
        ]);
    }

    /**
     * Show import results page
     */
    public function showResults()
    {
        return view('import-results');
    }
}