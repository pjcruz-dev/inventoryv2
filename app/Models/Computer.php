<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ActivityLoggable;

class Computer extends Model
{
    use ActivityLoggable;
    
    protected $fillable = [
        'asset_id',
        'processor',
        'memory',
        'storage',
        'operating_system',
        'computer_type',
    ];

    // Relationships
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
