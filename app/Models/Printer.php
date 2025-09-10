<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ActivityLoggable;

class Printer extends Model
{
    use ActivityLoggable;
    
    protected $fillable = [
        'asset_id',
        'type',
        'color_support',
        'duplex',
    ];

    protected $casts = [
        'color_support' => 'boolean',
        'duplex' => 'boolean',
    ];

    // Relationships
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
