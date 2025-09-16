<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ActivityLoggable;

class Asset extends Model
{
    use ActivityLoggable;
    protected $fillable = [
        'asset_tag',
        'category_id',
        'vendor_id',
        'name',
        'description',
        'serial_number',
        'purchase_date',
        'warranty_end',
        'cost',
        'po_number',
        'entity',
        'lifespan',
        'status',
        'movement',
        'assigned_to',
        'assigned_date',
        'department_id',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_end' => 'date',
        'assigned_date' => 'date',
        'cost' => 'decimal:2',
    ];
    
    /**
     * Validation rules for asset creation
     */
    public static function validationRules(): array
    {
        return [
            'asset_tag' => 'required|string|max:50|unique:assets,asset_tag|regex:/^[A-Z0-9\-_]+$/',
            'category_id' => 'required|exists:asset_categories,id',
            'vendor_id' => 'required|exists:vendors,id',
            'name' => 'required|string|max:255|regex:/^[a-zA-Z0-9\s\-_\.]+$/',
            'description' => 'nullable|string|max:1000',
            'serial_number' => 'required|string|max:100|unique:assets,serial_number',
            'model' => 'nullable|string|max:100',
            'purchase_date' => 'required|date|before_or_equal:today',
            'warranty_end' => 'nullable|date|after:purchase_date',
            'cost' => 'required|numeric|min:0|max:999999.99',
            'po_number' => 'nullable|string|max:100',
            'entity' => 'nullable|in:MIDC,PHILTOWER,PRIMUS',
            'lifespan' => 'nullable|integer|min:1|max:50',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:Available,Assigned,Active,Inactive,Under Maintenance,Issue Reported,Pending Confirmation,Retired,Damaged,Disposed',
            'movement' => 'required|in:New Arrival,Deployed,Returned,Transferred,Disposed',
            'assigned_to' => 'nullable|exists:users,id',
            'assigned_date' => 'nullable|date|required_with:assigned_to',
            'department_id' => 'nullable|exists:departments,id'
        ];
    }
    
    /**
     * Validation rules for asset update
     */
    public static function updateValidationRules($assetId): array
    {
        return [
            'asset_tag' => 'required|string|max:50|unique:assets,asset_tag,' . $assetId . '|regex:/^[A-Z0-9\-_]+$/',
            'category_id' => 'required|exists:asset_categories,id',
            'vendor_id' => 'required|exists:vendors,id',
            'name' => 'required|string|max:255|regex:/^[a-zA-Z0-9\s\-_\.]+$/',
            'description' => 'nullable|string|max:1000',
            'serial_number' => 'required|string|max:100|unique:assets,serial_number,' . $assetId,
            'model' => 'nullable|string|max:100',
            'purchase_date' => 'required|date|before_or_equal:today',
            'warranty_end' => 'nullable|date|after:purchase_date',
            'cost' => 'required|numeric|min:0|max:999999.99',
            'po_number' => 'nullable|string|max:100',
            'entity' => 'nullable|in:MIDC,PHILTOWER,PRIMUS',
            'lifespan' => 'nullable|integer|min:1|max:50',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:Available,Assigned,Active,Inactive,Under Maintenance,Issue Reported,Pending Confirmation,Retired,Damaged,Disposed',
            'movement' => 'required|in:New Arrival,Deployed,Returned,Transferred,Disposed',
            'assigned_to' => 'nullable|exists:users,id',
            'assigned_date' => 'nullable|date|required_with:assigned_to',
            'department_id' => 'nullable|exists:departments,id'
        ];
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function computer()
    {
        return $this->hasOne(Computer::class);
    }

    public function monitor()
    {
        return $this->hasOne(Monitor::class);
    }

    public function printer()
    {
        return $this->hasOne(Printer::class);
    }

    public function peripheral()
    {
        return $this->hasOne(Peripheral::class);
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }

    public function maintenance()
    {
        return $this->hasMany(Maintenance::class);
    }

    public function disposals()
    {
        return $this->hasMany(Disposal::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function timeline()
    {
        return $this->hasMany(AssetTimeline::class)->orderBy('performed_at', 'desc');
    }

    protected static function booted()
    {
        static::created(function ($asset) {
            $asset->createTimelineEntry('created', null, null, 'Asset created');
        });

        static::updating(function ($asset) {
            $original = $asset->getOriginal();
            $changes = $asset->getDirty();
            
            // Track assignment changes
            if (isset($changes['assigned_to'])) {
                $fromUserId = $original['assigned_to'];
                $toUserId = $changes['assigned_to'];
                
                if ($fromUserId && $toUserId) {
                    $action = 'transferred';
                    $notes = 'Asset transferred between users';
                } elseif ($toUserId) {
                    $action = 'assigned';
                    $notes = 'Asset assigned to user';
                } else {
                    $action = 'unassigned';
                    $notes = 'Asset unassigned from user';
                }
                
                $asset->createTimelineEntry(
                    $action,
                    $fromUserId,
                    $toUserId,
                    $notes,
                    $original,
                    $asset->getAttributes()
                );
            } else {
                // Log all other asset updates to timeline
                $changedFields = array_keys($changes);
                $fieldNames = implode(', ', $changedFields);
                
                $notes = 'Asset updated: ' . $fieldNames;
                
                // If asset is unassigned, specifically mention it
                if (!$asset->assigned_to) {
                    $notes .= ' (unassigned asset)';
                }
                
                $asset->createTimelineEntry(
                    'updated',
                    null,
                    null,
                    $notes,
                    $original,
                    $asset->getAttributes()
                );
            }
        });
    }

    public function createTimelineEntry($action, $fromUserId = null, $toUserId = null, $notes = null, $oldValues = null, $newValues = null)
    {
        // Only create timeline entry if user is authenticated
        if (auth()->check()) {
            AssetTimeline::create([
                'asset_id' => $this->id,
                'action' => $action,
                'from_user_id' => $fromUserId,
                'to_user_id' => $toUserId,
                'from_department_id' => $oldValues['department_id'] ?? null,
                'to_department_id' => $newValues['department_id'] ?? $this->department_id,
                'notes' => $notes,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'performed_by' => auth()->id(),
                'performed_at' => now()
            ]);
        }
    }
}
