<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Traits\ActivityLoggable;

class AssetAssignmentConfirmation extends Model
{
    use HasFactory, ActivityLoggable;

    protected $fillable = [
        'asset_id',
        'user_id',
        'confirmation_token',
        'status',
        'assigned_at',
        'confirmed_at',
        'declined_at',
        'notes',
        'last_reminder_sent_at',
        'reminder_count'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'declined_at' => 'datetime',
        'last_reminder_sent_at' => 'datetime',
        'reminder_count' => 'integer',
        'follow_up_required' => 'boolean',
        'follow_up_date' => 'datetime'
    ];

    /**
     * Generate a unique confirmation token
     */
    public static function generateToken(): string
    {
        do {
            $token = Str::random(64);
        } while (self::where('confirmation_token', $token)->exists());
        
        return $token;
    }

    /**
     * Get the asset that owns the confirmation
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get the user that owns the confirmation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the confirmation is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the confirmation is confirmed
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if the confirmation is declined
     */
    public function isDeclined(): bool
    {
        return $this->status === 'declined';
    }

    /**
     * Check if the confirmation is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if a reminder should be sent
     */
    public function shouldSendReminder(): bool
    {
        if (!$this->isPending()) {
            return false;
        }

        $daysSinceAssignment = $this->assigned_at->diffInDays(now());
        $daysSinceLastReminder = $this->last_reminder_sent_at 
            ? $this->last_reminder_sent_at->diffInDays(now())
            : $daysSinceAssignment;

        return $daysSinceAssignment >= 3 && $daysSinceLastReminder >= 3;
    }

    /**
     * Mark the confirmation as confirmed
     */
    public function markAsConfirmed(): void
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now()
        ]);
    }

    /**
     * Mark the confirmation as declined with enhanced details
     */
    public function markAsDeclined(array $declineData = []): void
    {
        $updateData = [
            'status' => 'declined',
            'declined_at' => now()
        ];

        // Handle enhanced decline data
        if (!empty($declineData['decline_category'])) {
            $updateData['decline_category'] = $declineData['decline_category'];
        }
        if (!empty($declineData['decline_reason'])) {
            $updateData['decline_reason'] = $declineData['decline_reason'];
        }
        if (!empty($declineData['decline_comments'])) {
            $updateData['decline_comments'] = $declineData['decline_comments'];
        }
        if (!empty($declineData['contact_preference'])) {
            $updateData['contact_preference'] = $declineData['contact_preference'];
        }
        if (isset($declineData['follow_up_required'])) {
            $updateData['follow_up_required'] = $declineData['follow_up_required'];
        }
        if (!empty($declineData['follow_up_actions'])) {
            $updateData['follow_up_actions'] = $declineData['follow_up_actions'];
        }
        if (!empty($declineData['follow_up_date'])) {
            $updateData['follow_up_date'] = $declineData['follow_up_date'];
        }
        if (!empty($declineData['decline_severity'])) {
            $updateData['decline_severity'] = $declineData['decline_severity'];
        }

        // Maintain backward compatibility with notes
        $notes = [];
        if (!empty($declineData['decline_reason'])) {
            $notes[] = 'Reason: ' . $declineData['decline_reason'];
        }
        if (!empty($declineData['decline_comments'])) {
            $notes[] = 'Comments: ' . $declineData['decline_comments'];
        }
        if (!empty($notes)) {
            $updateData['notes'] = implode(' | ', $notes);
        }

        $this->update($updateData);
    }

    /**
     * Mark the confirmation as completed
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed'
        ]);
    }

    /**
     * Get formatted decline reason
     */
    public function getFormattedDeclineReason(): string
    {
        if (!$this->decline_reason) {
            return 'No reason provided';
        }

        $reasons = [
            'never_delivered' => 'Asset was never delivered',
            'wrong_asset' => 'Wrong asset was delivered',
            'damaged_asset' => 'Asset was damaged upon delivery',
            'incomplete_delivery' => 'Incomplete delivery (missing accessories/parts)',
            'delivery_location' => 'Delivered to wrong location',
            'timing_issue' => 'Delivery timing issue',
            'other' => 'Other reason'
        ];

        return $reasons[$this->decline_reason] ?? $this->decline_reason;
    }

    /**
     * Get formatted follow-up actions
     */
    public function getFormattedFollowUpActions(): array
    {
        if (!$this->follow_up_actions) {
            return [];
        }

        return explode('|', $this->follow_up_actions);
    }

    /**
     * Get decline severity badge class
     */
    public function getDeclineSeverityBadgeClass(): string
    {
        switch ($this->decline_severity) {
            case 'high':
                return 'bg-danger';
            case 'medium':
                return 'bg-warning';
            case 'low':
                return 'bg-info';
            default:
                return 'bg-secondary';
        }
    }

    /**
     * Check if follow-up is overdue
     */
    public function isFollowUpOverdue(): bool
    {
        return $this->follow_up_required && 
               $this->follow_up_date && 
               $this->follow_up_date->isPast();
    }
}
