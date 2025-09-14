<?php

namespace App\Exports;

use App\Models\AssetAssignmentConfirmation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AssetAssignmentConfirmationExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
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
        
        return AssetAssignmentConfirmation::with(['asset', 'user'])
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
            'Status',
            'Priority',
            'Assigned At',
            'Confirmed At',
            'Declined At',
            'Notes',
            'Confirmation Token',
            'Created At',
            'Updated At'
        ];
    }
    
    /**
     * @param mixed $confirmation
     * @return array
     */
    public function map($confirmation): array
    {
        return [
            $confirmation->id,
            $confirmation->asset->asset_tag ?? 'N/A',
            $confirmation->asset->name ?? 'N/A',
            $confirmation->user->name ?? 'N/A',
            $confirmation->user->email ?? 'N/A',
            ucfirst($confirmation->status),
            ucfirst($confirmation->priority ?? 'normal'),
            $confirmation->assigned_at ? $confirmation->assigned_at->format('Y-m-d H:i:s') : '',
            $confirmation->confirmed_at ? $confirmation->confirmed_at->format('Y-m-d H:i:s') : '',
            $confirmation->declined_at ? $confirmation->declined_at->format('Y-m-d H:i:s') : '',
            $confirmation->notes ?? '',
            $confirmation->confirmation_token ?? '',
            $confirmation->created_at->format('Y-m-d H:i:s'),
            $confirmation->updated_at->format('Y-m-d H:i:s')
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