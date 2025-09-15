<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use App\Traits\ActivityLoggable;

class Role extends SpatieRole
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
