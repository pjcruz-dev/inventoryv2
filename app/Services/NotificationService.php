<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Send notification to user
     */
    public function sendNotification($userId, $type, $title, $message, $data = [])
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                Log::error("User not found for notification: {$userId}");
                return false;
            }

            // Create notification record
            $notification = Notification::create([
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
                'read_at' => null
            ]);

            // Send email notification if user has email
            if ($user->email) {
                $this->sendEmailNotification($user, $notification);
            }

            // Log the notification
            AuditLog::create([
                'user_id' => $userId,
                'action' => 'notification_sent',
                'model_type' => 'App\Models\Notification',
                'model_id' => $notification->id,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'System Notification',
                'details' => [
                    'notification_id' => $notification->id,
                    'type' => $type,
                    'title' => $title
                ],
                'timestamp' => now()
            ]);

            Log::info("Notification sent to user {$userId}: {$title}");
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to send notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send bulk notifications
     */
    public function sendBulkNotifications($userIds, $type, $title, $message, $data = [])
    {
        $successCount = 0;
        $failureCount = 0;

        foreach ($userIds as $userId) {
            if ($this->sendNotification($userId, $type, $title, $message, $data)) {
                $successCount++;
            } else {
                $failureCount++;
            }
        }

        Log::info("Bulk notification sent: {$successCount} success, {$failureCount} failures");
        return ['success' => $successCount, 'failures' => $failureCount];
    }

    /**
     * Send notification to all users
     */
    public function sendSystemNotification($type, $title, $message, $data = [])
    {
        $userIds = User::pluck('id')->toArray();
        return $this->sendBulkNotifications($userIds, $type, $title, $message, $data);
    }

    /**
     * Send maintenance reminder
     */
    public function sendMaintenanceReminder($maintenanceId)
    {
        $maintenance = \App\Models\Maintenance::with(['asset', 'assignedTo'])->find($maintenanceId);
        
        if (!$maintenance || !$maintenance->assignedTo) {
            return false;
        }

        $title = "Maintenance Reminder: {$maintenance->title}";
        $message = "Maintenance for asset '{$maintenance->asset->name}' is due on {$maintenance->scheduled_date->format('M d, Y')}.";
        
        $data = [
            'maintenance_id' => $maintenance->id,
            'asset_id' => $maintenance->asset_id,
            'scheduled_date' => $maintenance->scheduled_date,
            'priority' => $maintenance->priority
        ];

        return $this->sendNotification(
            $maintenance->assigned_to,
            'maintenance_reminder',
            $title,
            $message,
            $data
        );
    }

    /**
     * Send warranty expiry warning
     */
    public function sendWarrantyExpiryWarning($assetId)
    {
        $asset = \App\Models\Asset::find($assetId);
        
        if (!$asset) {
            return false;
        }

        $title = "Warranty Expiry Warning: {$asset->name}";
        $message = "The warranty for asset '{$asset->name}' will expire on {$asset->warranty_end_date->format('M d, Y')}.";
        
        $data = [
            'asset_id' => $asset->id,
            'warranty_end_date' => $asset->warranty_end_date,
            'days_remaining' => now()->diffInDays($asset->warranty_end_date, false)
        ];

        // Send to asset owner if assigned
        if ($asset->assigned_to) {
            return $this->sendNotification(
                $asset->assigned_to,
                'warranty_expiry',
                $title,
                $message,
                $data
            );
        }

        // Send to all admins if no specific owner
        $adminIds = User::role(['admin', 'super-admin'])->pluck('id')->toArray();
        return $this->sendBulkNotifications($adminIds, 'warranty_expiry', $title, $message, $data);
    }

    /**
     * Send security alert
     */
    public function sendSecurityAlert($type, $title, $message, $data = [])
    {
        $adminIds = User::role(['admin', 'super-admin'])->pluck('id')->toArray();
        
        return $this->sendBulkNotifications(
            $adminIds,
            'security_alert',
            $title,
            $message,
            $data
        );
    }

    /**
     * Send system maintenance notification
     */
    public function sendSystemMaintenanceNotification($title, $message, $scheduledTime = null)
    {
        $data = [
            'scheduled_time' => $scheduledTime,
            'maintenance_type' => 'system'
        ];

        return $this->sendSystemNotification(
            'system_maintenance',
            $title,
            $message,
            $data
        );
    }

    /**
     * Send email notification
     */
    private function sendEmailNotification($user, $notification)
    {
        try {
            Mail::send('emails.notification', [
                'user' => $user,
                'notification' => $notification
            ], function ($message) use ($user, $notification) {
                $message->to($user->email, $user->name)
                    ->subject($notification->title);
            });

            Log::info("Email notification sent to {$user->email}");
        } catch (\Exception $e) {
            Log::error("Failed to send email notification: " . $e->getMessage());
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId)
    {
        $notification = Notification::where('id', $notificationId)
            ->where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $userId)
            ->first();

        if ($notification && !$notification->read_at) {
            $notification->update(['read_at' => now()]);
            
            // Log the read action
            AuditLog::create([
                'user_id' => $userId,
                'action' => 'notification_read',
                'model_type' => 'App\Models\Notification',
                'model_id' => $notificationId,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'details' => [
                    'notification_id' => $notificationId,
                    'type' => $notification->type
                ],
                'timestamp' => now()
            ]);

            return true;
        }

        return false;
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead($userId)
    {
        $updated = Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if ($updated > 0) {
            // Log the bulk read action
            AuditLog::create([
                'user_id' => $userId,
                'action' => 'notifications_marked_read',
                'model_type' => 'App\Models\Notification',
                'model_id' => null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'details' => [
                    'count' => $updated
                ],
                'timestamp' => now()
            ]);
        }

        return $updated;
    }

    /**
     * Get unread notifications for user
     */
    public function getUnreadNotifications($userId, $limit = 10)
    {
        return Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get notification count for user
     */
    public function getNotificationCount($userId)
    {
        return Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Clean up old notifications
     */
    public function cleanupOldNotifications($days = 30)
    {
        $cutoffDate = now()->subDays($days);
        $deletedCount = Notification::where('created_at', '<', $cutoffDate)->delete();
        
        Log::info("Cleaned up {$deletedCount} old notifications");
        return $deletedCount;
    }
}
