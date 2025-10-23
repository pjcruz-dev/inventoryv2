<?php

namespace App\Exports;

use App\Models\AssetAssignmentConfirmation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DeclinedAssetsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected $filters;
    
    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }
    
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = AssetAssignmentConfirmation::where('status', 'declined')
            ->with(['asset.category', 'asset.vendor', 'user.department']);
        
        // Apply filters if provided
        if (!empty($this->filters['severity'])) {
            $query->where('decline_severity', $this->filters['severity']);
        }
        
        if (!empty($this->filters['date_from'])) {
            $query->whereDate('declined_at', '>=', $this->filters['date_from']);
        }
        
        if (!empty($this->filters['date_to'])) {
            $query->whereDate('declined_at', '<=', $this->filters['date_to']);
        }
        
        if (!empty($this->filters['follow_up_required'])) {
            $query->where('follow_up_required', $this->filters['follow_up_required']);
        }
        
        if (!empty($this->filters['category'])) {
            $query->where('decline_category', $this->filters['category']);
        }
        
        return $query->orderBy('declined_at', 'desc')->get();
    }
    
    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Confirmation ID',
            'Asset Tag',
            'Asset Name',
            'Asset Category',
            'Serial Number',
            'Vendor',
            'User Name',
            'User Email',
            'User Department',
            'Declined Date',
            'Decline Reason',
            'Decline Category',
            'Severity Level',
            'Follow-up Required',
            'Follow-up Date',
            'Follow-up Actions',
            'Additional Comments',
            'Contact Preference',
            'Assigned Date',
            'Days Until Declined',
            'Current Asset Status',
            'Asset Location'
        ];
    }
    
    /**
     * @param mixed $confirmation
     * @return array
     */
    public function map($confirmation): array
    {
        $daysUntilDeclined = $confirmation->assigned_at && $confirmation->declined_at 
            ? $confirmation->assigned_at->diffInDays($confirmation->declined_at)
            : null;
        
        return [
            $confirmation->id,
            $confirmation->asset->asset_tag ?? 'N/A',
            $confirmation->asset->asset_name ?? 'N/A',
            $confirmation->asset->category->name ?? 'N/A',
            $confirmation->asset->serial_number ?? 'N/A',
            $confirmation->asset->vendor->name ?? 'N/A',
            $confirmation->user->first_name . ' ' . $confirmation->user->last_name,
            $confirmation->user->email ?? 'N/A',
            $confirmation->user->department->name ?? 'N/A',
            $confirmation->declined_at ? $confirmation->declined_at->format('Y-m-d H:i:s') : '',
            $confirmation->getFormattedDeclineReason(),
            $confirmation->decline_category ? ucwords(str_replace('_', ' ', $confirmation->decline_category)) : 'N/A',
            $confirmation->decline_severity ? strtoupper($confirmation->decline_severity) : 'N/A',
            $confirmation->follow_up_required ? 'Yes' : 'No',
            $confirmation->follow_up_date ? $confirmation->follow_up_date->format('Y-m-d') : 'N/A',
            $confirmation->follow_up_actions ? str_replace('|', ', ', $confirmation->follow_up_actions) : 'N/A',
            $confirmation->decline_comments ?? 'None',
            $confirmation->contact_preference ? ucfirst($confirmation->contact_preference) : 'N/A',
            $confirmation->assigned_at ? $confirmation->assigned_at->format('Y-m-d') : '',
            $daysUntilDeclined ?? 'N/A',
            $confirmation->asset->status ?? 'N/A',
            $confirmation->asset->location ?? 'N/A'
        ];
    }
    
    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Apply styles to header row
        $sheet->getStyle('1:1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF667EEA']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ]);
        
        // Apply conditional formatting for severity levels
        $rowCount = $this->collection()->count() + 1;
        for ($row = 2; $row <= $rowCount; $row++) {
            $severity = $sheet->getCell('M' . $row)->getValue();
            
            if ($severity === 'HIGH') {
                $sheet->getStyle('M' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFF0000']
                    ],
                    'font' => ['color' => ['argb' => 'FFFFFFFF'], 'bold' => true]
                ]);
            } elseif ($severity === 'MEDIUM') {
                $sheet->getStyle('M' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFFC107']
                    ],
                    'font' => ['bold' => true]
                ]);
            } elseif ($severity === 'LOW') {
                $sheet->getStyle('M' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF17A2B8']
                    ],
                    'font' => ['color' => ['argb' => 'FFFFFFFF'], 'bold' => true]
                ]);
            }
            
            // Highlight follow-up required
            $followUpRequired = $sheet->getCell('N' . $row)->getValue();
            if ($followUpRequired === 'Yes') {
                $sheet->getStyle('N' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFFF3CD']
                    ],
                    'font' => ['bold' => true]
                ]);
            }
        }
        
        // Freeze header row
        $sheet->freezePane('A2');
        
        return [];
    }
    
    /**
     * @return string
     */
    public function title(): string
    {
        return 'Declined Assets Report';
    }
}

