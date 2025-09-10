<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ActivityLoggable;

class Peripheral extends Model
{
    use ActivityLoggable;
    
    protected $fillable = [
        'asset_id',
        'type',
        'interface',
    ];

    // Relationships
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
