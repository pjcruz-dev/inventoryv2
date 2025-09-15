<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ActivityLoggable;

class AssetCategory extends Model
{
    use ActivityLoggable;
    protected $fillable = [
        'name',
        'description',
    ];

    // Relationships
    public function assets()
    {
        return $this->hasMany(Asset::class, 'category_id');
    }
}
