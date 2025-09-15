<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use App\Traits\ActivityLoggable;

class Permission extends SpatiePermission
{
    use ActivityLoggable;
    protected $fillable = [
        'name',
        'description',
    ];

    // Relationships
    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }
}
