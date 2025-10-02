<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AssetAssignmentConfirmation;
use App\Mail\AssetAssignmentConfirmation as AssetAssignmentConfirmationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Traits\ActivityLoggable;

class AssetAssignment extends Model
{
    use HasFactory, SoftDeletes, ActivityLoggable;

    protected $fillable = [
        'asset_id',
        'user_id',
        'assigned_by',
        'assigned_date',
        'return_date',
        'status',
        'notes',
        'accountability_printed',
        'accountability_printed_at',
        'accountability_printed_by',
        'signed_form_path',
        'signed_form_uploaded_at',
        'signed_form_uploaded_by',
        'signed_form_description',
        'signed_form_email_subject',
        'signed_form_email_sent',
        'signed_form_email_sent_at',
        'signed_form_email_count'
    ];

    protected $casts = [
        'assigned_date' => 'datetime',
        'return_date' => 'datetime',
        'accountability_printed_at' => 'datetime',
        'signed_form_uploaded_at' => 'datetime',
        'signed_form_email_sent_at' => 'datetime',
        'signed_form_email_sent' => 'boolean'
    ];

    // Relationships
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function accountabilityPrintedBy()
    {
        return $this->belongsTo(User::class, 'accountability_printed_by');
    }

    public function signedFormUploadedBy()
    {
        return $this->belongsTo(User::class, 'signed_form_uploaded_by');
    }

    public function confirmation()
    {
        return $this->hasOne(AssetAssignmentConfirmation::class, 'asset_id', 'asset_id')
                    ->where('user_id', $this->user_id)
                    ->where('assigned_at', $this->assigned_date);
    }

    // Boot method to handle model events
    protected static function boot()
    {
        parent::boot();

        static::created(function ($assignment) {
            // Check if PENDING confirmation already exists for this asset-user combination
            $existingPendingConfirmation = AssetAssignmentConfirmation::where('asset_id', $assignment->asset_id)
                ->where('user_id', $assignment->user_id)
                ->where('status', 'pending')
                ->first();
            
            if (!$existingPendingConfirmation) {
                // Create confirmation record
                $confirmation = AssetAssignmentConfirmation::create([
                    'asset_id' => $assignment->asset_id,
                    'user_id' => $assignment->user_id,
                    'confirmation_token' => Str::random(64),
                    'status' => 'pending',
                    'assigned_at' => $assignment->assigned_date
                ]);

                // Send confirmation email
                try {
                    Mail::to($assignment->user->email)
                        ->send(new AssetAssignmentConfirmationMail(
                            $assignment->asset,
                            $assignment->user,
                            $confirmation->confirmation_token
                        ));
                    
                    \Log::info('Asset assignment confirmation email sent', [
                        'asset_id' => $assignment->asset_id,
                        'user_id' => $assignment->user_id,
                        'user_email' => $assignment->user->email,
                        'confirmation_id' => $confirmation->id
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to send asset assignment confirmation email: ' . $e->getMessage(), [
                        'asset_id' => $assignment->asset_id,
                        'user_id' => $assignment->user_id,
                        'user_email' => $assignment->user->email,
                        'error' => $e->getMessage()
                    ]);
                }
            } else {
                \Log::info('Skipping confirmation creation - pending confirmation already exists', [
                    'asset_id' => $assignment->asset_id,
                    'user_id' => $assignment->user_id,
                    'existing_confirmation_id' => $existingPendingConfirmation->id
                ]);
            }
        });
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeDeclined($query)
    {
        return $query->where('status', 'declined');
    }
}
