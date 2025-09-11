<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssetAssignmentConfirmation;
use App\Models\Asset;
use App\Models\Log;
use App\Models\Notification;
use App\Models\User;

class AssetConfirmationController extends Controller
{
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

        // Update asset status and movement
        $confirmation->asset->update([
            'status' => 'Active',
            'movement' => 'Deployed Tagged'
        ]);

        // Create audit log
        Log::create([
            'category' => 'Asset',
            'asset_id' => $confirmation->asset->id,
            'user_id' => $confirmation->user->id,
            'role_id' => $confirmation->user->role_id ?? 1,
            'department_id' => $confirmation->user->department_id,
            'event_type' => 'confirmed',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'remarks' => 'Asset assignment confirmed by user: ' . $confirmation->user->first_name . ' ' . $confirmation->user->last_name . '. Status changed from Pending Confirmation to Active.',
            'created_at' => now()
        ]);

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
        
        Notification::create(array_merge($notificationData, ['user_id' => $confirmation->user_id]));
        
        // Create identical notification for super administrator
        $this->notifySuperAdmin($notificationData);

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

        // Update asset status back to Active and unassign
        $confirmation->asset->update([
            'assigned_to' => null,
            'assigned_date' => null,
            'status' => 'Active',
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
        
        Notification::create(array_merge($notificationData, ['user_id' => $confirmation->user_id]));
        
        // Create identical notification for super administrator
        $this->notifySuperAdmin($notificationData);

        return view('asset-confirmation.success', [
            'confirmation' => $confirmation,
            'action' => 'declined'
        ]);
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
     * Process decline with reason
     */
    public function processDecline(Request $request, $token)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $confirmation = AssetAssignmentConfirmation::where('confirmation_token', $token)
            ->with(['asset', 'user'])
            ->first();

        if (!$confirmation || !$confirmation->isPending()) {
            return redirect()->route('asset-confirmation.show', $token)
                ->with('error', 'Unable to process decline.');
        }

        // Mark confirmation as declined with reason
        $confirmation->update([
            'status' => 'declined',
            'confirmed_at' => now(),
            'notes' => 'Declined by user. Reason: ' . $request->reason
        ]);

        // Update asset status back to Active and unassign
        $confirmation->asset->update([
            'assigned_to' => null,
            'assigned_date' => null,
            'status' => 'Active',
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
            'remarks' => 'Asset assignment declined by user: ' . $confirmation->user->first_name . ' ' . $confirmation->user->last_name . '. Reason: ' . $request->reason . '. Asset returned to available status.',
            'created_at' => now()
        ]);

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
            Notification::create(array_merge($notificationData, ['user_id' => $superAdmin->id]));
        }
    }
}
