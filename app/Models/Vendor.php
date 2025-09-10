<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ActivityLoggable;

class Vendor extends Model
{
    use ActivityLoggable;
    
    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
    ];

    // Relationships
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function maintenance()
    {
        return $this->hasMany(Maintenance::class);
    }
}
