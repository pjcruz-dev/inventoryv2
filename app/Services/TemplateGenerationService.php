<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Vendor;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class TemplateGenerationService
{
    /**
     * Generate comprehensive template with auto-populated data
     */
    public function generateTemplate(string $module, array $options = []): array
    {
        $templateData = [
            'headers' => $this->getTemplateHeaders($module),
            'sample_data' => $this->generateSampleData($module),
            'validation_rules' => $this->getValidationRules($module),
            'field_mappings' => $this->getFieldMappings($module),
            'auto_populated_data' => $this->getAutoPopulatedData($module),
            'metadata' => $this->generateMetadata($module)
        ];

        return $templateData;
    }

    /**
     * Get template headers with descriptions and validation info
     */
    private function getTemplateHeaders(string $module): array
    {
        $headers = [
            'assets' => [
                'asset_tag' => ['name' => 'Asset Tag', 'required' => true, 'type' => 'string', 'max_length' => 50, 'description' => 'Unique asset identifier'],
                'category_name' => ['name' => 'Category Name', 'required' => true, 'type' => 'string', 'description' => 'Asset category (must exist in system)'],
                'vendor_name' => ['name' => 'Vendor Name', 'required' => true, 'type' => 'string', 'description' => 'Vendor/supplier name (must exist in system)'],
                'name' => ['name' => 'Asset Name', 'required' => true, 'type' => 'string', 'max_length' => 255, 'description' => 'Asset display name'],
                'description' => ['name' => 'Description', 'required' => false, 'type' => 'text', 'description' => 'Detailed asset description'],
                'serial_number' => ['name' => 'Serial Number', 'required' => false, 'type' => 'string', 'max_length' => 100, 'unique' => true, 'description' => 'Manufacturer serial number'],
                'purchase_date' => ['name' => 'Purchase Date', 'required' => false, 'type' => 'date', 'format' => 'YYYY-MM-DD', 'description' => 'Date of purchase'],
                'warranty_end' => ['name' => 'Warranty End Date', 'required' => false, 'type' => 'date', 'format' => 'YYYY-MM-DD', 'description' => 'Warranty expiration date'],
                'cost' => ['name' => 'Cost', 'required' => false, 'type' => 'decimal', 'min' => 0, 'description' => 'Purchase cost in local currency'],
                'status' => ['name' => 'Status', 'required' => false, 'type' => 'enum', 'options' => ['Available', 'Active', 'Inactive', 'Under Maintenance', 'Issue Reported', 'Pending Confirmation', 'Disposed'], 'default' => 'Available', 'description' => 'Current asset status']
            ],
            'computers' => [
                'asset_tag' => ['name' => 'Asset Tag', 'required' => true, 'type' => 'string', 'max_length' => 50, 'description' => 'Unique asset identifier'],
                'category_name' => ['name' => 'Category Name', 'required' => true, 'type' => 'string', 'description' => 'Asset category (must exist in system)'],
                'vendor_name' => ['name' => 'Vendor Name', 'required' => true, 'type' => 'string', 'description' => 'Vendor/supplier name (must exist in system)'],
                'name' => ['name' => 'Computer Name', 'required' => true, 'type' => 'string', 'max_length' => 255, 'description' => 'Computer model/name'],
                'description' => ['name' => 'Description', 'required' => false, 'type' => 'text', 'description' => 'Detailed computer description'],
                'serial_number' => ['name' => 'Serial Number', 'required' => false, 'type' => 'string', 'max_length' => 100, 'unique' => true, 'description' => 'Manufacturer serial number'],
                'purchase_date' => ['name' => 'Purchase Date', 'required' => false, 'type' => 'date', 'format' => 'YYYY-MM-DD', 'description' => 'Date of purchase'],
                'warranty_end' => ['name' => 'Warranty End Date', 'required' => false, 'type' => 'date', 'format' => 'YYYY-MM-DD', 'description' => 'Warranty expiration date'],
                'cost' => ['name' => 'Cost', 'required' => false, 'type' => 'decimal', 'min' => 0, 'description' => 'Purchase cost in local currency'],
                'status' => ['name' => 'Status', 'required' => false, 'type' => 'enum', 'options' => ['Available', 'Active', 'Inactive', 'Under Maintenance', 'Issue Reported', 'Pending Confirmation', 'Disposed'], 'default' => 'Available', 'description' => 'Current asset status'],
                'processor' => ['name' => 'Processor', 'required' => true, 'type' => 'string', 'max_length' => 255, 'description' => 'CPU model and specifications'],
                'ram' => ['name' => 'RAM', 'required' => true, 'type' => 'string', 'max_length' => 255, 'description' => 'Memory specifications (e.g., 16GB DDR4)'],
                'storage' => ['name' => 'Storage', 'required' => true, 'type' => 'string', 'max_length' => 255, 'description' => 'Storage specifications (e.g., 512GB SSD)'],
                'os' => ['name' => 'Operating System', 'required' => true, 'type' => 'string', 'max_length' => 255, 'description' => 'Installed operating system']
            ],
            'users' => [
                'employee_no' => ['name' => 'Employee Number', 'required' => true, 'type' => 'string', 'max_length' => 50, 'unique' => true, 'description' => 'Unique employee identifier'],
                'employee_id' => ['name' => 'Employee ID', 'required' => false, 'type' => 'string', 'max_length' => 50, 'description' => 'Internal employee ID'],
                'first_name' => ['name' => 'First Name', 'required' => true, 'type' => 'string', 'max_length' => 255, 'description' => 'Employee first name'],
                'last_name' => ['name' => 'Last Name', 'required' => true, 'type' => 'string', 'max_length' => 255, 'description' => 'Employee last name'],
                'email' => ['name' => 'Email Address', 'required' => true, 'type' => 'email', 'unique' => true, 'description' => 'Employee email address'],
                'department_name' => ['name' => 'Department', 'required' => true, 'type' => 'string', 'description' => 'Department name (must exist in system)'],
                'position' => ['name' => 'Position', 'required' => false, 'type' => 'string', 'max_length' => 255, 'description' => 'Job title/position'],
                'role_name' => ['name' => 'Role', 'required' => false, 'type' => 'string', 'description' => 'System role (must exist in system)'],
                'status' => ['name' => 'Status', 'required' => false, 'type' => 'enum', 'options' => ['active', 'inactive'], 'default' => 'active', 'description' => 'Employee status']
            ],
            'departments' => [
                'name' => ['name' => 'Department Name', 'required' => true, 'type' => 'string', 'max_length' => 255, 'unique' => true, 'description' => 'Department name'],
                'description' => ['name' => 'Description', 'required' => false, 'type' => 'text', 'description' => 'Department description'],
                'email' => ['name' => 'Email', 'required' => false, 'type' => 'email', 'description' => 'Department contact email']
            ],
            'vendors' => [
                'name' => ['name' => 'Vendor Name', 'required' => true, 'type' => 'string', 'max_length' => 255, 'unique' => true, 'description' => 'Vendor/supplier name'],
                'contact_person' => ['name' => 'Contact Person', 'required' => false, 'type' => 'string', 'max_length' => 255, 'description' => 'Primary contact person'],
                'email' => ['name' => 'Email', 'required' => false, 'type' => 'email', 'description' => 'Vendor contact email'],
                'phone' => ['name' => 'Phone', 'required' => false, 'type' => 'string', 'max_length' => 50, 'description' => 'Vendor contact phone'],
                'address' => ['name' => 'Address', 'required' => false, 'type' => 'text', 'description' => 'Vendor address']
            ],
            'monitors' => [
                'asset_tag' => ['name' => 'Asset Tag', 'required' => true, 'type' => 'string', 'max_length' => 50, 'description' => 'Unique asset identifier'],
                'category_name' => ['name' => 'Category Name', 'required' => true, 'type' => 'string', 'description' => 'Asset category (must exist in system)'],
                'vendor_name' => ['name' => 'Vendor Name', 'required' => true, 'type' => 'string', 'description' => 'Vendor/supplier name (must exist in system)'],
                'name' => ['name' => 'Monitor Name', 'required' => true, 'type' => 'string', 'max_length' => 255, 'description' => 'Monitor model/name'],
                'description' => ['name' => 'Description', 'required' => false, 'type' => 'text', 'description' => 'Detailed monitor description'],
                'serial_number' => ['name' => 'Serial Number', 'required' => false, 'type' => 'string', 'max_length' => 100, 'unique' => true, 'description' => 'Manufacturer serial number'],
                'purchase_date' => ['name' => 'Purchase Date', 'required' => false, 'type' => 'date', 'format' => 'YYYY-MM-DD', 'description' => 'Date of purchase'],
                'warranty_end' => ['name' => 'Warranty End Date', 'required' => false, 'type' => 'date', 'format' => 'YYYY-MM-DD', 'description' => 'Warranty expiration date'],
                'cost' => ['name' => 'Cost', 'required' => false, 'type' => 'decimal', 'min' => 0, 'description' => 'Purchase cost in local currency'],
                'status' => ['name' => 'Status', 'required' => false, 'type' => 'enum', 'options' => ['Available', 'Active', 'Inactive', 'Under Maintenance', 'Issue Reported', 'Pending Confirmation', 'Disposed'], 'default' => 'Available', 'description' => 'Current asset status'],
                'screen_size' => ['name' => 'Screen Size', 'required' => false, 'type' => 'string', 'max_length' => 50, 'description' => 'Monitor screen size (e.g., 24")'],
                'resolution' => ['name' => 'Resolution', 'required' => false, 'type' => 'string', 'max_length' => 50, 'description' => 'Monitor resolution (e.g., 1920x1080)'],
                'panel_type' => ['name' => 'Panel Type', 'required' => false, 'type' => 'string', 'max_length' => 50, 'description' => 'Panel type (e.g., IPS, TN, VA)']
            ],
            'printers' => [
                'asset_tag' => ['name' => 'Asset Tag', 'required' => true, 'type' => 'string', 'max_length' => 50, 'description' => 'Unique asset identifier'],
                'category_name' => ['name' => 'Category Name', 'required' => true, 'type' => 'string', 'description' => 'Asset category (must exist in system)'],
                'vendor_name' => ['name' => 'Vendor Name', 'required' => true, 'type' => 'string', 'description' => 'Vendor/supplier name (must exist in system)'],
                'name' => ['name' => 'Printer Name', 'required' => true, 'type' => 'string', 'max_length' => 255, 'description' => 'Printer model/name'],
                'description' => ['name' => 'Description', 'required' => false, 'type' => 'text', 'description' => 'Detailed printer description'],
                'serial_number' => ['name' => 'Serial Number', 'required' => false, 'type' => 'string', 'max_length' => 100, 'unique' => true, 'description' => 'Manufacturer serial number'],
                'purchase_date' => ['name' => 'Purchase Date', 'required' => false, 'type' => 'date', 'format' => 'YYYY-MM-DD', 'description' => 'Date of purchase'],
                'warranty_end' => ['name' => 'Warranty End Date', 'required' => false, 'type' => 'date', 'format' => 'YYYY-MM-DD', 'description' => 'Warranty expiration date'],
                'cost' => ['name' => 'Cost', 'required' => false, 'type' => 'decimal', 'min' => 0, 'description' => 'Purchase cost in local currency'],
                'status' => ['name' => 'Status', 'required' => false, 'type' => 'enum', 'options' => ['Available', 'Active', 'Inactive', 'Under Maintenance', 'Issue Reported', 'Pending Confirmation', 'Disposed'], 'default' => 'Available', 'description' => 'Current asset status'],
                'printer_type' => ['name' => 'Printer Type', 'required' => false, 'type' => 'string', 'max_length' => 50, 'description' => 'Printer type (e.g., Laser, Inkjet)'],
                'color_support' => ['name' => 'Color Support', 'required' => false, 'type' => 'boolean', 'description' => 'Whether printer supports color printing'],
                'duplex_support' => ['name' => 'Duplex Support', 'required' => false, 'type' => 'boolean', 'description' => 'Whether printer supports duplex printing']
            ],
            'peripherals' => [
                'asset_tag' => ['name' => 'Asset Tag', 'required' => true, 'type' => 'string', 'max_length' => 50, 'description' => 'Unique asset identifier'],
                'category_name' => ['name' => 'Category Name', 'required' => true, 'type' => 'string', 'description' => 'Asset category (must exist in system)'],
                'vendor_name' => ['name' => 'Vendor Name', 'required' => true, 'type' => 'string', 'description' => 'Vendor/supplier name (must exist in system)'],
                'name' => ['name' => 'Peripheral Name', 'required' => true, 'type' => 'string', 'max_length' => 255, 'description' => 'Peripheral model/name'],
                'description' => ['name' => 'Description', 'required' => false, 'type' => 'text', 'description' => 'Detailed peripheral description'],
                'serial_number' => ['name' => 'Serial Number', 'required' => false, 'type' => 'string', 'max_length' => 100, 'unique' => true, 'description' => 'Manufacturer serial number'],
                'purchase_date' => ['name' => 'Purchase Date', 'required' => false, 'type' => 'date', 'format' => 'YYYY-MM-DD', 'description' => 'Date of purchase'],
                'warranty_end' => ['name' => 'Warranty End Date', 'required' => false, 'type' => 'date', 'format' => 'YYYY-MM-DD', 'description' => 'Warranty expiration date'],
                'cost' => ['name' => 'Cost', 'required' => false, 'type' => 'decimal', 'min' => 0, 'description' => 'Purchase cost in local currency'],
                'status' => ['name' => 'Status', 'required' => false, 'type' => 'enum', 'options' => ['Available', 'Active', 'Inactive', 'Under Maintenance', 'Issue Reported', 'Pending Confirmation', 'Disposed'], 'default' => 'Available', 'description' => 'Current asset status'],
                'peripheral_type' => ['name' => 'Peripheral Type', 'required' => false, 'type' => 'string', 'max_length' => 100, 'description' => 'Type of peripheral (e.g., Mouse, Keyboard, Webcam)'],
                'connectivity' => ['name' => 'Connectivity', 'required' => false, 'type' => 'string', 'max_length' => 50, 'description' => 'Connection type (e.g., USB, Wireless, Bluetooth)']
            ]
        ];

        return $headers[$module] ?? [];
    }

    /**
     * Generate intelligent sample data based on existing database records
     */
    private function generateSampleData(string $module): array
    {
        switch ($module) {
            case 'assets':
                return $this->generateAssetSampleData();
            case 'computers':
                return $this->generateComputerSampleData();
            case 'users':
                return $this->generateUserSampleData();
            case 'departments':
                return $this->generateDepartmentSampleData();
            case 'vendors':
                return $this->generateVendorSampleData();
            case 'monitors':
                return $this->generateMonitorSampleData();
            case 'printers':
                return $this->generatePrinterSampleData();
            case 'peripherals':
                return $this->generatePeripheralSampleData();
            default:
                return [];
        }
    }

    /**
     * Generate asset sample data with realistic values
     */
    private function generateAssetSampleData(): array
    {
        $categories = AssetCategory::pluck('name')->take(3)->toArray();
        $vendors = Vendor::pluck('name')->take(3)->toArray();
        
        $samples = [];
        $assetTypes = [
            ['tag' => 'LAP001', 'name' => 'Dell Latitude 7420', 'description' => 'Business laptop with Intel i7 processor'],
            ['tag' => 'MON001', 'name' => 'Samsung 27" Monitor', 'description' => '27-inch 4K display monitor'],
            ['tag' => 'PRT001', 'name' => 'HP LaserJet Pro', 'description' => 'Color laser printer with duplex printing']
        ];

        foreach ($assetTypes as $index => $asset) {
            $samples[] = [
                'asset_tag' => $asset['tag'],
                'category_name' => $categories[$index % count($categories)] ?? 'Computer Hardware',
                'vendor_name' => $vendors[$index % count($vendors)] ?? 'Dell Inc',
                'name' => $asset['name'],
                'description' => $asset['description'],
                'serial_number' => 'SN' . str_pad($index + 1, 6, '0', STR_PAD_LEFT),
                'purchase_date' => now()->subDays(rand(30, 365))->format('Y-m-d'),
                'warranty_end' => now()->addYears(3)->format('Y-m-d'),
                'cost' => number_format(rand(10000, 100000) / 100, 2, '.', ''),
                'status' => 'Available'
            ];
        }

        return $samples;
    }

    /**
     * Generate computer sample data
     */
    private function generateComputerSampleData(): array
    {
        $categories = AssetCategory::pluck('name')->take(2)->toArray();
        $vendors = Vendor::pluck('name')->take(2)->toArray();
        
        return [
            [
                'asset_tag' => 'COMP001',
                'category_name' => $categories[0] ?? 'Computer Hardware',
                'vendor_name' => $vendors[0] ?? 'Dell Inc',
                'name' => 'Dell OptiPlex 7090',
                'description' => 'Desktop computer for office use',
                'serial_number' => 'COMP000001',
                'purchase_date' => now()->subDays(60)->format('Y-m-d'),
                'warranty_end' => now()->addYears(3)->format('Y-m-d'),
                'cost' => '75000.00',
                'status' => 'Available',
                'processor' => 'Intel Core i7-11700',
                'ram' => '16GB DDR4',
                'storage' => '512GB SSD',
                'os' => 'Windows 11 Pro'
            ],
            [
                'asset_tag' => 'COMP002',
                'category_name' => $categories[1] ?? 'Computer Hardware',
                'vendor_name' => $vendors[1] ?? 'HP Inc',
                'name' => 'HP EliteDesk 800',
                'description' => 'Compact desktop computer',
                'serial_number' => 'COMP000002',
                'purchase_date' => now()->subDays(45)->format('Y-m-d'),
                'warranty_end' => now()->addYears(3)->format('Y-m-d'),
                'cost' => '68000.00',
                'status' => 'Available',
                'processor' => 'Intel Core i5-11500',
                'ram' => '8GB DDR4',
                'storage' => '256GB SSD',
                'os' => 'Windows 11 Pro'
            ]
        ];
    }

    /**
     * Generate user sample data
     */
    private function generateUserSampleData(): array
    {
        $departments = Department::pluck('name')->take(2)->toArray();
        
        return [
            [
                'employee_no' => 'EMP001',
                'employee_id' => '12345',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@company.com',
                'department_name' => $departments[0] ?? 'IT Department',
                'position' => 'Software Developer',
                'role_name' => 'User',
                'status' => 'active'
            ],
            [
                'employee_no' => 'EMP002',
                'employee_id' => '12346',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@company.com',
                'department_name' => $departments[1] ?? 'HR Department',
                'position' => 'HR Manager',
                'role_name' => 'Manager',
                'status' => 'active'
            ]
        ];
    }

    /**
     * Get validation rules for each module
     */
    private function getValidationRules(string $module): array
    {
        $rules = [
            'assets' => [
                'asset_tag' => 'required|string|max:50|unique:assets,asset_tag',
                'category_name' => 'required|string|exists:asset_categories,name',
                'vendor_name' => 'required|string|exists:vendors,name',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'serial_number' => 'nullable|string|max:100|unique:assets,serial_number',
                'purchase_date' => 'nullable|date|before_or_equal:today',
                'warranty_end' => 'nullable|date|after_or_equal:purchase_date',
                'cost' => 'nullable|numeric|min:0',
                'status' => 'nullable|in:Available,Active,Inactive,Under Maintenance,Issue Reported,Pending Confirmation,Disposed'
            ],
            'computers' => [
                'asset_tag' => 'required|string|max:50|unique:assets,asset_tag',
                'category_name' => 'required|string|exists:asset_categories,name',
                'vendor_name' => 'required|string|exists:vendors,name',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'serial_number' => 'nullable|string|max:100|unique:assets,serial_number',
                'purchase_date' => 'nullable|date|before_or_equal:today',
                'warranty_end' => 'nullable|date|after_or_equal:purchase_date',
                'cost' => 'nullable|numeric|min:0',
                'status' => 'nullable|in:Available,Active,Inactive,Under Maintenance,Issue Reported,Pending Confirmation,Disposed',
                'processor' => 'required|string|max:255',
                'ram' => 'required|string|max:255',
                'storage' => 'required|string|max:255',
                'os' => 'required|string|max:255'
            ],
            'users' => [
                'employee_no' => 'required|string|max:50|unique:users,employee_no',
                'employee_id' => 'nullable|string|max:50',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'department_name' => 'required|string|exists:departments,name',
                'position' => 'nullable|string|max:255',
                'role_name' => 'nullable|string|exists:roles,name',
                'status' => 'nullable|in:active,inactive'
            ],
            'departments' => [
                'name' => 'required|string|max:255|unique:departments,name',
                'description' => 'nullable|string'
            ],
            'vendors' => [
                'name' => 'required|string|max:255|unique:vendors,name',
                'contact_person' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20'
            ],
            'monitors' => [
                'asset_tag' => 'required|string|max:50|unique:assets,asset_tag',
                'brand' => 'required|string|max:255',
                'model' => 'required|string|max:255',
                'size' => 'nullable|string|max:50',
                'resolution' => 'nullable|string|max:50'
            ],
            'printers' => [
                'asset_tag' => 'required|string|max:50|unique:assets,asset_tag',
                'brand' => 'required|string|max:255',
                'model' => 'required|string|max:255',
                'type' => 'nullable|string|max:50',
                'color' => 'nullable|string|max:10'
            ],
            'peripherals' => [
                'asset_tag' => 'required|string|max:50|unique:assets,asset_tag',
                'type' => 'required|string|max:255',
                'brand' => 'required|string|max:255',
                'model' => 'required|string|max:255'
            ]
        ];

        return $rules[$module] ?? [];
    }

    /**
     * Get field mappings for database relationships
     */
    private function getFieldMappings(string $module): array
    {
        $mappings = [
            'assets' => [
                'category_name' => ['table' => 'asset_categories', 'field' => 'name', 'target' => 'category_id'],
                'vendor_name' => ['table' => 'vendors', 'field' => 'name', 'target' => 'vendor_id']
            ],
            'computers' => [
                'category_name' => ['table' => 'asset_categories', 'field' => 'name', 'target' => 'category_id'],
                'vendor_name' => ['table' => 'vendors', 'field' => 'name', 'target' => 'vendor_id']
            ],
            'users' => [
                'department_name' => ['table' => 'departments', 'field' => 'name', 'target' => 'department_id'],
                'role_name' => ['table' => 'roles', 'field' => 'name', 'target' => 'role_id']
            ],
            'departments' => [],
            'vendors' => [],
            'monitors' => [],
            'printers' => [],
            'peripherals' => []
        ];

        return $mappings[$module] ?? [];
    }

    /**
     * Get auto-populated data from existing records
     */
    private function getAutoPopulatedData(string $module): array
    {
        $data = [];

        switch ($module) {
            case 'assets':
            case 'computers':
                $data['categories'] = AssetCategory::pluck('name', 'id')->toArray();
                $data['vendors'] = Vendor::pluck('name', 'id')->toArray();
                break;
            case 'users':
                $data['departments'] = Department::pluck('name', 'id')->toArray();
                $data['roles'] = DB::table('roles')->pluck('name', 'id')->toArray();
                break;
            case 'departments':
            case 'vendors':
            case 'monitors':
            case 'printers':
            case 'peripherals':
                // No auto-populated data needed for these modules
                break;
        }

        return $data;
    }

    /**
     * Generate metadata for the template
     */
    private function generateMetadata(string $module): array
    {
        return [
            'generated_at' => now()->toISOString(),
            'generated_by' => auth()->user()->name ?? 'System',
            'module' => $module,
            'version' => '1.0',
            'total_existing_records' => $this->getExistingRecordCount($module),
            'last_import_date' => $this->getLastImportDate($module)
        ];
    }

    /**
     * Get existing record count for the module
     */
    private function getExistingRecordCount(string $module): int
    {
        switch ($module) {
            case 'assets':
                return Asset::count();
            case 'computers':
                return DB::table('computers')->count();
            case 'users':
                return User::count();
            default:
                return 0;
        }
    }

    /**
     * Get last import date for the module
     */
    private function getLastImportDate(string $module): ?string
    {
        // This would typically come from an import log table
        // For now, return null as placeholder
        return null;
    }

    /**
     * Generate next available asset tag
     */
    public function generateNextAssetTag(string $prefix = 'AST'): string
    {
        $lastAsset = Asset::where('asset_tag', 'LIKE', $prefix . '%')
            ->orderBy('asset_tag', 'desc')
            ->first();

        if (!$lastAsset) {
            return $prefix . '001';
        }

        $lastNumber = (int) substr($lastAsset->asset_tag, strlen($prefix));
        $nextNumber = $lastNumber + 1;

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Generate department sample data
     */
    private function generateDepartmentSampleData(): array
    {
        return [
            ['name' => 'Information Technology', 'description' => 'IT department managing technology infrastructure'],
            ['name' => 'Human Resources', 'description' => 'HR department handling employee relations'],
            ['name' => 'Finance', 'description' => 'Finance department managing company finances']
        ];
    }

    /**
     * Generate vendor sample data
     */
    private function generateVendorSampleData(): array
    {
        return [
            ['name' => 'Dell Technologies', 'contact_person' => 'John Smith', 'email' => 'john.smith@dell.com', 'phone' => '+1-555-0123'],
            ['name' => 'HP Inc', 'contact_person' => 'Sarah Johnson', 'email' => 'sarah.johnson@hp.com', 'phone' => '+1-555-0124'],
            ['name' => 'Lenovo Group', 'contact_person' => 'Mike Chen', 'email' => 'mike.chen@lenovo.com', 'phone' => '+1-555-0125']
        ];
    }

    /**
     * Generate monitor sample data
     */
    private function generateMonitorSampleData(): array
    {
        return [
            ['asset_tag' => 'MON001', 'brand' => 'Dell', 'model' => 'UltraSharp U2720Q', 'size' => '27"', 'resolution' => '3840x2160'],
            ['asset_tag' => 'MON002', 'brand' => 'Samsung', 'model' => 'Odyssey G7', 'size' => '32"', 'resolution' => '2560x1440'],
            ['asset_tag' => 'MON003', 'brand' => 'LG', 'model' => '27UK850-W', 'size' => '27"', 'resolution' => '3840x2160']
        ];
    }

    /**
     * Generate printer sample data
     */
    private function generatePrinterSampleData(): array
    {
        return [
            ['asset_tag' => 'PRT001', 'brand' => 'HP', 'model' => 'LaserJet Pro M404n', 'type' => 'Laser', 'color' => 'No'],
            ['asset_tag' => 'PRT002', 'brand' => 'Canon', 'model' => 'PIXMA TR8620', 'type' => 'Inkjet', 'color' => 'Yes'],
            ['asset_tag' => 'PRT003', 'brand' => 'Brother', 'model' => 'HL-L3270CDW', 'type' => 'Laser', 'color' => 'Yes']
        ];
    }

    /**
     * Generate peripheral sample data
     */
    private function generatePeripheralSampleData(): array
    {
        return [
            ['asset_tag' => 'PER001', 'type' => 'Keyboard', 'brand' => 'Logitech', 'model' => 'MX Keys'],
            ['asset_tag' => 'PER002', 'type' => 'Mouse', 'brand' => 'Logitech', 'model' => 'MX Master 3'],
            ['asset_tag' => 'PER003', 'type' => 'Webcam', 'brand' => 'Logitech', 'model' => 'C920 HD Pro']
        ];
    }

    /**
     * Generate next available serial number
     */
    public function generateNextSerialNumber(string $prefix = 'SN'): string
    {
        do {
            $serialNumber = $prefix . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        } while (Asset::where('serial_number', $serialNumber)->exists());

        return $serialNumber;
    }

    /**
     * Validate serial number uniqueness
     */
    public function validateSerialNumber(string $serialNumber, ?int $excludeAssetId = null): bool
    {
        $query = Asset::where('serial_number', $serialNumber);
        
        if ($excludeAssetId) {
            $query->where('id', '!=', $excludeAssetId);
        }

        return !$query->exists();
    }
}