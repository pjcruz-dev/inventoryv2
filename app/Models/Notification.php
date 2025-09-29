<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
        'notifiable_type',
        'notifiable_id'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime'
    ];

    /**
     * Get the user that owns the notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope for specific notification type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Check if notification is read
     */
    public function isRead()
    {
        return !is_null($this->read_at);
    }

    /**
     * Check if notification is unread
     */
    public function isUnread()
    {
        return is_null($this->read_at);
    }

    /**
     * Get notification icon based on type
     */
    public function getIconAttribute()
    {
        $icons = [
            'maintenance_reminder' => 'fas fa-wrench',
            'warranty_expiry' => 'fas fa-exclamation-triangle',
            'assignment_expiry' => 'fas fa-user-clock',
            'security_alert' => 'fas fa-shield-alt',
            'system_maintenance' => 'fas fa-cog',
            'asset_created' => 'fas fa-plus-circle',
            'asset_updated' => 'fas fa-edit',
            'asset_deleted' => 'fas fa-trash',
            'assignment_created' => 'fas fa-user-plus',
            'assignment_updated' => 'fas fa-user-edit',
            'assignment_deleted' => 'fas fa-user-minus',
            'maintenance_created' => 'fas fa-tools',
            'maintenance_completed' => 'fas fa-check-circle',
            'disposal_created' => 'fas fa-recycle',
            'report_generated' => 'fas fa-chart-bar',
            'notification_sent' => 'fas fa-bell'
        ];

        return $icons[$this->type] ?? 'fas fa-bell';
    }

    /**
     * Get notification color based on type
     */
    public function getColorAttribute()
    {
        $colors = [
            'maintenance_reminder' => 'warning',
            'warranty_expiry' => 'danger',
            'assignment_expiry' => 'info',
            'security_alert' => 'danger',
            'system_maintenance' => 'primary',
            'asset_created' => 'success',
            'asset_updated' => 'info',
            'asset_deleted' => 'danger',
            'assignment_created' => 'success',
            'assignment_updated' => 'info',
            'assignment_deleted' => 'danger',
            'maintenance_created' => 'primary',
            'maintenance_completed' => 'success',
            'disposal_created' => 'secondary',
            'report_generated' => 'info',
            'notification_sent' => 'primary'
        ];

        return $colors[$this->type] ?? 'primary';
    }

    /**
     * Get formatted time ago
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get notification badge text
     */
    public function getBadgeTextAttribute()
    {
        $badges = [
            'maintenance_reminder' => 'Maintenance',
            'warranty_expiry' => 'Warranty',
            'assignment_expiry' => 'Assignment',
            'security_alert' => 'Security',
            'system_maintenance' => 'System',
            'asset_created' => 'Asset',
            'asset_updated' => 'Asset',
            'asset_deleted' => 'Asset',
            'assignment_created' => 'Assignment',
            'assignment_updated' => 'Assignment',
            'assignment_deleted' => 'Assignment',
            'maintenance_created' => 'Maintenance',
            'maintenance_completed' => 'Maintenance',
            'disposal_created' => 'Disposal',
            'report_generated' => 'Report',
            'notification_sent' => 'Notification'
        ];

        return $badges[$this->type] ?? 'Notification';
    }
}