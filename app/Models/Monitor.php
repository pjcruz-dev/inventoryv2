<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ActivityLoggable;

class Monitor extends Model
{
    use ActivityLoggable;
    
    protected $fillable = [
        'asset_id',
        'size',
        'resolution',
        'panel_type',
    ];

    // Relationships
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
