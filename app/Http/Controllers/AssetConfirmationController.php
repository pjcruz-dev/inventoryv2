<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssetAssignmentConfirmation;
use App\Models\Asset;
use App\Models\Log;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\AssetConfirmationNotification;

class AssetConfirmationController extends Controller
{
    public function __construct()
    {
        // Token-based routes should be publicly accessible without authentication
        // No middleware needed for token-based confirmation routes
    }

    /**
     * Show confirmation page for asset assignment
     */
    public function show($token)
    {
        $confirmation = AssetAssignmentConfirmation::where('confirmation_token', $token)
            ->with(['asset.assetCategory', 'user'])
            ->first();

        if (!$confirmation) {
            return view('asset-confirmation.invalid', [
                'message' => 'Invalid confirmation token. The link may have expired or been used already.'
            ]);
        }

        if (!$confirmation->isPending()) {
            $status = $confirmation->isConfirmed() ? 'confirmed' : 'declined';
            return view('asset-confirmation.already-processed', [
                'confirmation' => $confirmation,
                'status' => $status
            ]);
        }

        return view('asset-confirmation.confirm', compact('confirmation'));
    }

    /**
     * Confirm asset assignment
     */
    public function confirm($token)
    {
        $confirmation = AssetAssignmentConfirmation::where('confirmation_token', $token)
            ->with(['asset', 'user'])
            ->first();

        // Validate token exists
        if (!$confirmation) {
            return view('asset-confirmation.invalid', [
                'message' => 'Invalid confirmation token. The link may have expired or been used already.'
            ]);
        }

        // Check if already processed
        if (!$confirmation->isPending()) {
            $status = $confirmation->isConfirmed() ? 'confirmed' : 'declined';
            $statusDate = $confirmation->confirmed_at ? $confirmation->confirmed_at->format('F j, Y \a\t g:i A') : 'Unknown';
            
            if ($confirmation->isConfirmed()) {
                $message = "This email was already confirmed on {$statusDate}.";
            } else {
                $message = "This request was declined on {$statusDate}.";
            }
            
            return view('asset-confirmation.already-processed', [
                'confirmation' => $confirmation,
                'status' => $status,
                'message' => $message
            ]);
        }

        // Log the confirmation attempt
        \Log::info('Asset confirmation attempt', [
            'confirmation_id' => $confirmation->id,
            'asset_tag' => $confirmation->asset->asset_tag,
            'user_email' => $confirmation->user->email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        // Mark confirmation as confirmed
        $confirmation->markAsConfirmed();

        // Create timeline entry for confirmation
        \App\Models\AssetTimeline::create([
            'asset_id' => $confirmation->asset_id,
            'action' => 'confirmed',
            'from_user_id' => null,
            'to_user_id' => $confirmation->user_id,
            'from_department_id' => null,
            'to_department_id' => $confirmation->asset->department_id,
            'notes' => 'Asset assignment confirmed by user via email confirmation',
            'old_values' => null,
            'new_values' => [
                'confirmation_id' => $confirmation->id,
                'confirmation_token' => $confirmation->confirmation_token,
                'confirmed_at' => now()->toISOString(),
                'user_email' => $confirmation->user->email,
                'user_name' => $confirmation->user->first_name . ' ' . $confirmation->user->last_name
            ],
            'performed_by' => $confirmation->user_id, // Use the actual user who confirmed/declined
            'performed_at' => now()
        ]);

        // Create activity log entry for confirmation
        \App\Models\Log::create([
            'loggable_type' => 'App\Models\AssetAssignmentConfirmation',
            'loggable_id' => $confirmation->id,
            'category' => 'asset_assignment_confirmation',
            'event_type' => 'confirmed',
            'description' => 'Asset assignment confirmed by user via email confirmation',
            'user_id' => $confirmation->user_id,
            'role_id' => $confirmation->user->role_id ?? null,
            'asset_id' => $confirmation->asset_id,
            'department_id' => $confirmation->asset->department_id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'remarks' => 'Asset assignment confirmed by user via email confirmation',
            'action_details' => [
                'confirmation_id' => $confirmation->id,
                'confirmation_token' => $confirmation->confirmation_token,
                'confirmed_at' => now()->toISOString(),
                'user_email' => $confirmation->user->email,
                'user_name' => $confirmation->user->first_name . ' ' . $confirmation->user->last_name,
                'asset_name' => $confirmation->asset->name,
                'asset_tag' => $confirmation->asset->asset_tag
            ],
            'session_id' => request()->hasSession() ? request()->session()->getId() : null,
            'request_method' => request()->method(),
            'request_url' => request()->fullUrl(),
            'action_timestamp' => now()
        ]);

        // Update asset status and movement
        $confirmation->asset->update([
            'status' => 'Active',
            'movement' => 'Deployed'
        ]);

        // Create enhanced audit log using ActivityLogService
        $activityLogService = app(\App\Services\ActivityLogService::class);
        $activityLogService->logActivity(
            $confirmation->asset,
            'confirmed',
            'Asset assignment confirmed by user: ' . $confirmation->user->first_name . ' ' . $confirmation->user->last_name . '. Status changed from Pending Confirmation to Active.',
            ['status' => 'Pending Confirmation', 'movement' => $confirmation->asset->getOriginal('movement')], // old values
            ['status' => 'Active', 'movement' => 'Deployed'], // new values
            [
                'confirming_user_id' => $confirmation->user->id,
                'confirming_user_name' => $confirmation->user->first_name . ' ' . $confirmation->user->last_name,
                'confirmation_id' => $confirmation->id,
                'confirmation_token' => $confirmation->token,
                'confirmed_at' => now()->toISOString(),
                'previous_status' => 'Pending Confirmation',
                'new_status' => 'Active',
                'previous_movement' => $confirmation->asset->getOriginal('movement'),
                'new_movement' => 'Deployed'
            ]
        );

        // Create notification for the user
        $notificationData = [
            'type' => 'asset_confirmed',
            'title' => 'Asset Assignment Confirmed',
            'message' => "You have successfully confirmed the assignment of asset {$confirmation->asset->asset_tag} ({$confirmation->asset->asset_name}).",
            'data' => [
                'asset_tag' => $confirmation->asset->asset_tag,
                'asset_name' => $confirmation->asset->asset_name,
                'confirmation_id' => $confirmation->id,
                'confirmed_at' => now()->toISOString()
            ]
        ];
        
        Notification::create(array_merge($notificationData, [
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $confirmation->user_id
        ]));
        
        // Create identical notification for super administrator
        $this->notifySuperAdmin($notificationData);

        // Send notification email to admin
        try {
            Mail::to('pjcruz@midc.ph')->send(new AssetConfirmationNotification(
                $confirmation,
                'confirmed',
                [
                    'confirmation_id' => $confirmation->id,
                    'confirmed_at' => now()->toISOString(),
                    'user_email' => $confirmation->user->email,
                    'user_name' => $confirmation->user->first_name . ' ' . $confirmation->user->last_name,
                    'asset_name' => $confirmation->asset->name,
                    'asset_tag' => $confirmation->asset->asset_tag
                ]
            ));
        } catch (\Exception $e) {
            // Log error but don't fail the confirmation
            \Log::error('Failed to send confirmation notification email: ' . $e->getMessage());
        }

        return view('asset-confirmation.success', [
            'confirmation' => $confirmation,
            'action' => 'confirmed'
        ]);
    }

    /**
     * Decline asset assignment
     */
    public function decline($token)
    {
        $confirmation = AssetAssignmentConfirmation::where('confirmation_token', $token)
            ->with(['asset', 'user'])
            ->first();

        // Validate token exists
        if (!$confirmation) {
            return view('asset-confirmation.invalid', [
                'message' => 'Invalid confirmation token. The link may have expired or been used already.'
            ]);
        }

        // Check if already processed
        if (!$confirmation->isPending()) {
            $status = $confirmation->isConfirmed() ? 'confirmed' : 'declined';
            $statusDate = $confirmation->confirmed_at ? $confirmation->confirmed_at->format('F j, Y \a\t g:i A') : 'Unknown';
            
            if ($confirmation->isConfirmed()) {
                $message = "This email was already confirmed on {$statusDate}.";
            } else {
                $message = "This request was declined on {$statusDate}.";
            }
            
            return view('asset-confirmation.already-processed', [
                'confirmation' => $confirmation,
                'status' => $status,
                'message' => $message
            ]);
        }

        // Log the decline attempt
        \Log::info('Asset decline attempt', [
            'confirmation_id' => $confirmation->id,
            'asset_tag' => $confirmation->asset->asset_tag,
            'user_email' => $confirmation->user->email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        // Mark confirmation as declined
        $confirmation->markAsDeclined();

        // Create timeline entry for decline
        \App\Models\AssetTimeline::create([
            'asset_id' => $confirmation->asset_id,
            'action' => 'declined',
            'from_user_id' => $confirmation->user_id,
            'to_user_id' => null,
            'from_department_id' => $confirmation->asset->department_id,
            'to_department_id' => null,
            'notes' => 'Asset assignment declined by user via email confirmation',
            'old_values' => null,
            'new_values' => [
                'confirmation_id' => $confirmation->id,
                'confirmation_token' => $confirmation->confirmation_token,
                'declined_at' => now()->toISOString(),
                'user_email' => $confirmation->user->email,
                'user_name' => $confirmation->user->first_name . ' ' . $confirmation->user->last_name
            ],
            'performed_by' => $confirmation->user_id, // Use the actual user who confirmed/declined
            'performed_at' => now()
        ]);

        // Create activity log entry for decline
        \App\Models\Log::create([
            'loggable_type' => 'App\Models\AssetAssignmentConfirmation',
            'loggable_id' => $confirmation->id,
            'category' => 'asset_assignment_confirmation',
            'event_type' => 'declined',
            'description' => 'Asset assignment declined by user via email confirmation',
            'user_id' => $confirmation->user_id,
            'role_id' => $confirmation->user->role_id ?? null,
            'asset_id' => $confirmation->asset_id,
            'department_id' => $confirmation->asset->department_id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'remarks' => 'Asset assignment declined by user via email confirmation',
            'action_details' => [
                'confirmation_id' => $confirmation->id,
                'confirmation_token' => $confirmation->confirmation_token,
                'declined_at' => now()->toISOString(),
                'user_email' => $confirmation->user->email,
                'user_name' => $confirmation->user->first_name . ' ' . $confirmation->user->last_name,
                'asset_name' => $confirmation->asset->name,
                'asset_tag' => $confirmation->asset->asset_tag
            ],
            'session_id' => request()->hasSession() ? request()->session()->getId() : null,
            'request_method' => request()->method(),
            'request_url' => request()->fullUrl(),
            'action_timestamp' => now()
        ]);

        // Update related AssetAssignment status from 'pending' to 'declined'
        \App\Models\AssetAssignment::where('asset_id', $confirmation->asset_id)
            ->where('user_id', $confirmation->user_id)
            ->where('status', 'pending')
            ->update([
                'status' => 'declined',
                'return_date' => now() // Set return date as assignment is rejected
            ]);

        // Update asset status back to Available and unassign
        $confirmation->asset->update([
            'assigned_to' => null,
            'assigned_date' => null,
            'status' => 'Available',
            'movement' => 'Returned'
        ]);

        // Create audit log
        Log::create([
            'category' => 'Asset',
            'asset_id' => $confirmation->asset->id,
            'user_id' => $confirmation->user->id,
            'role_id' => $confirmation->user->role_id ?? 1,
            'department_id' => $confirmation->user->department_id,
            'event_type' => 'declined',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'remarks' => 'Asset assignment declined by user: ' . $confirmation->user->first_name . ' ' . $confirmation->user->last_name . '. Asset returned to available status.',
            'created_at' => now()
        ]);

        // Create notification for the user
        $notificationData = [
            'type' => 'asset_declined',
            'title' => 'Asset Assignment Declined',
            'message' => "You have declined the assignment of asset {$confirmation->asset->asset_tag} ({$confirmation->asset->asset_name}).",
            'data' => [
                'asset_tag' => $confirmation->asset->asset_tag,
                'asset_name' => $confirmation->asset->asset_name,
                'confirmation_id' => $confirmation->id,
                'declined_at' => now()->toISOString()
            ]
        ];
        
        Notification::create(array_merge($notificationData, [
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $confirmation->user_id
        ]));
        
        // Create identical notification for super administrator
        $this->notifySuperAdmin($notificationData);

        // Send notification email to admin
        try {
            Mail::to(config('mail.notification_recipient', 'ict.department@midc.ph'))->send(new AssetConfirmationNotification(
                $confirmation,
                'declined',
                [
                    'confirmation_id' => $confirmation->id,
                    'declined_at' => now()->toISOString(),
                    'user_email' => $confirmation->user->email,
                    'user_name' => $confirmation->user->first_name . ' ' . $confirmation->user->last_name,
                    'asset_name' => $confirmation->asset->name,
                    'asset_tag' => $confirmation->asset->asset_tag,
                    'decline_reason' => $confirmation->getFormattedDeclineReason(),
                    'decline_category' => $confirmation->decline_category ?? null,
                    'decline_severity' => $confirmation->decline_severity ?? null,
                    'follow_up_required' => $confirmation->follow_up_required ?? false
                ]
            ));
        } catch (\Exception $e) {
            // Log error but don't fail the decline
            \Log::error('Failed to send decline notification email: ' . $e->getMessage());
        }

        return view('asset-confirmation.success', [
            'confirmation' => $confirmation,
            'action' => 'declined'
        ]);
    }

    /**
     * Categorize decline reason
     */
    private function categorizeDeclineReason($reason)
    {
        $categories = [
            'delivery_issues' => ['never_delivered', 'delivery_location', 'incomplete_delivery'],
            'asset_issues' => ['wrong_asset', 'damaged_asset', 'incompatible_asset'],
            'personal_reasons' => ['no_longer_needed', 'personal_preference', 'temporary_unavailable'],
            'technical_issues' => ['technical_problems', 'software_incompatibility'],
            'other' => ['other_reason']
        ];

        foreach ($categories as $category => $reasons) {
            if (in_array($reason, $reasons)) {
                return $category;
            }
        }

        return 'other';
    }

    /**
     * Determine severity based on decline reason
     */
    private function determineSeverity($reason)
    {
        $highSeverity = ['never_delivered', 'damaged_asset', 'wrong_asset'];
        $mediumSeverity = ['incomplete_delivery', 'delivery_location', 'technical_problems'];
        
        if (in_array($reason, $highSeverity)) {
            return 'high';
        } elseif (in_array($reason, $mediumSeverity)) {
            return 'medium';
        }
        
        return 'low';
    }

    /**
     * Generate follow-up actions based on decline reason
     */
    private function generateFollowUpActions($reason)
    {
        $actions = [
            'never_delivered' => ['Investigate delivery status', 'Contact delivery team', 'Reschedule delivery'],
            'wrong_asset' => ['Verify asset requirements', 'Arrange correct asset', 'Return incorrect asset'],
            'damaged_asset' => ['Inspect asset condition', 'Arrange repair or replacement', 'Document damage'],
            'incomplete_delivery' => ['Check missing items', 'Complete delivery', 'Update inventory'],
            'delivery_location' => ['Verify delivery address', 'Reschedule delivery', 'Update location preferences']
        ];

        return $actions[$reason] ?? ['Review decline reason', 'Contact user for clarification'];
    }



    /**
     * Show confirmation form with reason (for declines)
     */
    public function showDeclineForm($token)
    {
        $confirmation = AssetAssignmentConfirmation::where('confirmation_token', $token)
            ->with(['asset.assetCategory', 'user'])
            ->first();

        if (!$confirmation || !$confirmation->isPending()) {
            return redirect()->route('asset-confirmation.show', $token);
        }

        return view('asset-confirmation.decline-form', compact('confirmation'));
    }

    /**
     * Process decline with enhanced details
     */
    public function processDecline(Request $request, $token)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'comments' => 'nullable|string|max:1000',
            'contact_preference' => 'nullable|string|in:email,phone,in_person',
            'follow_up_actions' => 'nullable|array',
            'follow_up_actions.*' => 'string|max:255',
            'follow_up_date' => 'nullable|date|after:today',
            'severity' => 'nullable|string|in:low,medium,high'
        ]);

        $confirmation = AssetAssignmentConfirmation::where('confirmation_token', $token)
            ->with(['asset', 'user'])
            ->first();

        if (!$confirmation || !$confirmation->isPending()) {
            return redirect()->route('asset-confirmation.show', $token)
                ->with('error', 'Unable to process decline.');
        }

        // Determine follow-up requirements based on reason
        $followUpRequired = in_array($request->reason, [
            'never_delivered', 'wrong_asset', 'damaged_asset', 
            'incomplete_delivery', 'delivery_location'
        ]);

        // Determine severity based on reason
        $severity = $request->severity ?? $this->determineSeverity($request->reason);

        // Prepare follow-up actions
        $followUpActions = [];
        if ($followUpRequired) {
            $followUpActions = $this->generateFollowUpActions($request->reason);
            if ($request->follow_up_actions) {
                $followUpActions = array_merge($followUpActions, $request->follow_up_actions);
            }
        }

        // Prepare decline data
        $declineData = [
            'decline_category' => $this->categorizeDeclineReason($request->reason),
            'decline_reason' => $request->reason,
            'decline_comments' => $request->comments,
            'contact_preference' => $request->contact_preference ?? 'email',
            'follow_up_required' => $followUpRequired,
            'follow_up_actions' => !empty($followUpActions) ? implode('|', $followUpActions) : null,
            'follow_up_date' => $request->follow_up_date ?? ($followUpRequired ? now()->addDays(3) : null),
            'decline_severity' => $severity
        ];

        // Mark confirmation as declined with enhanced details
        $confirmation->markAsDeclined($declineData);

        // Create timeline entry for decline with reason
        \App\Models\AssetTimeline::create([
            'asset_id' => $confirmation->asset_id,
            'action' => 'declined',
            'from_user_id' => $confirmation->user_id,
            'to_user_id' => null,
            'from_department_id' => $confirmation->asset->department_id,
            'to_department_id' => null,
            'notes' => 'Asset assignment declined by user via email confirmation. Reason: ' . $confirmation->getFormattedDeclineReason(),
            'old_values' => null,
            'new_values' => [
                'confirmation_id' => $confirmation->id,
                'confirmation_token' => $confirmation->confirmation_token,
                'declined_at' => now()->toISOString(),
                'user_email' => $confirmation->user->email,
                'user_name' => $confirmation->user->first_name . ' ' . $confirmation->user->last_name,
                'decline_reason' => $confirmation->getFormattedDeclineReason(),
                'decline_category' => $declineData['decline_category'] ?? null,
                'decline_severity' => $declineData['decline_severity'] ?? null,
                'follow_up_required' => $followUpRequired
            ],
            'performed_by' => $confirmation->user_id, // Use the actual user who confirmed/declined
            'performed_at' => now()
        ]);

        // Create activity log entry for decline with reason
        \App\Models\Log::create([
            'loggable_type' => 'App\Models\AssetAssignmentConfirmation',
            'loggable_id' => $confirmation->id,
            'category' => 'asset_assignment_confirmation',
            'event_type' => 'declined',
            'description' => 'Asset assignment declined by user via email confirmation. Reason: ' . $confirmation->getFormattedDeclineReason(),
            'user_id' => $confirmation->user_id,
            'role_id' => $confirmation->user->role_id ?? null,
            'asset_id' => $confirmation->asset_id,
            'department_id' => $confirmation->asset->department_id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'remarks' => 'Asset assignment declined by user via email confirmation. Reason: ' . $confirmation->getFormattedDeclineReason(),
            'action_details' => [
                'confirmation_id' => $confirmation->id,
                'confirmation_token' => $confirmation->confirmation_token,
                'declined_at' => now()->toISOString(),
                'user_email' => $confirmation->user->email,
                'user_name' => $confirmation->user->first_name . ' ' . $confirmation->user->last_name,
                'asset_name' => $confirmation->asset->name,
                'asset_tag' => $confirmation->asset->asset_tag,
                'decline_reason' => $confirmation->getFormattedDeclineReason(),
                'decline_category' => $declineData['decline_category'] ?? null,
                'decline_severity' => $declineData['decline_severity'] ?? null,
                'follow_up_required' => $followUpRequired
            ],
            'session_id' => request()->hasSession() ? request()->session()->getId() : null,
            'request_method' => request()->method(),
            'request_url' => request()->fullUrl(),
            'action_timestamp' => now()
        ]);

        // Update related AssetAssignment status from 'pending' to 'declined'
        \App\Models\AssetAssignment::where('asset_id', $confirmation->asset_id)
            ->where('user_id', $confirmation->user_id)
            ->where('status', 'pending')
            ->update([
                'status' => 'declined',
                'return_date' => now() // Set return date as assignment is rejected
            ]);

        // Update asset status back to Available and unassign
        $confirmation->asset->update([
            'assigned_to' => null,
            'assigned_date' => null,
            'status' => 'Available',
            'movement' => 'Returned'
        ]);

        // Create enhanced audit log
        $remarks = sprintf(
            'Asset assignment declined by user: %s %s. Reason: %s. Severity: %s. Follow-up required: %s.',
            $confirmation->user->first_name,
            $confirmation->user->last_name,
            $confirmation->getFormattedDeclineReason(),
            ucfirst($severity),
            $followUpRequired ? 'Yes' : 'No'
        );

        if ($request->comments) {
            $remarks .= ' Comments: ' . $request->comments;
        }

        Log::create([
            'category' => 'Asset',
            'asset_id' => $confirmation->asset->id,
            'user_id' => $confirmation->user->id,
            'role_id' => $confirmation->user->role_id ?? 1,
            'department_id' => $confirmation->user->department_id,
            'event_type' => 'declined',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'remarks' => $remarks,
            'created_at' => now()
        ]);

        // Create enhanced notification
        $notificationData = [
            'type' => 'asset_declined',
            'title' => 'Asset Assignment Declined - ' . ucfirst($severity) . ' Priority',
            'message' => sprintf(
                'Asset %s (%s) assignment declined by %s %s. Reason: %s. Follow-up required: %s.',
                $confirmation->asset->asset_tag,
                $confirmation->asset->asset_name,
                $confirmation->user->first_name,
                $confirmation->user->last_name,
                $confirmation->getFormattedDeclineReason(),
                $followUpRequired ? 'Yes' : 'No'
            ),
            'data' => [
                'asset_tag' => $confirmation->asset->asset_tag,
                'asset_name' => $confirmation->asset->asset_name,
                'confirmation_id' => $confirmation->id,
                'declined_at' => now()->toISOString(),
                'decline_reason' => $confirmation->getFormattedDeclineReason(),
                'severity' => $severity,
                'follow_up_required' => $followUpRequired,
                'follow_up_date' => $confirmation->follow_up_date?->toISOString()
            ]
        ];
        
        Notification::create(array_merge($notificationData, [
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $confirmation->user_id
        ]));
        
        // Create identical notification for super administrator
        $this->notifySuperAdmin($notificationData);

        // Send notification email to admin
        try {
            Mail::to('pjcruz@midc.ph')->send(new AssetConfirmationNotification(
                $confirmation,
                'declined',
                [
                    'confirmation_id' => $confirmation->id,
                    'declined_at' => now()->toISOString(),
                    'user_email' => $confirmation->user->email,
                    'user_name' => $confirmation->user->first_name . ' ' . $confirmation->user->last_name,
                    'asset_name' => $confirmation->asset->name,
                    'asset_tag' => $confirmation->asset->asset_tag,
                    'decline_reason' => $confirmation->getFormattedDeclineReason(),
                    'decline_category' => $declineData['decline_category'] ?? null,
                    'decline_severity' => $declineData['decline_severity'] ?? null,
                    'follow_up_required' => $followUpRequired,
                    'decline_comments' => $request->comments,
                    'contact_preference' => $request->contact_preference ?? 'email',
                    'follow_up_actions' => $followUpActions,
                    'follow_up_date' => $request->follow_up_date
                ]
            ));
        } catch (\Exception $e) {
            // Log error but don't fail the decline
            \Log::error('Failed to send decline notification email: ' . $e->getMessage());
        }

        return view('asset-confirmation.success', [
            'confirmation' => $confirmation,
            'action' => 'declined'
        ]);
    }

    /**
     * Send identical notification to super administrator
     */
    private function notifySuperAdmin($notificationData)
    {
        // Find super administrator user
        $superAdmin = User::whereHas('role', function($query) {
            $query->where('name', 'Super Admin');
        })->first();

        if ($superAdmin) {
            Notification::create(array_merge($notificationData, [
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $superAdmin->id
            ]));
        }
    }
}
