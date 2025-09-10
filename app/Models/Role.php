<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
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
