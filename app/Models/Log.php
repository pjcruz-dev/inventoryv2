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
        // Enhanced activity tracking fields
        'action_details',
        'affected_fields',
        'metadata',
        'session_id',
        'request_method',
        'request_url',
        'request_parameters',
        'browser_name',
        'operating_system',
        'action_timestamp',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'action_details' => 'array',
        'affected_fields' => 'array',
        'metadata' => 'array',
        'request_parameters' => 'array',
        'action_timestamp' => 'datetime',
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

    // Helper methods for enhanced logging
    
    /**
     * Get formatted action details
     */
    public function getFormattedActionDetails()
    {
        if (!$this->action_details) {
            return 'No additional details available';
        }
        
        $details = [];
        foreach ($this->action_details as $key => $value) {
            $details[] = ucfirst(str_replace('_', ' ', $key)) . ': ' . (is_array($value) ? json_encode($value) : $value);
        }
        
        return implode(', ', $details);
    }
    
    /**
     * Get formatted affected fields with changes
     */
    public function getFormattedAffectedFields()
    {
        if (!$this->affected_fields) {
            return 'No field changes recorded';
        }
        
        $changes = [];
        foreach ($this->affected_fields as $field => $change) {
            $fieldName = ucfirst(str_replace('_', ' ', $field));
            $oldValue = $change['old'] ?? 'N/A';
            $newValue = $change['new'] ?? 'N/A';
            $changes[] = "{$fieldName}: '{$oldValue}' → '{$newValue}'";
        }
        
        return implode('; ', $changes);
    }
    
    /**
     * Get browser and OS information
     */
    public function getSystemInfo()
    {
        $info = [];
        if ($this->browser_name) {
            $info[] = $this->browser_name;
        }
        if ($this->operating_system) {
            $info[] = $this->operating_system;
        }
        
        return implode(' on ', $info) ?: 'Unknown system';
    }
    
    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('action_timestamp', [$startDate, $endDate]);
    }
    
    /**
     * Scope for filtering by session
     */
    public function scopeBySession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }
    
    /**
     * Scope for filtering by action type
     */
    public function scopeByAction($query, $actionType)
    {
        return $query->where('event_type', $actionType);
    }
}
