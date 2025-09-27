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
        $validModules = ['users', 'assets', 'computers', 'monitors', 'printers', 'peripherals', 'departments', 'vendors', 'asset_categories'];
        
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
                'user_id' => auth()->id() ?? 'guest',
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
        $validModules = ['users', 'assets', 'computers', 'monitors', 'printers', 'peripherals', 'departments', 'vendors', 'asset_categories'];
        
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
        
        // Extract field keys from the template headers structure
        $headerKeys = array_keys($templateData['headers']);
        $headerNames = array_map(function($key) use ($templateData) {
            return $templateData['headers'][$key]['name'] ?? $key;
        }, $headerKeys);
        
        // Add headers FIRST - this is critical for validation
        fputcsv($output, $headerNames);
        
        // Add sample data rows
        foreach ($templateData['sample_data'] as $row) {
            // Convert associative array to indexed array in correct order
            $rowData = [];
            foreach ($headerKeys as $key) {
                $rowData[] = $row[$key] ?? '';
            }
            fputcsv($output, $rowData);
        }
        
        // Add one empty row for user data
        fputcsv($output, array_fill(0, count($headerKeys), ''));
        
        // Add clear separator between data and instructions
        fwrite($output, "\n# ===========================================\n");
        fwrite($output, "# END OF DATA - INSTRUCTIONS BELOW\n");
        fwrite($output, "# ===========================================\n");
        fwrite($output, "# IMPORT TEMPLATE - READ INSTRUCTIONS CAREFULLY\n");
        fwrite($output, "# ===========================================\n");
        fwrite($output, "# Template Version: 2.0\n");
        fwrite($output, "# Generated: " . now()->toISOString() . "\n");
        fwrite($output, "# \n");
        fwrite($output, "# IMPORTANT INSTRUCTIONS:\n");
        fwrite($output, "# 1. DO NOT DELETE OR MODIFY THE HEADER ROW (Row 1)\n");
        fwrite($output, "# 2. Fill in your data starting from row 2\n");
        fwrite($output, "# 3. Required fields are marked with * in descriptions below\n");
        fwrite($output, "# 4. Use EXACT values as shown in sample data\n");
        fwrite($output, "# 5. Status: Use Available, Active, Inactive, Under Maintenance, Issue Reported, Pending Confirmation, Disposed\n");
        fwrite($output, "# 6. Movement: Use New Arrival, Transfer, Return, Disposal\n");
        fwrite($output, "# 7. Entity: Use MIDC, Philtower, PRIMUS\n");
        fwrite($output, "# 8. Department names must match exactly (case-sensitive)\n");
        fwrite($output, "# 9. Email addresses must be valid format\n");
        fwrite($output, "# \n");
        
        // Add field descriptions
        fwrite($output, "# FIELD DESCRIPTIONS:\n");
        foreach ($templateData['headers'] as $key => $field) {
            $required = $field['required'] ? ' *REQUIRED*' : ' (Optional)';
            fwrite($output, "# {$field['name']}: {$field['description']}{$required}\n");
            if (isset($field['options'])) {
                // Split long option lists into multiple lines to prevent truncation
                $options = $field['options'];
                if (count($options) > 6) {
                    $chunks = array_chunk($options, 6);
                    foreach ($chunks as $index => $chunk) {
                        $prefix = $index === 0 ? "#   Valid values: " : "#   ";
                        fwrite($output, $prefix . implode(', ', $chunk) . "\n");
                    }
                } else {
                    fwrite($output, "#   Valid values: " . implode(', ', $options) . "\n");
                }
            }
            if (isset($field['max_length'])) {
                fwrite($output, "#   Max length: {$field['max_length']} characters\n");
            }
        }
        fwrite($output, "# \n");
        
        // Add valid values reference if available
        if (isset($templateData['auto_populated_data'])) {
            fwrite($output, "# VALID VALUES REFERENCE:\n");
            foreach ($templateData['auto_populated_data'] as $type => $values) {
                if (!empty($values)) {
                    fwrite($output, "# Valid {$type}: " . implode(', ', array_slice($values, 0, 10)) . "\n");
                }
            }
            fwrite($output, "# \n");
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
            'users' => 'EMP001,John,Doe,john.doe@company.com,IT Department,Software Developer,Admin,Active',
            'assets' => 'AST001,Dell OptiPlex 7090,Computer Hardware,Dell Inc,Desktop Computer,ABC123456,2024-01-15,2027-01-15,50000.00,Available',
            'computers' => 'AST001,Dell OptiPlex 7090,Intel Core i7-11700,16GB DDR4,512GB SSD,Windows 11 Pro,RTX 3060,Desktop',
            'monitors' => 'MON001,Samsung 24" Monitor,24",1920x1080,IPS,HDMI,USB-C',
            'printers' => 'PRT001,HP LaserJet Pro,Laser,1,1,Network',
            'peripherals' => 'PER001,Wireless Mouse,Mouse,Wireless,USB,Optical',
            'departments' => 'Information Technology,IT Department managing all technology assets,admin@company.com',
            'vendors' => 'Dell Technologies,John Smith,sales@dell.com,+1-800-DELL,Round Rock TX USA',
            'asset_categories' => 'Computer Hardware,Desktop and laptop computers,COMP001,20,5,Active'
        ];

        return $samples[$module] ?? '';
    }

    /**
     * Import data from CSV file with comprehensive validation
     */
    public function import(Request $request, $module)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240', // Support Excel and CSV
            'validate_only' => 'nullable|in:true,false,1,0', // Option to validate without importing
            'partial_import' => 'nullable|in:true,false,1,0' // Option for partial import (skip invalid records)
        ]);

        $file = $request->file('file');
        $validateOnly = $request->boolean('validate_only', false);
        $partialImport = $request->boolean('partial_import', false);
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
        
        // Read file based on type
        try {
            $fileExtension = strtolower($file->getClientOriginalExtension());
            
            if (in_array($fileExtension, ['xlsx', 'xls'])) {
                // Handle Excel files
                $csvData = $this->readExcelFile($path);
            } else {
                // Handle CSV files
                $csvData = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            }
            
            if ($csvData === false || empty($csvData)) {
                return redirect()->route('import-export.results')
                    ->with('import_errors', [[
                        'row' => 1,
                        'field' => 'file',
                        'message' => 'File is empty or could not be read.',
                        'value' => ''
                    ]]);
            }
            
            // Filter out comment lines and instruction lines
            $csvData = array_filter($csvData, function($line) {
                $trimmed = trim($line);
                return !empty($trimmed) && 
                       !str_starts_with($trimmed, '#') && 
                       !str_starts_with($trimmed, 'IMPORT TEMPLATE') &&
                       !str_starts_with($trimmed, 'Template Version') &&
                       !str_starts_with($trimmed, 'Generated:') &&
                       !str_starts_with($trimmed, 'IMPORTANT INSTRUCTIONS') &&
                       !str_starts_with($trimmed, 'FIELD DESCRIPTIONS') &&
                       !str_starts_with($trimmed, 'VALID VALUES REFERENCE') &&
                       !str_starts_with($trimmed, 'DO NOT DELETE') &&
                       !str_starts_with($trimmed, 'Fill in your data') &&
                       !str_starts_with($trimmed, 'Required fields') &&
                       !str_starts_with($trimmed, 'Use EXACT values') &&
                       !str_starts_with($trimmed, 'Status fields') &&
                       !str_starts_with($trimmed, 'Department names') &&
                       !str_starts_with($trimmed, 'Email addresses') &&
                       !str_starts_with($trimmed, 'END OF DATA') &&
                       !str_starts_with($trimmed, '=====');
            });
            
            // Reindex array after filtering
            $csvData = array_values($csvData);
            
            if (empty($csvData)) {
                return redirect()->route('import-export.results')
                    ->with('import_errors', [[
                        'row' => 1,
                        'field' => 'file',
                        'message' => 'No valid data found after filtering comments.',
                        'value' => ''
                    ]]);
            }
        } catch (\Exception $e) {
            return redirect()->route('import-export.results')
                ->with('import_errors', [[
                    'row' => 1,
                    'field' => 'file',
                    'message' => 'Error reading file: ' . $e->getMessage(),
                    'value' => ''
                ]]);
        }

        // Parse CSV data with error handling
        try {
            $data = [];
            foreach ($csvData as $lineNumber => $line) {
                // Use a more robust CSV parsing approach
                $parsedLine = str_getcsv($line, ',', '"', '\\');
                if ($parsedLine === false || empty($parsedLine)) {
                    continue; // Skip empty lines
                }
                
                // Skip lines that are all empty or contain only instruction text
                $nonEmptyFields = array_filter($parsedLine, function($field) { 
                    $trimmed = trim($field);
                    return !empty($trimmed) && 
                           !str_starts_with($trimmed, '#') &&
                           !str_starts_with($trimmed, 'IMPORT TEMPLATE') &&
                           !str_starts_with($trimmed, 'Template Version') &&
                           !str_starts_with($trimmed, 'Generated:') &&
                           !str_starts_with($trimmed, 'IMPORTANT INSTRUCTIONS') &&
                           !str_starts_with($trimmed, 'FIELD DESCRIPTIONS') &&
                           !str_starts_with($trimmed, 'VALID VALUES REFERENCE');
                });
                
                if (empty($nonEmptyFields)) {
                    continue; // Skip empty lines and instruction lines
                }
                
                // Additional check: Skip rows that are completely empty (all fields are empty strings)
                $hasAnyData = false;
                foreach ($parsedLine as $field) {
                    if (!empty(trim($field))) {
                        $hasAnyData = true;
                        break;
                    }
                }
                
                if (!$hasAnyData) {
                    continue; // Skip completely empty rows
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
        
        // Map display names back to field keys for processing
        $fieldKeys = $this->mapDisplayNamesToKeys($module, $headers);
        
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
            $rowData = array_combine($fieldKeys, array_pad($row, count($fieldKeys), ''));
            
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
                $rowData = array_combine($fieldKeys, array_pad($row, count($fieldKeys), ''));
                
                // Skip empty rows - check if all fields are empty or whitespace
                $hasData = false;
                foreach ($row as $field) {
                    if (!empty(trim($field))) {
                        $hasData = true;
                        break;
                    }
                }
                
                if (!$hasData) {
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
            
            // Check if this is an AJAX request
            if (request()->ajax()) {
                return response()->json([
                    'success' => empty($allErrors),
                    'message' => 'Validation completed. ' . (empty($allErrors) ? 'No errors found.' : count($allErrors) . ' errors found.'),
                    'successful' => 0,
                    'failed' => count($allErrors),
                    'warnings' => count($warnings),
                    'error_details' => $allErrors,
                    'warning_details' => $warnings,
                    'summary' => $summary
                ]);
            }
            
            return redirect()->route('import-export.results')
                ->with('import_errors', $allErrors)
                ->with('import_warnings', $warnings)
                ->with('import_summary', $summary)
                ->with('validation_message', 'Validation completed. ' . (empty($allErrors) ? 'No errors found.' : count($allErrors) . ' errors found.'));
        }

        // Check for duplicates before proceeding with import
        if (!empty($duplicates)) {
            $summary = [
                'total' => $totalRows,
                'successful' => 0,
                'failed' => count($duplicates),
                'warnings' => 0
            ];
            
            // Check if this is an AJAX request
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Duplicate entries found. Please fix duplicates before importing.',
                    'successful' => 0,
                    'failed' => count($duplicates),
                    'warnings' => 0,
                    'error_details' => $duplicates,
                    'summary' => $summary
                ]);
            }
            
            return redirect()->route('import-export.results')
                ->with('import_errors', $duplicates)
                ->with('import_summary', $summary);
        }

        // PHASE 1: VALIDATE ALL RECORDS
        Log::info('Import validation started', [
            'module' => $module,
            'user_id' => auth()->id(),
            'total_rows' => $totalRows,
            'filename' => $file->getClientOriginalName(),
            'partial_import' => $partialImport
        ]);

        $validationErrors = [];
        $validRows = [];
        
        foreach ($data as $index => $row) {
            $rowNumber = $index + 2; // +2 because we removed headers and arrays are 0-indexed
            
            // Skip empty rows - check if all fields are empty or whitespace
            $hasData = false;
            foreach ($row as $field) {
                if (!empty(trim($field))) {
                    $hasData = true;
                    break;
                }
            }
            
            if (!$hasData) {
                $warnings[] = [
                    'row' => $rowNumber,
                    'message' => 'Empty row skipped',
                    'field' => 'general'
                ];
                continue;
            }
            
            $rowData = array_combine($fieldKeys, array_pad($row, count($fieldKeys), ''));
            
            // Validate each row WITHOUT importing
            try {
                $this->validateImportRow($module, $rowData, $rowNumber);
                $validRows[] = ['row' => $rowData, 'rowNumber' => $rowNumber];
            } catch (\Illuminate\Validation\ValidationException $e) {
                foreach ($e->errors() as $field => $messages) {
                    foreach ($messages as $message) {
                        $validationErrors[] = [
                            'row' => $rowNumber,
                            'field' => $field,
                            'message' => $message,
                            'value' => $rowData[$field] ?? ''
                        ];
                    }
                }
            } catch (\Exception $e) {
                $validationErrors[] = [
                    'row' => $rowNumber,
                    'field' => 'general',
                    'message' => $e->getMessage(),
                    'value' => ''
                ];
            }
        }

        // Handle validation errors based on import type
        if (!empty($validationErrors)) {
            if ($partialImport) {
                // Partial import: Continue with valid rows only
                Log::info('Partial import mode - proceeding with valid rows only', [
                    'module' => $module,
                    'user_id' => auth()->id(),
                    'valid_rows' => count($validRows),
                    'invalid_rows' => count($validationErrors),
                    'filename' => $file->getClientOriginalName()
                ]);
            } else {
                // Full import (fail-fast): Stop and return errors
                $summary = [
                    'total' => $totalRows,
                    'successful' => 0,
                    'failed' => count($validationErrors),
                    'warnings' => count($warnings)
                ];
                
                Log::error('Import validation failed - stopping import (fail-fast mode)', [
                    'module' => $module,
                    'user_id' => auth()->id(),
                    'validation_errors' => count($validationErrors),
                    'filename' => $file->getClientOriginalName()
                ]);
                
                // Check if this is an AJAX request
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed. Please fix all errors before importing. No records were imported.',
                        'successful' => 0,
                        'failed' => count($validationErrors),
                        'warnings' => count($warnings),
                        'error_details' => $validationErrors,
                        'summary' => $summary
                    ]);
                }
                
                return redirect()->route('import-export.results')
                    ->with('import_errors', $validationErrors)
                    ->with('import_summary', $summary);
            }
        }

        // PHASE 2: PROCEED WITH IMPORT
        if ($partialImport) {
            Log::info('Starting partial import', [
                'module' => $module,
                'user_id' => auth()->id(),
                'valid_rows' => count($validRows),
                'invalid_rows' => count($validationErrors),
                'filename' => $file->getClientOriginalName()
            ]);
        } else {
            Log::info('All validations passed - starting full import', [
                'module' => $module,
                'user_id' => auth()->id(),
                'valid_rows' => count($validRows),
                'filename' => $file->getClientOriginalName()
            ]);
        }

        try {
            // Import all valid rows in a single transaction
            DB::beginTransaction();
            
            $actualSuccessCount = 0;
            $importErrors = [];
            
            foreach ($validRows as $validRow) {
                try {
                    $this->processImportRow($module, $validRow['row'], $validRow['rowNumber']);
                    $actualSuccessCount++;
                } catch (\Exception $e) {
                    // In partial import mode, log the error but continue with next record
                    if ($partialImport) {
                        $importErrors[] = [
                            'row' => $validRow['rowNumber'],
                            'field' => 'import',
                            'message' => 'Import failed: ' . $e->getMessage(),
                            'value' => ''
                        ];
                        Log::warning('Record import failed in partial import mode', [
                            'module' => $module,
                            'row' => $validRow['rowNumber'],
                            'error' => $e->getMessage(),
                            'data' => $validRow['row']
                        ]);
                        continue;
                    } else {
                        // In full import mode, re-throw the exception to stop the entire import
                        throw $e;
                    }
                }
            }
            
            DB::commit();
            
            // Update success count with actual successful imports
            $successCount = $actualSuccessCount;
            
            // Prepare response data for successful import
            $totalFailed = count($validationErrors) + count($importErrors);
            
            Log::info('Import completed successfully', [
                'module' => $module,
                'user_id' => auth()->id(),
                'imported_records' => $successCount,
                'validation_errors' => count($validationErrors),
                'import_errors' => count($importErrors),
                'total_failed' => $totalFailed,
                'import_type' => $partialImport ? 'partial' : 'full',
                'filename' => $file->getClientOriginalName()
            ]);
            $summary = [
                'total' => $totalRows,
                'successful' => $successCount,
                'failed' => $totalFailed,
                'warnings' => count($warnings)
            ];

            // Prepare success message based on import type
            if ($partialImport) {
                $message = "Partial import completed! Successfully imported {$successCount} valid {$module} records.";
                if (count($validationErrors) > 0) {
                    $message .= " " . count($validationErrors) . " records failed validation and were skipped.";
                }
                if (count($importErrors) > 0) {
                    $message .= " " . count($importErrors) . " records failed during import and were skipped.";
                }
            } else {
                $message = "Successfully imported {$successCount} {$module} records. All validations passed.";
            }

            // Check if this is an AJAX request
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'successful' => $successCount,
                    'failed' => $totalFailed,
                    'warnings' => count($warnings),
                    'error_details' => array_merge($validationErrors, $importErrors),
                    'warning_details' => $warnings,
                    'summary' => $summary
                ]);
            }

            // Non-AJAX request handling for successful import
            $successMessage = $message;
            if (count($warnings) > 0) {
                $successMessage .= " {$summary['warnings']} warnings generated.";
            }

            return redirect()->route('import-export.results')
                ->with('import_success', $successMessage)
                ->with('import_warnings', $warnings)
                ->with('import_summary', $summary);

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Import exception during processing', [
                'module' => $module,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $errorData = [[
                'row' => 0,
                'field' => 'general',
                'message' => 'Import processing failed: ' . $e->getMessage(),
                'value' => ''
            ]];
            
            // Check if this is an AJAX request
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Import processing failed: ' . $e->getMessage(),
                    'imported' => 0,
                    'errors' => 1,
                    'warnings' => 0,
                    'error_details' => $errorData,
                    'summary' => [
                        'total' => 0,
                        'successful' => 0,
                        'failed' => 1,
                        'warnings' => 0
                    ]
                ], 500);
            }
            
            return redirect()->route('import-export.results')
                ->with('import_errors', $errorData);
        }
    }

    /**
     * Get required fields for module validation
     */
    private function getRequiredFields($module)
    {
        // Get the template headers to extract display names for required fields
        $templateService = app(TemplateGenerationService::class);
        $templateData = $templateService->generateTemplate($module);
        
        $requiredFields = [];
        foreach ($templateData['headers'] as $key => $field) {
            if ($field['required']) {
                $requiredFields[] = $field['name'];
            }
        }

        return $requiredFields;
    }

    /**
     * Map display names back to field keys for data processing
     */
    private function mapDisplayNamesToKeys($module, $headers)
    {
        $templateService = app(TemplateGenerationService::class);
        $templateData = $templateService->generateTemplate($module);
        
        $nameToKeyMap = [];
        foreach ($templateData['headers'] as $key => $field) {
            $nameToKeyMap[$field['name']] = $key;
        }
        
        return array_map(function($header) use ($nameToKeyMap) {
            return $nameToKeyMap[$header] ?? $header;
        }, $headers);
    }

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
     * Find the closest match from a list of options (for suggestions)
     */
    private function findClosestMatch($input, $options)
    {
        if (empty($input) || empty($options)) {
            return null;
        }
        
        $input = strtolower(trim($input));
        $bestMatch = null;
        $bestScore = 0;
        
        foreach ($options as $option) {
            $option = strtolower(trim($option));
            
            // Exact match
            if ($input === $option) {
                return $option;
            }
            
            // Check if input is contained in option or vice versa
            if (strpos($option, $input) !== false || strpos($input, $option) !== false) {
                $score = max(strlen($input), strlen($option)) / max(strlen($input), strlen($option));
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestMatch = $option;
                }
            }
            
            // Calculate similarity using similar_text
            similar_text($input, $option, $percent);
            if ($percent > 60 && $percent > $bestScore * 100) {
                $bestScore = $percent / 100;
                $bestMatch = $option;
            }
        }
        
        return $bestMatch;
    }

    /**
     * Generate import preview data
     */
    public function generateImportPreview(Request $request, $module)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();
        
        try {
            // Read and parse file
            $fileExtension = strtolower($file->getClientOriginalExtension());
            
            if (in_array($fileExtension, ['xlsx', 'xls'])) {
                $csvData = $this->readExcelFile($path);
            } else {
                $csvData = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            }
            
            // Filter out comment lines and instruction lines
            $csvData = array_filter($csvData, function($line) {
                $trimmed = trim($line);
                return !empty($trimmed) && 
                       !str_starts_with($trimmed, '#') && 
                       !str_starts_with($trimmed, 'IMPORT TEMPLATE') &&
                       !str_starts_with($trimmed, 'Template Version') &&
                       !str_starts_with($trimmed, 'Generated:') &&
                       !str_starts_with($trimmed, 'IMPORTANT INSTRUCTIONS') &&
                       !str_starts_with($trimmed, 'FIELD DESCRIPTIONS') &&
                       !str_starts_with($trimmed, 'VALID VALUES REFERENCE') &&
                       !str_starts_with($trimmed, 'DO NOT DELETE') &&
                       !str_starts_with($trimmed, 'Fill in your data') &&
                       !str_starts_with($trimmed, 'Required fields') &&
                       !str_starts_with($trimmed, 'Use EXACT values') &&
                       !str_starts_with($trimmed, 'Status fields') &&
                       !str_starts_with($trimmed, 'Department names') &&
                       !str_starts_with($trimmed, 'Email addresses') &&
                       !str_starts_with($trimmed, 'END OF DATA') &&
                       !str_starts_with($trimmed, '=====');
            });
            $csvData = array_values($csvData);
            
            if (empty($csvData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid data found in file'
                ]);
            }
            
            // Parse CSV data
            $data = [];
            foreach ($csvData as $line) {
                $parsedLine = str_getcsv($line);
                if ($parsedLine !== false) {
                    $data[] = $parsedLine;
                }
            }
            
            $headers = array_shift($data);
            $headers = array_map(function($header) {
                return str_replace(["\xEF\xBB\xBF", "\xFF\xFE", "\xFE\xFF"], '', trim($header));
            }, $headers);
            
            // Map display names back to field keys for processing
            $fieldKeys = $this->mapDisplayNamesToKeys($module, $headers);
            
            // Generate preview data
            $previewData = [];
            $errors = [];
            $warnings = [];
            
            foreach ($data as $index => $row) {
                $rowNumber = $index + 2;
                $rowData = array_combine($fieldKeys, array_pad($row, count($fieldKeys), ''));
                
                // Skip empty rows - check if all fields are empty or whitespace
                $hasData = false;
                foreach ($row as $field) {
                    if (!empty(trim($field))) {
                        $hasData = true;
                        break;
                    }
                }
                
                if (!$hasData) {
                    $warnings[] = [
                        'row' => $rowNumber,
                        'message' => 'Empty row will be skipped',
                        'field' => 'general'
                    ];
                    continue;
                }
                
                // Validate row and generate preview
                try {
                    $previewRow = $this->generateRowPreview($module, $rowData, $rowNumber);
                    $previewData[] = $previewRow;
                } catch (\Exception $e) {
                    $errors[] = [
                        'row' => $rowNumber,
                        'field' => 'general',
                        'message' => $e->getMessage(),
                        'value' => implode(', ', array_slice($row, 0, 3))
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'preview_data' => $previewData,
                'headers' => $headers,
                'errors' => $errors,
                'warnings' => $warnings,
                'summary' => [
                    'total_rows' => count($data),
                    'valid_rows' => count($previewData),
                    'errors' => count($errors),
                    'warnings' => count($warnings)
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing file: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate preview data for a single row
     */
    private function generateRowPreview($module, $data, $rowNumber)
    {
        $preview = [
            'row_number' => $rowNumber,
            'original_data' => $data,
            'processed_data' => [],
            'status' => 'valid',
            'warnings' => [],
            'errors' => []
        ];
        
        try {
            switch ($module) {
                case 'users':
                    $preview['processed_data'] = $this->generateUserPreview($data);
                    break;
                case 'assets':
                    $preview['processed_data'] = $this->generateAssetPreview($data);
                    break;
                case 'computers':
                    $preview['processed_data'] = $this->generateComputerPreview($data);
                    break;
                case 'monitors':
                    $preview['processed_data'] = $this->generateMonitorPreview($data);
                    break;
                case 'printers':
                    $preview['processed_data'] = $this->generatePrinterPreview($data);
                    break;
                case 'peripherals':
                    $preview['processed_data'] = $this->generatePeripheralPreview($data);
                    break;
                case 'departments':
                    $preview['processed_data'] = $this->generateDepartmentPreview($data);
                    break;
                case 'vendors':
                    $preview['processed_data'] = $this->generateVendorPreview($data);
                    break;
                case 'asset_categories':
                    $preview['processed_data'] = $this->generateAssetCategoryPreview($data);
                    break;
                default:
                    throw new \Exception('Invalid module specified.');
            }
        } catch (\Exception $e) {
            $preview['status'] = 'error';
            $preview['errors'][] = $e->getMessage();
        }
        
        return $preview;
    }

    /**
     * Generate user preview data
     */
    private function generateUserPreview($data)
    {
        $preview = [];
        
        // Basic field mapping
        $preview['employee_id'] = $data['employee_id'] ?? '';
        $preview['first_name'] = $data['first_name'] ?? '';
        $preview['last_name'] = $data['last_name'] ?? '';
        $preview['email'] = $data['email_address'] ?? '';
        $preview['phone'] = $data['phone_number'] ?? '';
        $preview['job_title'] = $data['job_title'] ?? '';
        $preview['company'] = $data['company'] ?? '';
        $preview['status'] = $data['status'] ?? '1';
        $preview['password'] = '***HIDDEN***';
        
        // Department lookup
        if (!empty($data['department'])) {
            $department = Department::where('name', $data['department'])->first();
            if ($department) {
                $preview['department'] = $department->name;
                $preview['department_id'] = $department->id;
            } else {
                $preview['department'] = $data['department'] . ' ❌ NOT FOUND';
                $preview['department_id'] = null;
            }
        }
        
        // Role lookup
        if (!empty($data['role'])) {
            $role = Role::where('name', $data['role'])->first();
            if ($role) {
                $preview['role'] = $role->name;
                $preview['role_id'] = $role->id;
            } else {
                $preview['role'] = $data['role'] . ' ❌ NOT FOUND';
                $preview['role_id'] = null;
            }
        }
        
        // Check for duplicates
        if (!empty($data['employee_id'])) {
            $existingUser = User::where('employee_id', $data['employee_id'])->first();
            if ($existingUser) {
                $preview['duplicate_employee_id'] = '⚠️ Employee ID already exists';
            }
        }
        
        if (!empty($data['email_address'])) {
            $existingUser = User::where('email', $data['email_address'])->first();
            if ($existingUser) {
                $preview['duplicate_email'] = '⚠️ Email already exists';
            }
        }
        
        return $preview;
    }

    /**
     * Generate asset preview data
     */
    private function generateAssetPreview($data)
    {
        $preview = [];
        
        $preview['asset_tag'] = $data['asset_tag'] ?? 'AUTO-GENERATED';
        $preview['asset_name'] = $data['asset_name'] ?? '';
        $preview['model'] = $data['model'] ?? '';
        $preview['serial_number'] = $data['serial_number'] ?? '';
        $preview['purchase_date'] = $data['purchase_date'] ?? '';
        $preview['purchase_cost'] = $data['purchase_cost'] ?? '';
        $preview['po_number'] = $data['po_number'] ?? '';
        $preview['entity'] = $data['entity'] ?? '';
        $preview['lifespan'] = $data['lifespan'] ?? '';
        $preview['location'] = $data['location'] ?? '';
        $preview['notes'] = $data['notes'] ?? '';
        $preview['status'] = $data['status'] ?? 'Available';
        $preview['movement'] = $data['movement'] ?? 'New Arrival';
        
        // Category lookup
        if (!empty($data['category'])) {
            $category = AssetCategory::where('name', $data['category'])->first();
            if ($category) {
                $preview['category'] = $category->name;
                $preview['category_id'] = $category->id;
            } else {
                $preview['category'] = $data['category'] . ' ❌ NOT FOUND';
                $preview['category_id'] = null;
            }
        }
        
        // Vendor lookup
        if (!empty($data['vendor'])) {
            $vendor = Vendor::where('name', $data['vendor'])->first();
            if ($vendor) {
                $preview['vendor'] = $vendor->name;
                $preview['vendor_id'] = $vendor->id;
            } else {
                $preview['vendor'] = $data['vendor'] . ' ❌ NOT FOUND';
                $preview['vendor_id'] = null;
            }
        }
        
        return $preview;
    }

    /**
     * Generate other module previews (simplified)
     */
    private function generateComputerPreview($data) { return $this->generateAssetPreview($data); }
    private function generateMonitorPreview($data) { return $this->generateAssetPreview($data); }
    private function generatePrinterPreview($data) { return $this->generateAssetPreview($data); }
    private function generatePeripheralPreview($data) { return $this->generateAssetPreview($data); }
    private function generateDepartmentPreview($data) { return $data; }
    private function generateVendorPreview($data) { return $data; }
    private function generateAssetCategoryPreview($data) { return $data; }

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
     * Read Excel file and convert to array
     */
    private function readExcelFile($path)
    {
        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($path);
            $worksheet = $spreadsheet->getActiveSheet();
            $data = [];
            
            foreach ($worksheet->getRowIterator() as $row) {
                $rowData = [];
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getCalculatedValue();
                }
                
                // Skip empty rows
                if (!empty(array_filter($rowData))) {
                    $data[] = implode(',', $rowData);
                }
            }
            
            return $data;
        } catch (\Exception $e) {
            throw new \Exception('Error reading Excel file: ' . $e->getMessage());
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
                throw new \Exception("❌ DUPLICATE EMPLOYEE ID: Employee ID '{$data['employee_id']}' already exists in the system. Please use a unique employee ID or update the existing record.");
            }
        }
        
        if (!empty($data['email_address'])) {
            $existingUser = User::where('email', $data['email_address'])->first();
            if ($existingUser) {
                throw new \Exception("❌ DUPLICATE EMAIL: Email address '{$data['email_address']}' already exists in the system. Please use a unique email address or update the existing record.");
            }
        }

        // Enhanced validation with specific error messages
        $validator = Validator::make($data, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email_address' => 'required|email|max:255',
            'employee_id' => 'required|string|max:255',
            'department' => 'required|string',
            'phone_number' => 'nullable|string|max:50',
            'status' => 'nullable|integer|in:0,1',
            'role' => 'nullable|string',
            'role_id' => 'nullable|integer|exists:roles,id',
            'company' => 'nullable|string|in:Philtower,MIDC,PRIMUS',
            'job_title' => 'nullable|string|max:255'
        ], [
            'first_name.required' => '❌ MISSING FIRST NAME: First name is required. Please provide the employee\'s first name.',
            'first_name.string' => '❌ INVALID FIRST NAME: First name must be text. Please enter a valid first name.',
            'first_name.max' => '❌ FIRST NAME TOO LONG: First name cannot exceed 255 characters. Please shorten the name.',
            
            'last_name.required' => '❌ MISSING LAST NAME: Last name is required. Please provide the employee\'s last name.',
            'last_name.string' => '❌ INVALID LAST NAME: Last name must be text. Please enter a valid last name.',
            'last_name.max' => '❌ LAST NAME TOO LONG: Last name cannot exceed 255 characters. Please shorten the name.',
            
            'email_address.required' => '❌ MISSING EMAIL: Email address is required. Please provide the employee\'s email address.',
            'email_address.email' => '❌ INVALID EMAIL FORMAT: Email address format is invalid. Please use a valid email format (e.g., john.doe@company.com).',
            'email_address.max' => '❌ EMAIL TOO LONG: Email address cannot exceed 255 characters. Please use a shorter email address.',
            
            'employee_id.required' => '❌ MISSING EMPLOYEE ID: Employee ID is required. Please provide a unique employee ID.',
            'employee_id.string' => '❌ INVALID EMPLOYEE ID: Employee ID must be text. Please enter a valid employee ID.',
            'employee_id.max' => '❌ EMPLOYEE ID TOO LONG: Employee ID cannot exceed 255 characters. Please use a shorter ID.',
            
            'department.required' => '❌ MISSING DEPARTMENT: Department is required. Please specify the employee\'s department.',
            'department.string' => '❌ INVALID DEPARTMENT: Department must be text. Please enter a valid department name.',
            
            'phone_number.string' => '❌ INVALID PHONE: Phone number must be text. Please enter a valid phone number.',
            'phone_number.max' => '❌ PHONE TOO LONG: Phone number cannot exceed 50 characters. Please use a shorter phone number.',
            
            'status.in' => '❌ INVALID STATUS: Status must be 1 (Active) or 0 (Inactive). Please use a valid status number.',
            
            'role.string' => '❌ INVALID ROLE: Role must be text. Please enter a valid role name.',
            
            'role_id.integer' => '❌ INVALID ROLE ID: Role ID must be a number. Please enter a valid role ID.',
            'role_id.exists' => '❌ ROLE NOT FOUND: Role ID does not exist in the system. Please use a valid role ID (1-5).',
            
            'company.in' => '❌ INVALID COMPANY: Company must be one of: Philtower, MIDC, PRIMUS. Please use a valid company name.',
            
            'job_title.string' => '❌ INVALID JOB TITLE: Job title must be text. Please enter a valid job title.',
            'job_title.max' => '❌ JOB TITLE TOO LONG: Job title cannot exceed 255 characters. Please shorten the job title.'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Find department by name with enhanced error message
        $department = Department::where('name', $data['department'])->first();
        if (!$department) {
            $availableDepartments = Department::pluck('name')->take(10)->toArray();
            $suggestion = $this->findClosestMatch($data['department'], Department::pluck('name')->toArray());
            
            $errorMessage = "❌ DEPARTMENT NOT FOUND: Department '{$data['department']}' does not exist in the system.\n\n";
            $errorMessage .= "📋 AVAILABLE DEPARTMENTS (first 10):\n";
            foreach ($availableDepartments as $dept) {
                $errorMessage .= "   • {$dept}\n";
            }
            
            if ($suggestion) {
                $errorMessage .= "\n💡 SUGGESTION: Did you mean '{$suggestion}'?\n";
            }
            
            $errorMessage .= "\n🔧 ACTION REQUIRED: Please use one of the exact department names listed above (case-sensitive).";
            
            throw new \Exception($errorMessage);
        }

        // Find role by ID or name with enhanced error message
        $role = null;
        $roleId = null;
        
        // Priority 1: Use role_id if provided
        if (!empty($data['role_id'])) {
            $role = Role::find($data['role_id']);
            if (!$role) {
                $availableRoles = Role::pluck('name', 'id')->toArray();
                $errorMessage = "❌ ROLE ID NOT FOUND: Role ID '{$data['role_id']}' does not exist in the system.\n\n";
                $errorMessage .= "📋 AVAILABLE ROLE IDS:\n";
                foreach ($availableRoles as $id => $name) {
                    $errorMessage .= "   • {$id} - {$name}\n";
                }
                $errorMessage .= "\n🔧 ACTION REQUIRED: Please use one of the exact role IDs listed above.";
                throw new \Exception($errorMessage);
            }
            $roleId = $role->id;
        }
        // Priority 2: Use role name if provided and no role_id
        elseif (!empty($data['role'])) {
            $role = Role::where('name', $data['role'])->first();
            if (!$role) {
                $availableRoles = Role::pluck('name')->toArray();
                $suggestion = $this->findClosestMatch($data['role'], $availableRoles);
                
                $errorMessage = "❌ ROLE NOT FOUND: Role '{$data['role']}' does not exist in the system.\n\n";
                $errorMessage .= "📋 AVAILABLE ROLES:\n";
                foreach ($availableRoles as $roleName) {
                    $errorMessage .= "   • {$roleName}\n";
                }
                
                if ($suggestion) {
                    $errorMessage .= "\n💡 SUGGESTION: Did you mean '{$suggestion}'?\n";
                }
                
                $errorMessage .= "\n🔧 ACTION REQUIRED: Please use one of the exact role names listed above (case-sensitive).";
                
                throw new \Exception($errorMessage);
            }
            $roleId = $role->id;
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
            'role_id' => $roleId,
            'status' => $data['status'] ?? 1,
            'password' => bcrypt($data['password'] ?? 'password123')
        ]);
    }

    /**
     * Import asset data
     */
    private function importAsset($data, $rowNumber)
    {
        // Enhanced validation with specific error messages
        $validator = Validator::make($data, [
            'asset_tag' => 'nullable|string|max:255',
            'asset_name' => 'required|string|max:255',
            'category' => 'required|string',
            'vendor' => 'required|string',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:Available,Active,Inactive,Under Maintenance,Issue Reported,Pending Confirmation,Disposed',
            'movement' => 'nullable|string|in:New Arrival,Transfer,Return,Disposal',
            'entity' => 'nullable|string|in:MIDC,Philtower,PRIMUS',
            'serial_number' => 'nullable|string|max:100',
            'lifespan' => 'nullable|integer|min:1',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ], [
            'asset_name.required' => '❌ MISSING ASSET NAME: Asset name is required. Please provide a descriptive name for the asset.',
            'asset_name.string' => '❌ INVALID ASSET NAME: Asset name must be text. Please enter a valid asset name.',
            'asset_name.max' => '❌ ASSET NAME TOO LONG: Asset name cannot exceed 255 characters. Please shorten the name.',
            
            'category.required' => '❌ MISSING CATEGORY: Category is required. Please specify the asset category.',
            'category.string' => '❌ INVALID CATEGORY: Category must be text. Please enter a valid category name.',
            
            'vendor.required' => '❌ MISSING VENDOR: Vendor is required. Please specify the vendor/supplier.',
            'vendor.string' => '❌ INVALID VENDOR: Vendor must be text. Please enter a valid vendor name.',
            
            'asset_tag.string' => '❌ INVALID ASSET TAG: Asset tag must be text. Please enter a valid asset tag.',
            'asset_tag.max' => '❌ ASSET TAG TOO LONG: Asset tag cannot exceed 255 characters. Please shorten the tag.',
            
            'purchase_date.date' => '❌ INVALID PURCHASE DATE: Purchase date must be a valid date. Please use format YYYY-MM-DD (e.g., 2024-01-15).',
            
            'purchase_cost.numeric' => '❌ INVALID PURCHASE COST: Purchase cost must be a number. Please enter a valid cost amount.',
            'purchase_cost.min' => '❌ INVALID PURCHASE COST: Purchase cost cannot be negative. Please enter a positive number.',
            
            'status.in' => '❌ INVALID STATUS: Status must be one of: Available, Active, Inactive, Under Maintenance, Issue Reported, Pending Confirmation, Disposed. Please use a valid status.',
            
            'movement.in' => '❌ INVALID MOVEMENT: Movement must be one of: New Arrival, Transfer, Return, Disposal. Please use a valid movement type.',
            
            'entity.in' => '❌ INVALID ENTITY: Entity must be one of: MIDC, Philtower, PRIMUS. Please use a valid entity name.',
            
            'serial_number.string' => '❌ INVALID SERIAL NUMBER: Serial number must be text. Please enter a valid serial number.',
            'serial_number.max' => '❌ SERIAL NUMBER TOO LONG: Serial number cannot exceed 100 characters. Please shorten the serial number.',
            
            'lifespan.integer' => '❌ INVALID LIFESPAN: Lifespan must be a whole number. Please enter a valid number of years.',
            'lifespan.min' => '❌ INVALID LIFESPAN: Lifespan must be at least 1 year. Please enter a valid number.',
            
            'location.string' => '❌ INVALID LOCATION: Location must be text. Please enter a valid location.',
            'location.max' => '❌ LOCATION TOO LONG: Location cannot exceed 255 characters. Please shorten the location.',
            
            'notes.string' => '❌ INVALID NOTES: Notes must be text. Please enter valid notes.'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Find category by name with enhanced error message
        $category = AssetCategory::where('name', $data['category'])->first();
        if (!$category) {
            $availableCategories = AssetCategory::pluck('name')->take(10)->toArray();
            $suggestion = $this->findClosestMatch($data['category'], AssetCategory::pluck('name')->toArray());
            
            $errorMessage = "❌ CATEGORY NOT FOUND: Category '{$data['category']}' does not exist in the system.\n\n";
            $errorMessage .= "📋 AVAILABLE CATEGORIES (first 10):\n";
            foreach ($availableCategories as $cat) {
                $errorMessage .= "   • {$cat}\n";
            }
            
            if ($suggestion) {
                $errorMessage .= "\n💡 SUGGESTION: Did you mean '{$suggestion}'?\n";
            }
            
            $errorMessage .= "\n🔧 ACTION REQUIRED: Please use one of the exact category names listed above (case-sensitive).";
            
            throw new \Exception($errorMessage);
        }

        // Find vendor by name with enhanced error message
        $vendor = Vendor::where('name', $data['vendor'])->first();
        if (!$vendor) {
            $availableVendors = Vendor::pluck('name')->take(10)->toArray();
            $suggestion = $this->findClosestMatch($data['vendor'], Vendor::pluck('name')->toArray());
            
            $errorMessage = "❌ VENDOR NOT FOUND: Vendor '{$data['vendor']}' does not exist in the system.\n\n";
            $errorMessage .= "📋 AVAILABLE VENDORS (first 10):\n";
            foreach ($availableVendors as $vend) {
                $errorMessage .= "   • {$vend}\n";
            }
            
            if ($suggestion) {
                $errorMessage .= "\n💡 SUGGESTION: Did you mean '{$suggestion}'?\n";
            }
            
            $errorMessage .= "\n🔧 ACTION REQUIRED: Please use one of the exact vendor names listed above (case-sensitive).";
            
            throw new \Exception($errorMessage);
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
        // Validate computer-specific fields
        $validator = Validator::make($data, [
            'asset_id' => 'required|integer|exists:assets,id|unique:computers,asset_id',
            'processor' => 'required|string|max:255',
            'memory_ram' => 'required|string|max:255',
            'storage' => 'required|string|max:255',
            'operating_system' => 'required|string|max:255',
            'graphics_card' => 'nullable|string|max:255',
            'computer_type' => 'required|in:Desktop,Laptop,Server,Workstation'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Verify asset exists and is available for computer creation
        $asset = Asset::find($data['asset_id']);
        if (!$asset) {
            throw new \Exception("Asset with ID {$data['asset_id']} not found.");
        }

        // Check if asset is already used for a computer
        if (Computer::where('asset_id', $data['asset_id'])->exists()) {
            throw new \Exception("Asset with ID {$data['asset_id']} is already used for a computer.");
        }

        // Check if asset is in Computer Hardware category
        $computerCategory = \App\Models\AssetCategory::where('name', 'Computer Hardware')->first();
        if (!$computerCategory || $asset->category_id !== $computerCategory->id) {
            throw new \Exception("Asset with ID {$data['asset_id']} is not in Computer Hardware category.");
        }

        // Create the computer record
        Computer::create([
            'asset_id' => $data['asset_id'],
            'processor' => $data['processor'],
            'memory' => $data['memory_ram'],
            'storage' => $data['storage'],
            'graphics_card' => $data['graphics_card'] ?? null,
            'computer_type' => $data['computer_type'],
            'operating_system' => $data['operating_system']
        ]);
    }

    /**
     * Import monitor data
     */
    private function importMonitor($data, $rowNumber)
    {
        // Create the monitor record using the existing asset ID (same as computers)
        Monitor::create([
            'asset_id' => $data['asset_id'],
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
        // Convert descriptive text to boolean values
        $colorSupport = $this->convertToBoolean($data['color_support']);
        $duplexPrinting = $this->convertToBoolean($data['duplex_printing']);

        // Create the printer record using the existing asset ID (same as computers and monitors)
        Printer::create([
            'asset_id' => $data['asset_id'],
            'type' => $data['printer_type'],
            'color_support' => $colorSupport,
            'duplex' => $duplexPrinting
        ]);
    }

    /**
     * Convert various text formats to boolean
     */
    private function convertToBoolean($value)
    {
        if (empty($value)) {
            return false;
        }

        $value = strtolower(trim($value));
        
        // Handle Yes/No values
        if (in_array($value, ['yes', '1', 'true'])) {
            return true;
        }
        
        if (in_array($value, ['no', '0', 'false'])) {
            return false;
        }
        
        // Handle legacy descriptive text (for backward compatibility)
        if (in_array($value, ['color printing', 'duplex support'])) {
            return true;
        }
        
        if (in_array($value, ['monochrome only', 'single-sided only'])) {
            return false;
        }
        
        // Default to false for unknown values
        return false;
    }

    /**
     * Import peripheral data
     */
    private function importPeripheral($data, $rowNumber)
    {
        // Create the peripheral record using the existing asset ID (same as computers, monitors, and printers)
        Peripheral::create([
            'asset_id' => $data['asset_id'],
            'type' => $data['type'],
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
                            'purchase_date' => $asset->purchase_date ? (is_string($asset->purchase_date) ? $asset->purchase_date : (method_exists($asset->purchase_date, 'format') ? $asset->purchase_date->format('Y-m-d') : $asset->purchase_date)) : '',
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
                            'memory_ram' => $computer->memory,
                            'storage' => $computer->storage,
                            'graphics_card' => $computer->graphics_card,
                            'computer_type' => $computer->computer_type,
                            'operating_system' => $computer->operating_system
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
            'role_id' => 'nullable|integer|exists:roles,id',
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
            // Accept both integer and text values, convert to integer
            $statusValue = $data['status'];
            
            // Handle string values (from CSV)
            if (is_string($statusValue)) {
                $statusValue = trim($statusValue);
                
                // Check if it's a numeric string
                if (is_numeric($statusValue)) {
                    $statusValue = (int) $statusValue;
                    if (!in_array($statusValue, [0, 1])) {
                        throw new \Exception("Invalid status '{$data['status']}'. Valid statuses: 0 (Inactive), 1 (Active)");
                    }
                    $data['status'] = $statusValue;
                } else {
                    // Handle text status values
                    $statusMap = [
                        'active' => 1,
                        'inactive' => 0,
                        'suspended' => 0, // Map to inactive for now
                        'terminated' => 0, // Map to inactive for now
                        'on leave' => 0, // Map to inactive for now
                    ];
                    
                    $statusValue = strtolower($statusValue);
                    if (isset($statusMap[$statusValue])) {
                        $data['status'] = $statusMap[$statusValue];
                    } else {
                        throw new \Exception("Invalid status '{$data['status']}'. Valid statuses: 0 (Inactive), 1 (Active), or text: Active, Inactive");
                    }
                }
            } else {
                // Validate integer status
                if (!in_array($statusValue, [0, 1])) {
                    throw new \Exception("Invalid status '{$data['status']}'. Valid statuses: 0 (Inactive), 1 (Active)");
                }
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
            $validStatuses = ['Available', 'Active', 'Inactive', 'Under Maintenance', 'Issue Reported', 'Pending Confirmation', 'Disposed'];
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
        // For computers, we validate the asset ID reference
        $validator = Validator::make($data, [
            'asset_id' => 'required|integer|exists:assets,id',
            'processor' => 'required|string|max:255',
            'memory_ram' => 'required|string|max:255',
            'storage' => 'required|string|max:255',
            'operating_system' => 'required|string|max:255',
            'graphics_card' => 'nullable|string|max:255',
            'computer_type' => 'required|in:Desktop,Laptop,Server,Workstation'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Check if asset exists and is available for computer creation
        $asset = Asset::find($data['asset_id']);
        if (!$asset) {
            throw new \Exception("Asset with ID {$data['asset_id']} not found.");
        }

        // Check if asset is already used for a computer
        if (Computer::where('asset_id', $data['asset_id'])->exists()) {
            throw new \Exception("Asset with ID {$data['asset_id']} is already used for a computer.");
        }

        // Check if asset is in Computer Hardware category
        $computerCategory = \App\Models\AssetCategory::where('name', 'Computer Hardware')->first();
        if (!$computerCategory || $asset->category_id !== $computerCategory->id) {
            throw new \Exception("Asset with ID {$data['asset_id']} is not in Computer Hardware category.");
        }

        // Validate memory format (e.g., "8GB", "16 GB", "16GB DDR4", "32GB DDR5", "16GB DDR4-3200", "32GB DDR5 ECC")
        if (!empty($data['memory_ram']) && !preg_match('/^\d+\s*(GB|MB|TB)(\s+(DDR[3-5]|LPDDR[3-5]|SDRAM|RAM|Unified Memory)(\s*[-]\s*\d+)?(\s+(ECC|Non-ECC))?)?$/i', trim($data['memory_ram']))) {
            throw new \Exception("Invalid memory format '{$data['memory_ram']}'. Use format like '8GB', '16 GB', '16GB DDR4', '32GB DDR5', '16GB DDR4-3200', or '32GB DDR5 ECC'.");
        }

        // Validate storage format (e.g., "500GB", "1TB", "512GB NVMe SSD", "1TB SATA SSD", "2TB HDD")
        if (!empty($data['storage']) && !preg_match('/^\d+\s*(GB|TB|MB)(\s+(NVMe\s+SSD|SATA\s+SSD|SSD|HDD|M\.2\s+SSD|PCIe\s+SSD|SAS\s+SSD|SAS\s+HDD|SATA\s+HDD|NVMe|M\.2|PCIe|SAS|SATA))?$/i', trim($data['storage']))) {
            throw new \Exception("Invalid storage format '{$data['storage']}'. Use format like '500GB', '1TB', '512GB NVMe SSD', '1TB SATA SSD', or '2TB HDD'.");
        }
    }

    private function validateMonitor($data, $rowNumber)
    {
        // For monitors, we validate the asset ID reference (same as computers)
        $validator = Validator::make($data, [
            'asset_id' => 'required|integer|exists:assets,id',
            'size' => 'required|string|max:255',
            'resolution' => 'required|string|max:255',
            'panel_type' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Check if monitor record already exists for this asset
        $existingMonitor = Monitor::where('asset_id', $data['asset_id'])->first();
        if ($existingMonitor) {
            throw new \Exception("Monitor record for asset ID '{$data['asset_id']}' already exists. Skipping duplicate entry.");
        }

        // Validate size format (e.g., "24"", "27"", "32"")
        if (!empty($data['size']) && !preg_match('/^\d+(\.\d+)?"?$/', trim($data['size']))) {
            throw new \Exception("Invalid size format '{$data['size']}'. Use format like '24\"' or '27.5\"'.");
        }

        // Validate resolution format - must match specific supported resolutions
        $validResolutions = [
            '1920x1080 (Full HD)',
            '2560x1440 (QHD)',
            '3840x2160 (4K UHD)',
            '1366x768 (HD)',
            '1680x1050 (WSXGA+)',
            '2560x1080 (UltraWide)',
            '3440x1440 (UltraWide QHD)'
        ];
        
        if (!empty($data['resolution']) && !in_array($data['resolution'], $validResolutions)) {
            throw new \Exception("Invalid resolution '{$data['resolution']}'. Valid resolutions: " . implode(', ', $validResolutions));
        }

        // Validate panel type - must match specific supported panel types
        $validPanelTypes = ['LCD', 'LED', 'OLED', 'CRT', 'Plasma', 'IPS'];
        
        if (!empty($data['panel_type']) && !in_array($data['panel_type'], $validPanelTypes)) {
            throw new \Exception("Invalid panel type '{$data['panel_type']}'. Valid panel types: " . implode(', ', $validPanelTypes));
        }
    }

    private function validatePrinter($data, $rowNumber)
    {
        // For printers, we validate the asset ID reference (same as computers and monitors)
        $validator = Validator::make($data, [
            'asset_id' => 'required|integer|exists:assets,id',
            'printer_type' => 'required|string|max:255',
            'color_support' => 'nullable|string|max:255',
            'duplex_printing' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Check if printer record already exists for this asset
        $existingPrinter = Printer::where('asset_id', $data['asset_id'])->first();
        if ($existingPrinter) {
            throw new \Exception("Printer record for asset ID '{$data['asset_id']}' already exists. Skipping duplicate entry.");
        }

        // Validate printer type
        $validPrinterTypes = ['Inkjet', 'Laser', 'Dot Matrix', 'Thermal', 'LED', '3D Printer'];
        if (!empty($data['printer_type']) && !in_array($data['printer_type'], $validPrinterTypes)) {
            throw new \Exception("Invalid printer type '{$data['printer_type']}'. Valid types: " . implode(', ', $validPrinterTypes));
        }

        // Validate color support field
        $validColorSupport = ['Yes', 'No', 'true', 'false', '1', '0', 'yes', 'no'];
        if (!empty($data['color_support']) && !in_array($data['color_support'], $validColorSupport) && !in_array(strtolower($data['color_support']), ['true', 'false', '1', '0', 'yes', 'no'])) {
            throw new \Exception("Invalid color support value '{$data['color_support']}'. Valid values: " . implode(', ', $validColorSupport));
        }

        // Validate duplex printing field
        $validDuplexPrinting = ['Yes', 'No', 'true', 'false', '1', '0', 'yes', 'no'];
        if (!empty($data['duplex_printing']) && !in_array($data['duplex_printing'], $validDuplexPrinting) && !in_array(strtolower($data['duplex_printing']), ['true', 'false', '1', '0', 'yes', 'no'])) {
            throw new \Exception("Invalid duplex printing value '{$data['duplex_printing']}'. Valid values: " . implode(', ', $validDuplexPrinting));
        }
    }

    private function validatePeripheral($data, $rowNumber)
    {
        // For peripherals, we validate the asset ID reference (same as computers, monitors, and printers)
        $validator = Validator::make($data, [
            'asset_id' => 'required|integer|exists:assets,id',
            'type' => 'required|string|max:255',
            'interface' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Check if peripheral record already exists for this asset
        $existingPeripheral = Peripheral::where('asset_id', $data['asset_id'])->first();
        if ($existingPeripheral) {
            throw new \Exception("Peripheral record for asset ID '{$data['asset_id']}' already exists. Skipping duplicate entry.");
        }

        // Validate peripheral type
        $validPeripheralTypes = ['Mouse', 'Keyboard', 'Webcam', 'Headset', 'Speaker', 'Microphone', 'USB Hub', 'External Drive', 'Other'];
        if (!empty($data['type']) && !in_array($data['type'], $validPeripheralTypes)) {
            throw new \Exception("Invalid peripheral type '{$data['type']}'. Valid types: " . implode(', ', $validPeripheralTypes));
        }

        // Validate interface type
        $validInterfaces = ['USB', 'Bluetooth', 'Wireless', 'Wired'];
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
        
        // Map display names back to field keys for processing
        $fieldKeys = $this->mapDisplayNamesToKeys($module, $headers);
        
        // Validate header
        $requiredFields = $this->getRequiredFields($module);
        $missingFields = array_diff($requiredFields, $headers);
        
        if (!empty($missingFields)) {
            // Get valid values reference for the module
            $validValues = $this->getValidValuesReference($module);
            
            return response()->json([
                'success' => false,
                'errors' => [[
                    'row' => 1,
                    'field' => 'header',
                    'message' => 'Missing required columns: ' . implode(', ', $missingFields),
                    'value' => implode(', ', $headers)
                ]],
                'warnings' => [],
                'summary' => [
                    'total' => count($data),
                    'errors' => 1,
                    'warnings' => 0
                ],
                'valid_values' => $validValues
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
            
            $rowData = array_combine($fieldKeys, array_pad($row, count($fieldKeys), ''));
            
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

        // Get valid values reference for the module
        $validValues = $this->getValidValuesReference($module);

        return response()->json([
            'success' => empty($errors),
            'summary' => [
                'total' => $totalRows,
                'errors' => count($errors),
                'warnings' => count($warnings)
            ],
            'errors' => $errors,
            'warnings' => $warnings,
            'valid_values' => $validValues
        ]);
    }

    /**
     * Get valid values reference for a module
     */
    private function getValidValuesReference($module)
    {
        $validValues = [];

        switch ($module) {
            case 'assets':
                $validValues['categories'] = \App\Models\AssetCategory::pluck('name')->toArray();
                $validValues['vendors'] = \App\Models\Vendor::pluck('name')->toArray();
                $validValues['departments'] = \App\Models\Department::pluck('name')->toArray();
                $validValues['required_columns'] = [
                    'asset_tag', 'asset_name', 'category', 'vendor', 'status', 'movement', 
                    'model', 'serial_number', 'purchase_date', 'purchase_cost', 'po_number', 
                    'entity', 'lifespan', 'location', 'notes'
                ];
                break;
                
            case 'users':
                $validValues['departments'] = \App\Models\Department::pluck('name')->toArray();
                $validValues['companies'] = ['Philtower', 'MIDC', 'PRIMUS'];
                $validValues['roles'] = ['User', 'Manager', 'Admin'];
                $validValues['statuses'] = ['0', '1'];
                $validValues['required_columns'] = [
                    'employee_id', 'first_name', 'last_name', 'email_address', 'department', 
                    'company', 'job_title', 'phone_number', 'status', 'role', 'password', 'confirm_password'
                ];
                break;
                
            case 'departments':
                $validValues['required_columns'] = ['name', 'description', 'parent_id', 'manager_id'];
                break;
                
            case 'vendors':
                $validValues['required_columns'] = ['name', 'contact_person', 'email', 'phone', 'address'];
                break;
                
            case 'asset_categories':
                $validValues['required_columns'] = ['name', 'description'];
                break;
        }

        return $validValues;
    }

    /**
     * Show import results page
     */
    public function showResults()
    {
        return view('import-results');
    }
}