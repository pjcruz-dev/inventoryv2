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
                'asset_name' => ['name' => 'Asset Name', 'required' => true, 'type' => 'string', 'max_length' => 255, 'description' => 'Asset display name'],
                'category' => ['name' => 'Category', 'required' => true, 'type' => 'string', 'description' => 'Asset category (must exist in system)'],
                'status' => ['name' => 'Status', 'required' => true, 'type' => 'enum', 'options' => ['Available', 'Active', 'Inactive', 'Under Maintenance', 'Issue Reported', 'Pending Confirmation', 'Disposed'], 'default' => 'Available', 'description' => 'Current asset status'],
                'movement' => ['name' => 'Movement', 'required' => true, 'type' => 'enum', 'options' => ['New Arrival', 'Transfer', 'Return', 'Disposal'], 'default' => 'New Arrival', 'description' => 'Asset movement type'],
                'model' => ['name' => 'Model', 'required' => true, 'type' => 'string', 'max_length' => 255, 'description' => 'Asset model/brand'],
                'serial_number' => ['name' => 'Serial Number', 'required' => true, 'type' => 'string', 'max_length' => 100, 'unique' => true, 'description' => 'Manufacturer serial number'],
                'vendor' => ['name' => 'Vendor', 'required' => false, 'type' => 'string', 'description' => 'Vendor/supplier name (must exist in system)'],
                'purchase_date' => ['name' => 'Purchase Date', 'required' => false, 'type' => 'date', 'format' => 'YYYY-MM-DD', 'description' => 'Date of purchase'],
                'purchase_cost' => ['name' => 'Purchase Cost', 'required' => false, 'type' => 'decimal', 'min' => 0, 'description' => 'Purchase cost in local currency'],
                'po_number' => ['name' => 'PO Number', 'required' => false, 'type' => 'string', 'max_length' => 100, 'description' => 'Purchase order number'],
                'entity' => ['name' => 'Entity', 'required' => true, 'type' => 'enum', 'options' => ['MIDC', 'Philtower', 'PRIMUS'], 'description' => 'Entity/organization'],
                'lifespan' => ['name' => 'Lifespan', 'required' => false, 'type' => 'integer', 'min' => 1, 'description' => 'Asset lifespan in years'],
                'location' => ['name' => 'Location', 'required' => false, 'type' => 'string', 'max_length' => 255, 'description' => 'Physical location of asset'],
                'notes' => ['name' => 'Notes', 'required' => false, 'type' => 'text', 'description' => 'Additional notes or comments']
            ],
            'computers' => [
                'asset_id' => ['name' => 'Asset ID', 'required' => true, 'type' => 'integer', 'description' => 'Reference to existing asset ID'],
                'asset_name' => ['name' => 'Asset Name', 'required' => true, 'type' => 'string', 'max_length' => 255, 'description' => 'Asset name for reference'],
                'processor' => ['name' => 'Processor', 'required' => true, 'type' => 'string', 'max_length' => 255, 'description' => 'CPU model and specifications'],
                'memory_ram' => ['name' => 'Memory (RAM)', 'required' => true, 'type' => 'string', 'max_length' => 255, 'description' => 'Memory specifications (e.g., 16GB DDR4)'],
                'storage' => ['name' => 'Storage', 'required' => true, 'type' => 'string', 'max_length' => 255, 'description' => 'Storage specifications (e.g., 512GB SSD)'],
                'graphics_card' => ['name' => 'Graphics Card', 'required' => false, 'type' => 'string', 'max_length' => 255, 'description' => 'Graphics card specifications'],
                'computer_type' => ['name' => 'Computer Type', 'required' => true, 'type' => 'enum', 'options' => ['Desktop', 'Laptop', 'Server', 'Workstation'], 'description' => 'Type of computer'],
                'operating_system' => ['name' => 'Operating System', 'required' => false, 'type' => 'enum', 'options' => ['Windows 10', 'Windows 11', 'MacOS', 'Ubuntu', 'CentOS'], 'description' => 'Installed operating system']
            ],
            'users' => [
                'employee_id' => ['name' => 'Employee ID', 'required' => true, 'type' => 'string', 'max_length' => 50, 'description' => 'Internal employee ID (unique)'],
                'first_name' => ['name' => 'First Name', 'required' => true, 'type' => 'string', 'max_length' => 255, 'description' => 'Employee first name'],
                'last_name' => ['name' => 'Last Name', 'required' => true, 'type' => 'string', 'max_length' => 255, 'description' => 'Employee last name'],
                'email_address' => ['name' => 'Email Address', 'required' => true, 'type' => 'email', 'unique' => true, 'description' => 'Employee email address (unique)'],
                'department' => ['name' => 'Department', 'required' => true, 'type' => 'string', 'description' => 'Department name (must exist in system - see valid values below)'],
                'company' => ['name' => 'Company', 'required' => false, 'type' => 'enum', 'options' => ['Philtower', 'MIDC', 'PRIMUS'], 'description' => 'Company/organization'],
                'job_title' => ['name' => 'Job Title', 'required' => false, 'type' => 'string', 'max_length' => 255, 'description' => 'Job title/position'],
                'phone_number' => ['name' => 'Phone Number', 'required' => false, 'type' => 'string', 'max_length' => 50, 'description' => 'Contact phone number'],
                'status' => ['name' => 'Status', 'required' => true, 'type' => 'integer', 'options' => [0, 1], 'default' => 1, 'description' => 'Employee status: 1=Active, 0=Inactive'],
                'role' => ['name' => 'Role', 'required' => false, 'type' => 'string', 'description' => 'System role name (User, Manager, Admin) - use either role or role_id'],
                'role_id' => ['name' => 'Role ID', 'required' => false, 'type' => 'integer', 'options' => [1, 2, 3, 4, 5], 'description' => 'System role ID: 1=Super Admin, 2=Admin, 3=Manager, 4=User, 5=IT Support'],
                'password' => ['name' => 'Password', 'required' => false, 'type' => 'string', 'default' => '1234', 'description' => 'User password (default: 1234)'],
                'confirm_password' => ['name' => 'Confirm Password', 'required' => false, 'type' => 'string', 'default' => '1234', 'description' => 'Password confirmation (default: 1234)']
            ],
            'departments' => [
                'name' => ['name' => 'Department Name', 'required' => true, 'type' => 'string', 'max_length' => 100, 'unique' => true, 'description' => 'Department name'],
                'description' => ['name' => 'Description', 'required' => false, 'type' => 'text', 'description' => 'Department description'],
                'parent_id' => ['name' => 'Parent Department ID', 'required' => false, 'type' => 'integer', 'description' => 'ID of parent department (leave empty for root departments)'],
                'manager_id' => ['name' => 'Manager User ID', 'required' => false, 'type' => 'integer', 'description' => 'ID of department manager user']
            ],
            'vendors' => [
                'name' => ['name' => 'Vendor Name', 'required' => true, 'type' => 'string', 'max_length' => 255, 'unique' => true, 'description' => 'Vendor/supplier name'],
                'contact_person' => ['name' => 'Contact Person', 'required' => true, 'type' => 'string', 'max_length' => 255, 'description' => 'Primary contact person'],
                'email' => ['name' => 'Email Address', 'required' => true, 'type' => 'email', 'description' => 'Vendor contact email'],
                'phone' => ['name' => 'Phone Number', 'required' => false, 'type' => 'string', 'max_length' => 50, 'description' => 'Vendor contact phone'],
                'address' => ['name' => 'Address', 'required' => false, 'type' => 'text', 'description' => 'Vendor address']
            ],
            'monitors' => [
                'asset_id' => ['name' => 'Asset ID', 'required' => true, 'type' => 'integer', 'description' => 'Reference to existing asset ID'],
                'asset_name' => ['name' => 'Asset Name', 'required' => true, 'type' => 'string', 'max_length' => 255, 'description' => 'Asset name for reference'],
                'size' => ['name' => 'Size', 'required' => true, 'type' => 'string', 'max_length' => 50, 'description' => 'Monitor screen size (e.g., 24")'],
                'resolution' => ['name' => 'Resolution', 'required' => true, 'type' => 'enum', 'options' => ['1920x1080 (FullHD)', '2560x1440 (QHD)', '3840x2160 (4K UHD)', '1366x768 (HD)', '1680x1050 (WSXGA+)', '1920x1200 (WUXGA)', '2560x1600 (WQXGA)', '5120x2880 (5K)'], 'description' => 'Monitor resolution'],
                'panel_type' => ['name' => 'Panel Type', 'required' => true, 'type' => 'enum', 'options' => ['LCD', 'LED', 'OLED', 'CRT', 'Plasma'], 'description' => 'Panel technology type']
            ],
            'printers' => [
                'asset_id' => ['name' => 'Asset ID', 'required' => true, 'type' => 'integer', 'description' => 'Reference to existing asset ID'],
                'asset_name' => ['name' => 'Asset Name', 'required' => true, 'type' => 'string', 'max_length' => 255, 'description' => 'Asset name for reference'],
                'printer_type' => ['name' => 'Printer Type', 'required' => true, 'type' => 'enum', 'options' => ['Inkjet', 'Laser', 'Dot Matrix', 'Thermal', '3D'], 'description' => 'Type of printer technology'],
                'color_support' => ['name' => 'Color Support', 'required' => true, 'type' => 'enum', 'options' => ['Color Printing', 'Monochrome Only'], 'description' => 'Color printing capability'],
                'duplex_printing' => ['name' => 'Duplex Printing', 'required' => true, 'type' => 'enum', 'options' => ['Duplex Support', 'Single-sided Only'], 'description' => 'Double-sided printing capability']
            ],
            'peripherals' => [
                'asset_id' => ['name' => 'Asset ID', 'required' => true, 'type' => 'integer', 'description' => 'Reference to existing asset ID'],
                'asset_name' => ['name' => 'Asset Name', 'required' => true, 'type' => 'string', 'max_length' => 255, 'description' => 'Asset name for reference'],
                'type' => ['name' => 'Type', 'required' => false, 'type' => 'enum', 'options' => ['Mouse', 'Keyboard', 'Webcam', 'Headset', 'Speaker', 'Microphone', 'USB Hub', 'External Drive'], 'description' => 'Type of peripheral device'],
                'interface' => ['name' => 'Interface', 'required' => false, 'type' => 'enum', 'options' => ['USB', 'Bluetooth', 'Wireless', 'Wired'], 'description' => 'Connection interface type']
            ],
            'asset_categories' => [
                'name' => ['name' => 'Category Name', 'required' => true, 'type' => 'string', 'max_length' => 100, 'unique' => true, 'description' => 'Asset category name'],
                'description' => ['name' => 'Description', 'required' => false, 'type' => 'text', 'description' => 'Category description']
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
            case 'asset_categories':
                return $this->generateAssetCategorySampleData();
            default:
                return [];
        }
    }

    /**
     * Generate asset sample data with realistic values
     */
    private function generateAssetSampleData(): array
    {
        return [
            [
                'asset_tag' => 'LAP001',
                'asset_name' => 'Dell Latitude 7420',
                'category' => 'Computer Hardware',
                'status' => 'Available',
                'movement' => 'New Arrival',
                'model' => 'Latitude 7420',
                'serial_number' => 'SN000001',
                'vendor' => 'Dell Technologies',
                'purchase_date' => now()->subDays(30)->format('Y-m-d'),
                'purchase_cost' => '85000.00',
                'po_number' => 'PO-2024-001',
                'entity' => 'MIDC',
                'lifespan' => '5',
                'location' => 'Information and Communications Technology',
                'notes' => 'Business laptop with Intel i7 processor'
            ],
            [
                'asset_tag' => 'MON001',
                'asset_name' => 'Samsung 27" Monitor',
                'category' => 'Monitors',
                'status' => 'Available',
                'movement' => 'New Arrival',
                'model' => 'Samsung 27" 4K',
                'serial_number' => 'SN000002',
                'vendor' => 'Samsung Electronics',
                'purchase_date' => now()->subDays(45)->format('Y-m-d'),
                'purchase_cost' => '25000.00',
                'po_number' => 'PO-2024-002',
                'entity' => 'Philtower',
                'lifespan' => '7',
                'location' => 'Human Resources and Administration',
                'notes' => '27-inch 4K display monitor'
            ],
            [
                'asset_tag' => 'PRT001',
                'asset_name' => 'HP LaserJet Pro',
                'category' => 'Printers',
                'status' => 'Available',
                'movement' => 'New Arrival',
                'model' => 'LaserJet Pro M404n',
                'serial_number' => 'SN000003',
                'vendor' => 'HP Inc.',
                'purchase_date' => now()->subDays(60)->format('Y-m-d'),
                'purchase_cost' => '15000.00',
                'po_number' => 'PO-2024-003',
                'entity' => 'PRIMUS',
                'lifespan' => '3',
                'location' => 'Finance',
                'notes' => 'Color laser printer with duplex printing'
            ]
        ];
    }

    /**
     * Generate computer sample data
     */
    private function generateComputerSampleData(): array
    {
        return [
            [
                'asset_id' => '1',
                'asset_name' => 'Dell OptiPlex 7090 Desktop',
                'processor' => 'Intel Core i7-11700 @ 2.50GHz',
                'memory_ram' => '16GB DDR4-3200',
                'storage' => '512GB NVMe SSD',
                'graphics_card' => 'Intel UHD Graphics 750',
                'computer_type' => 'Desktop',
                'operating_system' => 'Windows 11 Pro'
            ],
            [
                'asset_id' => '2',
                'asset_name' => 'HP EliteBook 850 Laptop',
                'processor' => 'Intel Core i5-1235U @ 1.30GHz',
                'memory_ram' => '8GB DDR4-3200',
                'storage' => '256GB NVMe SSD',
                'graphics_card' => 'Intel Iris Xe Graphics',
                'computer_type' => 'Laptop',
                'operating_system' => 'Windows 11 Pro'
            ],
            [
                'asset_id' => '3',
                'asset_name' => 'Dell PowerEdge T340 Server',
                'processor' => 'Intel Xeon E-2224 @ 3.40GHz',
                'memory_ram' => '32GB DDR4 ECC',
                'storage' => '1TB SATA SSD',
                'graphics_card' => 'Integrated',
                'computer_type' => 'Server',
                'operating_system' => 'Windows Server 2022'
            ]
        ];
    }

    /**
     * Generate user sample data
     */
    private function generateUserSampleData(): array
    {
        return [
            [
                'employee_id' => 'EMP001',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email_address' => 'john.doe@company.com',
                'department' => 'Human Resources and Administration',
                'company' => 'Philtower',
                'job_title' => 'Software Developer',
                'phone_number' => '+63 912 345 6789',
                'status' => '1',
                'role' => 'User',
                'role_id' => '4',
                'password' => '1234',
                'confirm_password' => '1234'
            ],
            [
                'employee_id' => 'EMP002',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email_address' => 'jane.smith@company.com',
                'department' => 'Human Resources and Administration',
                'company' => 'MIDC',
                'job_title' => 'HR Manager',
                'phone_number' => '+63 912 345 6790',
                'status' => '1',
                'role' => 'Manager',
                'role_id' => '3',
                'password' => '1234',
                'confirm_password' => '1234'
            ],
            [
                'employee_id' => 'EMP003',
                'first_name' => 'Mike',
                'last_name' => 'Johnson',
                'email_address' => 'mike.johnson@company.com',
                'department' => 'Finance',
                'company' => 'PRIMUS',
                'job_title' => 'Accountant',
                'phone_number' => '+63 912 345 6791',
                'status' => '0',
                'role' => 'User',
                'role_id' => '4',
                'password' => '1234',
                'confirm_password' => '1234'
            ]
        ];
    }

    /**
     * Generate department sample data
     */
    private function generateDepartmentSampleData(): array
    {
        return [
            [
                'name' => 'Human Resources and Administration',
                'description' => 'HR and administrative division managing personnel and office operations',
                'parent_id' => '',
                'manager_id' => ''
            ],
            [
                'name' => 'Finance',
                'description' => 'Finance division handling financial operations',
                'parent_id' => '',
                'manager_id' => ''
            ],
            [
                'name' => 'Operations & Maintenance',
                'description' => 'Division managing operational activities and maintenance',
                'parent_id' => '',
                'manager_id' => ''
            ]
        ];
    }

    /**
     * Generate vendor sample data
     */
    private function generateVendorSampleData(): array
    {
        return [
            [
                'name' => 'Dell Technologies',
                'contact_person' => 'Maria Santos',
                'email' => 'maria.santos@dell.com',
                'phone' => '+63 2 8567 1234',
                'address' => '30th Floor, One Corporate Centre, Meralco Avenue, Ortigas Center, Pasig City'
            ],
            [
                'name' => 'HP Inc.',
                'contact_person' => 'Juan Dela Cruz',
                'email' => 'juan.delacruz@hp.com',
                'phone' => '+63 2 8567 5678',
                'address' => '6750 Ayala Avenue, Makati City, Metro Manila'
            ],
            [
                'name' => 'Samsung Electronics',
                'contact_person' => 'Ana Rodriguez',
                'email' => 'ana.rodriguez@samsung.com',
                'phone' => '+63 2 8567 9999',
                'address' => 'BGC Corporate Center, Taguig City, Metro Manila'
            ]
        ];
    }

    /**
     * Generate monitor sample data
     */
    private function generateMonitorSampleData(): array
    {
        return [
            [
                'asset_id' => '1',
                'asset_name' => 'Samsung 27" 4K UHD Monitor',
                'size' => '27"',
                'resolution' => '3840x2160 (4K UHD)',
                'panel_type' => 'LED'
            ],
            [
                'asset_id' => '2',
                'asset_name' => 'Dell 24" Full HD Monitor',
                'size' => '24"',
                'resolution' => '1920x1080 (FullHD)',
                'panel_type' => 'IPS'
            ],
            [
                'asset_id' => '3',
                'asset_name' => 'HP 32" QHD Monitor',
                'size' => '32"',
                'resolution' => '2560x1440 (QHD)',
                'panel_type' => 'VA'
            ]
        ];
    }

    /**
     * Generate printer sample data
     */
    private function generatePrinterSampleData(): array
    {
        return [
            [
                'asset_id' => '1',
                'asset_name' => 'HP LaserJet Pro M404n',
                'printer_type' => 'Laser',
                'color_support' => 'Monochrome Only',
                'duplex_printing' => 'Duplex Support'
            ],
            [
                'asset_id' => '2',
                'asset_name' => 'Canon PIXMA G3110',
                'printer_type' => 'Inkjet',
                'color_support' => 'Color Printing',
                'duplex_printing' => 'Single-sided Only'
            ],
            [
                'asset_id' => '3',
                'asset_name' => 'Brother MFC-L2750DW',
                'printer_type' => 'Laser',
                'color_support' => 'Monochrome Only',
                'duplex_printing' => 'Duplex Support'
            ]
        ];
    }

    /**
     * Generate peripheral sample data
     */
    private function generatePeripheralSampleData(): array
    {
        return [
            [
                'asset_id' => '1',
                'asset_name' => 'Logitech MX Master 3 Mouse',
                'type' => 'Mouse',
                'interface' => 'Wireless'
            ],
            [
                'asset_id' => '2',
                'asset_name' => 'Logitech K380 Keyboard',
                'type' => 'Keyboard',
                'interface' => 'Bluetooth'
            ],
            [
                'asset_id' => '3',
                'asset_name' => 'Logitech C920 Webcam',
                'type' => 'Webcam',
                'interface' => 'USB'
            ],
            [
                'asset_id' => '4',
                'asset_name' => 'JBL Go 3 Speaker',
                'type' => 'Speaker',
                'interface' => 'Bluetooth'
            ]
        ];
    }

    /**
     * Generate asset category sample data
     */
    private function generateAssetCategorySampleData(): array
    {
        return [
            [
                'name' => 'Computer Hardware',
                'description' => 'Desktop computers, laptops, servers, and internal computer components (RAM, CPU, motherboards, etc.)'
            ],
            [
                'name' => 'Monitors',
                'description' => 'Computer monitors, displays, and screen-related equipment'
            ],
            [
                'name' => 'Printers',
                'description' => 'Printers, scanners, multifunction devices, and printing equipment'
            ],
            [
                'name' => 'Peripherals',
                'description' => 'External devices like keyboards, mice, webcams, speakers, and other computer accessories'
            ],
            [
                'name' => 'Network Equipment',
                'description' => 'Routers, switches, access points, cables, and networking hardware'
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
                'employee_id' => 'required|string|max:50',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email_address' => 'required|email|max:255',
                'department' => 'required|string|exists:departments,name',
                'company' => 'nullable|string|max:255',
                'job_title' => 'nullable|string|max:255',
                'phone_number' => 'nullable|string|max:50',
                'status' => 'nullable|string|max:255',
                'role' => 'nullable|string|max:255',
                'role_id' => 'nullable|integer|exists:roles,id'
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
                'department' => ['table' => 'departments', 'field' => 'name', 'target' => 'department_id'],
                'role' => ['table' => 'roles', 'field' => 'name', 'target' => 'role_id']
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
                $data['roles'] = \App\Models\Role::pluck('name', 'id')->toArray();
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
            'generated_by' => auth()->check() ? auth()->user()->name : 'System',
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
                return \App\Models\Computer::count();
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