<?php

namespace App\Exports;

use App\Models\AssetAssignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AssetAssignmentExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $isTemplate;
    
    public function __construct($isTemplate = false)
    {
        $this->isTemplate = $isTemplate;
    }
    
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->isTemplate) {
            // Return empty collection for template
            return collect([]);
        }
        
        return AssetAssignment::with(['asset', 'user', 'assignedBy'])
                             ->orderBy('created_at', 'desc')
                             ->get();
    }
    
    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Asset Tag',
            'Asset Name',
            'User Name',
            'User Email',
            'Assigned By',
            'Assigned Date',
            'Return Date',
            'Status',
            'Notes',
            'Created At',
            'Updated At'
        ];
    }
    
    /**
     * @param mixed $assignment
     * @return array
     */
    public function map($assignment): array
    {
        return [
            $assignment->id,
            $assignment->asset->asset_tag ?? 'N/A',
            $assignment->asset->name ?? 'N/A',
            $assignment->user->name ?? 'N/A',
            $assignment->user->email ?? 'N/A',
            $assignment->assignedBy->name ?? 'N/A',
            $assignment->assigned_date ? $assignment->assigned_date->format('Y-m-d H:i:s') : '',
            $assignment->return_date ? $assignment->return_date->format('Y-m-d H:i:s') : '',
            ucfirst($assignment->status),
            $assignment->notes ?? '',
            $assignment->created_at->format('Y-m-d H:i:s'),
            $assignment->updated_at->format('Y-m-d H:i:s')
        ];
    }
    
    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
            
            // Set background color for header
            1 => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE2E2E2']
                ]
            ]
        ];
    }
}