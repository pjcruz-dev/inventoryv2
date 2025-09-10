<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $fillable = [
        'asset_id',
        'vendor_id',
        'issue_reported',
        'repair_action',
        'cost',
        'start_date',
        'end_date',
        'status',
        'remarks',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    // Relationships
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
