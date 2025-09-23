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
        'company',
        'entity',
        'position',
        'email',
        'password',
        'role_id',
        'status',
        'phone',
        'job_title',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
    
    /**
     * Validation rules for user creation
     */
    public static function validationRules(): array
    {
        return [
            'employee_id' => 'required|string|max:50|unique:users,employee_id',
            'first_name' => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
            'phone' => 'nullable|string|max:20|regex:/^[+]?[0-9\s\-\(\)]+$/',
            'department_id' => 'required|exists:departments,id',
            'company' => 'nullable|in:Philtower,MIDC',
            'role_id' => 'required|exists:roles,id',
            'job_title' => 'nullable|string|max:100',
            'status' => 'required|integer|in:0,1,2'
        ];
    }
    
    /**
     * Validation rules for user update
     */
    public static function updateValidationRules($userId): array
    {
        return [
            'employee_id' => 'required|string|max:50|unique:users,employee_id,' . $userId,
            'first_name' => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|max:150|unique:users,email,' . $userId,
            'password' => 'nullable|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
            'phone' => 'nullable|string|max:20|regex:/^[+]?[0-9\s\-\(\)]+$/',
            'department_id' => 'required|exists:departments,id',
            'company' => 'nullable|in:Philtower,MIDC',
            'role_id' => 'required|exists:roles,id',
            'job_title' => 'nullable|string|max:100',
            'status' => 'required|integer|in:0,1,2'
        ];
    }
    
    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($user) {
            // Ensure email is lowercase
            $user->email = strtolower($user->email);
            
            // Generate employee_id if not provided
            if (empty($user->employee_id)) {
                $user->employee_id = 'EMP' . str_pad(User::count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
        
        static::updating(function ($user) {
            // Ensure email is lowercase
            if ($user->isDirty('email')) {
                $user->email = strtolower($user->email);
            }
        });
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
