<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TemplateController extends Controller
{
    /**
     * Download template for a specific module
     */
    public function downloadTemplate($module)
    {
        $validModules = ['users', 'assets', 'computers', 'monitors', 'printers', 'peripherals', 'departments', 'vendors', 'asset_categories'];
        
        if (!in_array($module, $validModules)) {
            return response()->json(['error' => 'Invalid module specified'], 400);
        }
        
        // Generate CSV template
        $templateData = $this->generateTemplateData($module);
        
        $csvContent = implode(',', $templateData['headers']) . "\n";
        
        // Handle multiple sample data rows
        if (is_array($templateData['sample_data']) && isset($templateData['sample_data'][0]) && is_array($templateData['sample_data'][0])) {
            // Multiple rows of sample data
            foreach ($templateData['sample_data'] as $row) {
                $csvContent .= implode(',', $row) . "\n";
            }
        } else {
            // Single row of sample data
            $csvContent .= implode(',', $templateData['sample_data']) . "\n";
        }
        
        $filename = $module . '_template_' . date('Y-m-d_H-i-s') . '.csv';
        
        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }
    
    /**
     * Generate template data for each module
     */
    private function generateTemplateData($module)
    {
        $templates = [
            'users' => [
                'headers' => ['employee_id', 'first_name', 'last_name', 'email_address', 'department'],
                'sample_data' => ['EMP001', 'John', 'Doe', 'john.doe@company.com', 'Information and Communications Technology']
            ],
            'assets' => [
                'headers' => ['asset_tag', 'asset_name', 'category', 'vendor'],
                'sample_data' => ['AST001', 'Dell OptiPlex 7090', 'Computer Hardware', 'Dell Technologies']
            ],
            'computers' => [
                'headers' => ['asset_id', 'processor', 'memory_ram', 'storage', 'operating_system', 'computer_type', 'graphics_card'],
                'sample_data' => [
                    ['1', 'Intel Core i7-13700K @ 3.40GHz', '32GB DDR5-5600', '1TB NVMe SSD', 'Windows 11 Pro', 'Desktop', 'NVIDIA RTX 4070'],
                    ['2', 'Intel Core i5-13400 @ 2.50GHz', '16GB DDR5-4800', '512GB NVMe SSD', 'Windows 11 Pro', 'Desktop', 'Intel UHD Graphics 730'],
                    ['3', 'AMD Ryzen 9 7950X @ 4.50GHz', '64GB DDR5-6000', '2TB NVMe SSD', 'Windows 11 Pro', 'Workstation', 'NVIDIA RTX 4090'],
                    ['4', 'Intel Core i7-1360P @ 2.20GHz', '16GB LPDDR5-5200', '512GB NVMe SSD', 'Windows 11 Pro', 'Laptop', 'Intel Iris Xe Graphics'],
                    ['5', 'Intel Xeon W-2245 @ 3.90GHz', '32GB DDR4 ECC', '1TB SATA SSD', 'Windows Server 2022', 'Server', 'NVIDIA Quadro RTX 4000'],
                    ['6', 'Apple M3 Pro @ 3.70GHz', '18GB Unified Memory', '1TB SSD', 'macOS Sonoma', 'Laptop', 'Apple M3 Pro GPU']
                ]
            ],
            'monitors' => [
                'headers' => ['asset_id', 'asset_name', 'size', 'resolution', 'panel_type'],
                'sample_data' => [
                    ['1', 'Samsung 27" 4K UHD Monitor', '27"', '3840x2160 (4K UHD)', 'LED'],
                    ['2', 'Dell 24" Full HD Monitor', '24"', '1920x1080 (Full HD)', 'IPS'],
                    ['3', 'HP 32" QHD Monitor', '32"', '2560x1440 (QHD)', 'LCD'],
                    ['4', 'LG 34" Ultrawide Monitor', '34"', '3440x1440 (UltraWide QHD)', 'IPS'],
                    ['5', 'ASUS 49" Super Ultrawide', '49"', '2560x1080 (UltraWide)', 'OLED']
                ]
            ],
            'printers' => [
                'headers' => ['asset_id', 'printer_type', 'color_support', 'duplex_printing'],
                'sample_data' => [
                    ['110', 'Laser', 'No', 'Yes'],
                    ['113', 'Inkjet', 'Yes', 'No'],
                    ['116', 'Dot Matrix', 'No', 'Yes'],
                    ['119', 'Thermal', 'Yes', 'No'],
                    ['122', '3D Printer', 'Yes', 'Yes']
                ]
            ],
            'peripherals' => [
                'headers' => ['asset_id', 'type', 'interface'],
                'sample_data' => [
                    ['130', 'Mouse', 'Wireless'],
                    ['131', 'Keyboard', 'Bluetooth'],
                    ['132', 'Webcam', 'USB'],
                    ['133', 'Headset', 'Wireless'],
                    ['134', 'Speaker', 'Wired'],
                    ['135', 'Microphone', 'USB'],
                    ['136', 'USB Hub', 'USB'],
                    ['137', 'External Drive', 'USB'],
                    ['138', 'Mouse', 'Wired'],
                    ['139', 'Keyboard', 'Wireless']
                ]
            ],
            'departments' => [
                'headers' => ['name'],
                'sample_data' => ['Information Technology']
            ],
            'vendors' => [
                'headers' => ['vendor_name'],
                'sample_data' => ['Dell Technologies']
            ],
            'asset_categories' => [
                'headers' => ['category_name'],
                'sample_data' => ['Computer Hardware']
            ]
        ];
        
        return $templates[$module] ?? ['headers' => [], 'sample_data' => []];
    }
}




