<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'ip_address',
        'user_agent',
        'details',
        'timestamp'
    ];

    protected $casts = [
        'details' => 'array',
        'timestamp' => 'datetime'
    ];

    /**
     * Get the user that performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the model that was affected
     */
    public function model()
    {
        if ($this->model_type && $this->model_id) {
            return $this->morphTo('model', 'model_type', 'model_id');
        }
        
        return null;
    }

    /**
     * Scope for security events
     */
    public function scopeSecurityEvents($query)
    {
        return $query->whereIn('action', [
            'auth_login', 'auth_logout', 'auth_failed_login',
            'rate_limit_exceeded', 'suspicious_activity'
        ]);
    }

    /**
     * Scope for data changes
     */
    public function scopeDataChanges($query)
    {
        return $query->whereIn('action', [
            'created', 'updated', 'deleted', 'restored'
        ]);
    }

    /**
     * Scope for file operations
     */
    public function scopeFileOperations($query)
    {
        return $query->where('action', 'like', 'file_%');
    }
}
