<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ComprehensiveAssetExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    use Exportable;
    
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
        $query = Asset::with([
            'category', 
            'vendor', 
            'assignedUser', 
            'assignedUser.department',
            'department',
            'assignmentConfirmations' => function($query) {
                $query->latest();
            },
            'timeline' => function($query) {
                $query->latest()->limit(5);
            }
        ]);
        
        // Apply filters
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_tag', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhereHas('category', function($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('vendor', function($vendorQuery) use ($search) {
                      $vendorQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('assignedUser', function($userQuery) use ($search) {
                      $userQuery->where('first_name', 'like', "%{$search}%")
                               ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }
        
        if (!empty($this->filters['category'])) {
            $query->where('category_id', $this->filters['category']);
        }
        
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        
        if (!empty($this->filters['movement'])) {
            $query->where('movement', $this->filters['movement']);
        }
        
        if (!empty($this->filters['assignment'])) {
            if ($this->filters['assignment'] === 'assigned') {
                $query->whereNotNull('assigned_to');
            } elseif ($this->filters['assignment'] === 'unassigned') {
                $query->whereNull('assigned_to');
            }
        }
        
        return $query->orderBy('asset_tag')->get();
    }
    
    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Asset Tag',
            'Asset Name',
            'Category',
            'Serial Number',
            'Model',
            'Status',
            'Movement',
            'Location',
            'Cost',
            'Purchase Date',
            'Warranty End',
            'PO Number',
            'Entity',
            'Lifespan',
            'Mobile Number',
            'Description',
            'Notes',
            
            // Assigned User Information
            'Assigned User Name',
            'Assigned User Employee ID',
            'Assigned User Email',
            'Assigned User Phone',
            'Assigned User Department',
            'Assigned User Position',
            'Assigned User Company',
            'Assigned User Entity',
            'Assigned Date',
            
            // Vendor Information
            'Vendor Name',
            'Vendor Contact Person',
            'Vendor Email',
            'Vendor Phone',
            'Vendor Address',
            
            // Asset Department
            'Asset Department',
            
            // Assignment Confirmation Status
            'Confirmation Status',
            'Confirmation Date',
            'Confirmation Notes',
            'Decline Reason',
            
            // Recent Timeline Activities (Last 3)
            'Recent Activity 1',
            'Recent Activity 2',
            'Recent Activity 3',
            
            // Timestamps
            'Created At',
            'Updated At'
        ];
    }
    
    /**
     * @param mixed $asset
     * @return array
     */
    public function map($asset): array
    {
        // Get latest assignment confirmation
        $latestConfirmation = $asset->assignmentConfirmations->first();
        
        // Get recent timeline activities
        $recentActivities = $asset->timeline->take(3);
        $activity1 = $recentActivities->get(0);
        $activity2 = $recentActivities->get(1);
        $activity3 = $recentActivities->get(2);
        
        return [
            // Basic Asset Information
            $asset->asset_tag,
            $asset->name,
            $asset->category ? $asset->category->name : 'N/A',
            $asset->serial_number,
            $asset->model,
            $asset->status,
            $asset->movement,
            $asset->location,
            $asset->cost,
            $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : 'N/A',
            $asset->warranty_end ? $asset->warranty_end->format('Y-m-d') : 'N/A',
            $asset->po_number,
            $asset->entity,
            $asset->lifespan,
            $asset->mobile_number,
            $asset->description,
            $asset->notes,
            
            // Assigned User Information
            $asset->assignedUser ? $asset->assignedUser->first_name . ' ' . $asset->assignedUser->last_name : 'Unassigned',
            $asset->assignedUser ? $asset->assignedUser->employee_id : 'N/A',
            $asset->assignedUser ? $asset->assignedUser->email : 'N/A',
            $asset->assignedUser ? $asset->assignedUser->phone : 'N/A',
            $asset->assignedUser && $asset->assignedUser->department ? $asset->assignedUser->department->name : 'N/A',
            $asset->assignedUser ? $asset->assignedUser->job_title : 'N/A',
            $asset->assignedUser ? $asset->assignedUser->company : 'N/A',
            $asset->assignedUser ? $asset->assignedUser->entity : 'N/A',
            $asset->assigned_date ? $asset->assigned_date->format('Y-m-d H:i:s') : 'N/A',
            
            // Vendor Information
            $asset->vendor ? $asset->vendor->name : 'N/A',
            $asset->vendor ? $asset->vendor->contact_person : 'N/A',
            $asset->vendor ? $asset->vendor->email : 'N/A',
            $asset->vendor ? $asset->vendor->phone : 'N/A',
            $asset->vendor ? $asset->vendor->address : 'N/A',
            
            // Asset Department
            $asset->department ? $asset->department->name : 'N/A',
            
            // Assignment Confirmation Status
            $latestConfirmation ? ucfirst($latestConfirmation->status) : 'N/A',
            $latestConfirmation && $latestConfirmation->confirmed_at ? $latestConfirmation->confirmed_at->format('Y-m-d H:i:s') : 
                ($latestConfirmation && $latestConfirmation->declined_at ? $latestConfirmation->declined_at->format('Y-m-d H:i:s') : 'N/A'),
            $latestConfirmation ? $latestConfirmation->notes : 'N/A',
            $latestConfirmation ? $latestConfirmation->decline_reason : 'N/A',
            
            // Recent Timeline Activities
            $activity1 ? $activity1->action . ' - ' . $activity1->notes . ' (' . $activity1->performed_at->format('Y-m-d H:i') . ')' : 'N/A',
            $activity2 ? $activity2->action . ' - ' . $activity2->notes . ' (' . $activity2->performed_at->format('Y-m-d H:i') . ')' : 'N/A',
            $activity3 ? $activity3->action . ' - ' . $activity3->notes . ' (' . $activity3->performed_at->format('Y-m-d H:i') . ')' : 'N/A',
            
            // Timestamps
            $asset->created_at->format('Y-m-d H:i:s'),
            $asset->updated_at->format('Y-m-d H:i:s')
        ];
    }
    
    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE2E3E5']
                ]
            ],
        ];
    }
}
