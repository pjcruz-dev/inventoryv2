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
        $csvContent .= implode(',', $templateData['sample_data']) . "\n";
        
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
                'headers' => ['asset_id', 'asset_name', 'processor', 'memory_ram', 'storage', 'operating_system'],
                'sample_data' => ['AST001', 'Dell OptiPlex 7090', 'Intel Core i7-11700', '16GB DDR4', '512GB SSD', 'Windows 11 Pro']
            ],
            'monitors' => [
                'headers' => ['asset_id', 'asset_name', 'size', 'resolution'],
                'sample_data' => ['AST002', 'Dell 24" Monitor', '24 inch', '1920x1080']
            ],
            'printers' => [
                'headers' => ['asset_id', 'asset_name', 'type'],
                'sample_data' => ['AST003', 'HP LaserJet Pro', 'Laser']
            ],
            'peripherals' => [
                'headers' => ['asset_id', 'asset_name', 'type', 'interface'],
                'sample_data' => ['AST004', 'Logitech Mouse', 'Mouse', 'USB']
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


