<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Vendor;
use App\Models\AssetCategory;

class ValidationController extends Controller
{
    /**
     * Validate CSV file for import
     */
    public function validateCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
            'module' => 'required|string|in:users,assets,computers,monitors,printers,peripherals,departments,vendors,asset_categories'
        ]);
        
        $module = $request->input('module');
        $file = $request->file('file');
        $path = $file->getRealPath();
        
        try {
            // Read CSV file
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
            
            $data = array_map('str_getcsv', $csvData);
            $headers = array_shift($data);
            
            // Clean headers (remove BOM and trim)
            $headers = array_map(function($header) {
                return trim(str_replace("\xEF\xBB\xBF", '', $header));
            }, $headers);
            
            $errors = [];
            $warnings = [];
            
            // Get required fields for the module
            $requiredFields = $this->getRequiredFields($module);
            
            // Check for missing required columns
            $missingFields = array_diff($requiredFields, $headers);
            if (!empty($missingFields)) {
                $errors[] = [
                    'row' => 1,
                    'field' => 'header',
                    'message' => 'Missing required columns: ' . implode(', ', $missingFields),
                    'value' => implode(', ', $headers),
                    'suggestion' => 'Add these columns to your CSV: ' . implode(', ', $missingFields)
                ];
            }
            
            // Get valid values from database
            $validValues = $this->getValidValues();
            
            // Validate each row
            foreach ($data as $index => $row) {
                $rowNumber = $index + 2; // +2 because we removed headers and arrays are 0-indexed
                
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
                
                // Validate specific fields based on module
                $this->validateRowData($module, $rowData, $rowNumber, $validValues, $errors);
            }
            
            return response()->json([
                'success' => empty($errors),
                'summary' => [
                    'total_rows' => count($data),
                    'errors' => count($errors),
                    'warnings' => count($warnings)
                ],
                'errors' => $errors,
                'warnings' => $warnings,
                'valid_values' => $validValues
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => [[
                    'row' => 1,
                    'field' => 'file',
                    'message' => 'Error processing file: ' . $e->getMessage(),
                    'value' => ''
                ]]
            ]);
        }
    }
    
    /**
     * Get required fields for each module
     */
    private function getRequiredFields($module)
    {
        $requiredFields = [
            'users' => ['employee_id', 'first_name', 'last_name', 'email_address', 'department'],
            'assets' => ['asset_tag', 'asset_name', 'category', 'vendor'],
            'computers' => ['asset_id', 'asset_name', 'processor', 'memory_ram', 'storage', 'operating_system'],
            'monitors' => ['asset_id', 'asset_name', 'size', 'resolution'],
            'printers' => ['asset_id', 'asset_name', 'type'],
            'peripherals' => ['asset_id', 'asset_name', 'type', 'interface'],
            'departments' => ['name'],
            'vendors' => ['vendor_name'],
            'asset_categories' => ['category_name']
        ];
        
        return $requiredFields[$module] ?? [];
    }
    
    /**
     * Get valid values from database
     */
    private function getValidValues()
    {
        return [
            'departments' => Department::pluck('name')->toArray(),
            'vendors' => Vendor::pluck('name')->toArray(),
            'categories' => AssetCategory::pluck('name')->toArray()
        ];
    }
    
    /**
     * Validate row data based on module
     */
    private function validateRowData($module, $rowData, $rowNumber, $validValues, &$errors)
    {
        if ($module === 'users') {
            $this->validateUserData($rowData, $rowNumber, $validValues, $errors);
        } elseif ($module === 'assets') {
            $this->validateAssetData($rowData, $rowNumber, $validValues, $errors);
        }
        // Add more module validations as needed
    }
    
    /**
     * Validate user data
     */
    private function validateUserData($rowData, $rowNumber, $validValues, &$errors)
    {
        // Check department
        if (!empty($rowData['department']) && !in_array($rowData['department'], $validValues['departments'])) {
            $errors[] = [
                'row' => $rowNumber,
                'field' => 'department',
                'message' => "Department '{$rowData['department']}' not found in database",
                'value' => $rowData['department'],
                'suggestion' => 'Use one of these valid departments: ' . implode(', ', array_slice($validValues['departments'], 0, 10)) . '...'
            ];
        }
        
        // Check email format
        if (!empty($rowData['email_address']) && !filter_var($rowData['email_address'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = [
                'row' => $rowNumber,
                'field' => 'email_address',
                'message' => 'Invalid email format',
                'value' => $rowData['email_address'],
                'suggestion' => 'Use a valid email format like: user@company.com'
            ];
        }
    }
    
    /**
     * Validate asset data
     */
    private function validateAssetData($rowData, $rowNumber, $validValues, &$errors)
    {
        // Check category
        if (!empty($rowData['category']) && !in_array($rowData['category'], $validValues['categories'])) {
            $errors[] = [
                'row' => $rowNumber,
                'field' => 'category',
                'message' => "Category '{$rowData['category']}' not found in database",
                'value' => $rowData['category'],
                'suggestion' => 'Use one of these valid categories: ' . implode(', ', $validValues['categories'])
            ];
        }
        
        // Check vendor
        if (!empty($rowData['vendor']) && !in_array($rowData['vendor'], $validValues['vendors'])) {
            $errors[] = [
                'row' => $rowNumber,
                'field' => 'vendor',
                'message' => "Vendor '{$rowData['vendor']}' not found in database",
                'value' => $rowData['vendor'],
                'suggestion' => 'Use one of these valid vendors: ' . implode(', ', $validValues['vendors'])
            ];
        }
    }
}

