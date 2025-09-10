<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
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
