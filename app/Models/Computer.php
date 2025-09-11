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
        'ram',
        'storage',
        'os',
    ];

    // Relationships
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
