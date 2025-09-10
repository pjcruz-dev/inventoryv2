<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\ActivityLoggable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles, ActivityLoggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'employee_no',
        'employee_id',
        'first_name',
        'last_name',
        'department_id',
        'position',
        'email',
        'password',
        'role_id',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // Accessors
    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // Relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function transfersFrom()
    {
        return $this->hasMany(Transfer::class, 'from_user_id');
    }

    public function transfersTo()
    {
        return $this->hasMany(Transfer::class, 'to_user_id');
    }

    public function disposalsApproved()
    {
        return $this->hasMany(Disposal::class, 'approved_by');
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }

    public function assignedAssets()
    {
        return $this->hasMany(Asset::class, 'assigned_to');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
