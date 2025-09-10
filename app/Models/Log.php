<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        'loggable_type',
        'loggable_id',
        'user_id',
        'event_type',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'category',
        'asset_id',
        'role_id',
        'permission_id',
        'department_id',
        'remarks',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // Relationships
    public function loggable()
    {
        return $this->morphTo();
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
