<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ActivityLoggable;

class Department extends Model
{
    use ActivityLoggable;
    protected $fillable = [
        'name',
        'description',
        'manager_id',
    ];

    // Relationships
    public function logs()
    {
        return $this->hasMany(Log::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'department_id');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
