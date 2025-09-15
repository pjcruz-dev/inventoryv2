<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ActivityLoggable;

class Disposal extends Model
{
    use ActivityLoggable;
    protected $fillable = [
        'asset_id',
        'disposal_date',
        'disposal_type',
        'disposal_value',
        'approved_by',
        'remarks',
    ];

    protected $casts = [
        'disposal_date' => 'datetime',
        'disposal_value' => 'decimal:2',
    ];

    // Relationships
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }


}
