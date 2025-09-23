<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'message',
        'data',
        'notifiable_type',
        'notifiable_id',
        'read_at',
        'expires_at',
        'is_urgent',
        'action_url',
        'action_text',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_urgent' => 'boolean',
    ];

    /**
     * Get the notifiable entity that the notification belongs to.
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Mark the notification as unread.
     */
    public function markAsUnread(): void
    {
        $this->update(['read_at' => null]);
    }

    /**
     * Check if the notification is read.
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Check if the notification is unread.
     */
    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    /**
     * Check if the notification is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get the notification icon based on type.
     */
    public function getIconAttribute(): string
    {
        $icons = [
            'info' => 'fas fa-info-circle',
            'success' => 'fas fa-check-circle',
            'warning' => 'fas fa-exclamation-triangle',
            'error' => 'fas fa-times-circle',
            'asset_update' => 'fas fa-box',
            'user_action' => 'fas fa-user',
            'system' => 'fas fa-cog',
            'maintenance' => 'fas fa-wrench',
            'assignment' => 'fas fa-handshake',
            'disposal' => 'fas fa-trash',
        ];

        return $icons[$this->type] ?? 'fas fa-bell';
    }

    /**
     * Get the notification color based on type.
     */
    public function getColorAttribute(): string
    {
        $colors = [
            'info' => 'primary',
            'success' => 'success',
            'warning' => 'warning',
            'error' => 'danger',
            'asset_update' => 'info',
            'user_action' => 'secondary',
            'system' => 'dark',
            'maintenance' => 'warning',
            'assignment' => 'success',
            'disposal' => 'danger',
        ];

        return $colors[$this->type] ?? 'primary';
    }

    /**
     * Get the time ago string for the notification.
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Scope to get unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope to get read notifications.
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope to get urgent notifications.
     */
    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true);
    }

    /**
     * Scope to get non-expired notifications.
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope to get recent notifications.
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}