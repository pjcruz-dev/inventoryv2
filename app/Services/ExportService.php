<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ExportService
{
    /**
     * Export assets to Excel format
     */
    public function exportAssetsToExcel(Collection $assets, array $options = [])
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Inventory Management System')
            ->setLastModifiedBy(auth()->user()->name ?? 'System')
            ->setTitle('Assets Export')
            ->setDescription('Exported assets from inventory management system')
            ->setKeywords('assets, inventory, export')
            ->setCategory('Inventory');
        
        // Set headers
        $headers = [
            'A1' => 'Asset Tag',
            'B1' => 'Name',
            'C1' => 'Category',
            'D1' => 'Status',
            'E1' => 'Serial Number',
            'F1' => 'Assigned To',
            'G1' => 'Department',
            'H1' => 'Location',
            'I1' => 'Purchase Date',
            'J1' => 'Warranty Expiry',
            'K1' => 'Purchase Price',
            'L1' => 'Current Value',
            'M1' => 'Vendor',
            'N1' => 'Created At',
            'O1' => 'Updated At'
        ];
        
        // Apply headers
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        
        // Style headers
        $headerRange = 'A1:O1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        
        // Add data rows
        $row = 2;
        foreach ($assets as $asset) {
            $sheet->setCellValue('A' . $row, $asset->asset_tag);
            $sheet->setCellValue('B' . $row, $asset->name);
            $sheet->setCellValue('C' . $row, $asset->category->name ?? 'N/A');
            $sheet->setCellValue('D' . $row, $asset->status);
            $sheet->setCellValue('E' . $row, $asset->serial_number ?? 'N/A');
            $sheet->setCellValue('F' . $row, $asset->assignedTo->name ?? 'Unassigned');
            $sheet->setCellValue('G' . $row, $asset->assignedTo->department->name ?? 'N/A');
            $sheet->setCellValue('H' . $row, $asset->location ?? 'N/A');
            $sheet->setCellValue('I' . $row, $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : 'N/A');
            $sheet->setCellValue('J' . $row, $asset->warranty_expiry ? $asset->warranty_expiry->format('Y-m-d') : 'N/A');
            $sheet->setCellValue('K' . $row, $asset->purchase_price ? '$' . number_format($asset->purchase_price, 2) : 'N/A');
            $sheet->setCellValue('L' . $row, $asset->current_value ? '$' . number_format($asset->current_value, 2) : 'N/A');
            $sheet->setCellValue('M' . $row, $asset->vendor->name ?? 'N/A');
            $sheet->setCellValue('N' . $row, $asset->created_at->format('Y-m-d H:i:s'));
            $sheet->setCellValue('O' . $row, $asset->updated_at->format('Y-m-d H:i:s'));
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'O') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Add borders to data
        $dataRange = 'A1:O' . ($row - 1);
        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);
        
        // Add summary sheet
        $this->addSummarySheet($spreadsheet, $assets);
        
        return $spreadsheet;
    }
    
    /**
     * Export assets to PDF format
     */
    public function exportAssetsToPDF(Collection $assets, array $options = [])
    {
        $template = $options['template'] ?? 'default';
        $orientation = $options['orientation'] ?? 'portrait';
        $includeImages = $options['include_images'] ?? false;
        
        $data = [
            'assets' => $assets,
            'exported_at' => now(),
            'exported_by' => auth()->user()->name ?? 'System',
            'total_count' => $assets->count(),
            'template' => $template,
            'include_images' => $includeImages
        ];
        
        $pdf = Pdf::loadView("exports.assets.{$template}", $data);
        $pdf->setPaper('A4', $orientation);
        
        return $pdf;
    }
    
    /**
     * Export dashboard data to Excel
     */
    public function exportDashboardToExcel(array $dashboardData)
    {
        $spreadsheet = new Spreadsheet();
        
        // Summary sheet
        $summarySheet = $spreadsheet->getActiveSheet();
        $summarySheet->setTitle('Dashboard Summary');
        
        $this->addDashboardSummary($summarySheet, $dashboardData);
        
        // Assets breakdown sheet
        $assetsSheet = $spreadsheet->createSheet();
        $assetsSheet->setTitle('Assets Breakdown');
        
        if (isset($dashboardData['assets'])) {
            $this->addAssetsBreakdown($assetsSheet, $dashboardData['assets']);
        }
        
        // Status breakdown sheet
        $statusSheet = $spreadsheet->createSheet();
        $statusSheet->setTitle('Status Breakdown');
        
        if (isset($dashboardData['statusBreakdown'])) {
            $this->addStatusBreakdown($statusSheet, $dashboardData['statusBreakdown']);
        }
        
        return $spreadsheet;
    }
    
    /**
     * Export users to Excel
     */
    public function exportUsersToExcel(Collection $users)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $headers = [
            'A1' => 'Employee ID',
            'B1' => 'Name',
            'C1' => 'Email',
            'D1' => 'Department',
            'E1' => 'Role',
            'F1' => 'Status',
            'G1' => 'Phone',
            'H1' => 'Created At',
            'I1' => 'Last Login'
        ];
        
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        
        // Style headers
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        
        // Add data
        $row = 2;
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $row, $user->employee_id ?? 'N/A');
            $sheet->setCellValue('B' . $row, $user->name);
            $sheet->setCellValue('C' . $row, $user->email);
            $sheet->setCellValue('D' . $row, $user->department->name ?? 'N/A');
            $sheet->setCellValue('E' . $row, $user->roles->pluck('name')->join(', ') ?: 'N/A');
            $sheet->setCellValue('F' . $row, $user->is_active ? 'Active' : 'Inactive');
            $sheet->setCellValue('G' . $row, $user->phone ?? 'N/A');
            $sheet->setCellValue('H' . $row, $user->created_at->format('Y-m-d H:i:s'));
            $sheet->setCellValue('I' . $row, $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : 'Never');
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        return $spreadsheet;
    }
    
    /**
     * Add summary sheet to spreadsheet
     */
    private function addSummarySheet(Spreadsheet $spreadsheet, Collection $assets)
    {
        $summarySheet = $spreadsheet->createSheet();
        $summarySheet->setTitle('Summary');
        
        $summaryData = [
            ['Metric', 'Count'],
            ['Total Assets', $assets->count()],
            ['Active Assets', $assets->where('status', 'Active')->count()],
            ['Under Maintenance', $assets->where('status', 'Under Maintenance')->count()],
            ['Issue Reported', $assets->where('status', 'Issue Reported')->count()],
            ['Assigned Assets', $assets->whereNotNull('assigned_to')->count()],
            ['Unassigned Assets', $assets->whereNull('assigned_to')->count()],
            ['Total Value', '$' . number_format($assets->sum('current_value'), 2)],
        ];
        
        $row = 1;
        foreach ($summaryData as $data) {
            $summarySheet->setCellValue('A' . $row, $data[0]);
            $summarySheet->setCellValue('B' . $row, $data[1]);
            $row++;
        }
        
        // Style summary
        $summarySheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E7E6E6']]
        ]);
        
        // Auto-size columns
        $summarySheet->getColumnDimension('A')->setAutoSize(true);
        $summarySheet->getColumnDimension('B')->setAutoSize(true);
    }
    
    /**
     * Add dashboard summary to sheet
     */
    private function addDashboardSummary($sheet, array $data)
    {
        $summaryData = [
            ['Dashboard Summary', ''],
            ['Generated At', now()->format('Y-m-d H:i:s')],
            ['Generated By', auth()->user()->name ?? 'System'],
            ['', ''],
            ['Total Assets', $data['totalAssets'] ?? 0],
            ['Total Users', $data['totalUsers'] ?? 0],
            ['Total Departments', $data['totalDepartments'] ?? 0],
            ['Total Vendors', $data['totalVendors'] ?? 0],
            ['Active Assets %', ($data['activeAssetsPercentage'] ?? 0) . '%'],
        ];
        
        $row = 1;
        foreach ($summaryData as $data) {
            $sheet->setCellValue('A' . $row, $data[0]);
            $sheet->setCellValue('B' . $row, $data[1]);
            $row++;
        }
        
        // Style headers
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']]
        ]);
    }
    
    /**
     * Add assets breakdown to sheet
     */
    private function addAssetsBreakdown($sheet, Collection $assets)
    {
        $headers = ['Asset Tag', 'Name', 'Status', 'Assigned To', 'Value'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }
        
        $row = 2;
        foreach ($assets as $asset) {
            $sheet->setCellValue('A' . $row, $asset->asset_tag);
            $sheet->setCellValue('B' . $row, $asset->name);
            $sheet->setCellValue('C' . $row, $asset->status);
            $sheet->setCellValue('D' . $row, $asset->assignedTo->name ?? 'Unassigned');
            $sheet->setCellValue('E' . $row, $asset->current_value ? '$' . number_format($asset->current_value, 2) : 'N/A');
            $row++;
        }
        
        // Style headers
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E7E6E6']]
        ]);
    }
    
    /**
     * Add status breakdown to sheet
     */
    private function addStatusBreakdown($sheet, array $statusBreakdown)
    {
        $headers = ['Status', 'Count', 'Percentage'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }
        
        $total = array_sum($statusBreakdown);
        $row = 2;
        
        foreach ($statusBreakdown as $status => $count) {
            $percentage = $total > 0 ? round(($count / $total) * 100, 2) : 0;
            $sheet->setCellValue('A' . $row, $status);
            $sheet->setCellValue('B' . $row, $count);
            $sheet->setCellValue('C' . $row, $percentage . '%');
            $row++;
        }
        
        // Style headers
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E7E6E6']]
        ]);
    }
    
    /**
     * Generate export filename
     */
    public function generateFilename(string $type, string $format, array $options = [])
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $entity = $options['entity'] ?? '';
        $entitySuffix = $entity ? "_{$entity}" : '';
        
        return "{$type}_export{$entitySuffix}_{$timestamp}.{$format}";
    }
    
    /**
     * Get available export templates
     */
    public function getAvailableTemplates(string $type)
    {
        $templates = [
            'assets' => [
                'default' => 'Default Asset Report',
                'detailed' => 'Detailed Asset Report',
                'summary' => 'Asset Summary Report',
                'maintenance' => 'Maintenance Report',
                'financial' => 'Financial Report'
            ],
            'users' => [
                'default' => 'Default User Report',
                'detailed' => 'Detailed User Report',
                'department' => 'Department Report'
            ],
            'dashboard' => [
                'summary' => 'Dashboard Summary',
                'detailed' => 'Detailed Dashboard Report'
            ]
        ];
        
        return $templates[$type] ?? [];
    }
}
