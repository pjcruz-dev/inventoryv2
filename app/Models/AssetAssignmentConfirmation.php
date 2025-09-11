<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AssetAssignmentConfirmation extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'asset_id',
        'user_id',
        'confirmation_token',
        'status',
        'assigned_at',
        'confirmed_at',
        'notes',
        'last_reminder_sent_at',
        'reminder_count'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'last_reminder_sent_at' => 'datetime',
        'reminder_count' => 'integer'
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
     * Mark the confirmation as declined
     */
    public function markAsDeclined(string $reason = null, string $comments = null): void
    {
        $notes = [];
        if ($reason) {
            $notes[] = 'Reason: ' . $reason;
        }
        if ($comments) {
            $notes[] = 'Comments: ' . $comments;
        }

        $this->update([
            'status' => 'declined',
            'confirmed_at' => now(),
            'notes' => implode(' | ', $notes)
        ]);
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
}
