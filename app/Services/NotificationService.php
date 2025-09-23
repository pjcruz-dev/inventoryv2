<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Create a new notification.
     */
    public function create(
        string $type,
        string $title,
        string $message,
        Model $notifiable,
        array $data = [],
        bool $isUrgent = false,
        ?string $actionUrl = null,
        ?string $actionText = null,
        ?Carbon $expiresAt = null
    ): Notification {
        return Notification::create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->id,
            'is_urgent' => $isUrgent,
            'action_url' => $actionUrl,
            'action_text' => $actionText,
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Create a notification for asset updates.
     */
    public function createAssetNotification(
        Model $asset,
        string $action,
        User $user,
        array $additionalData = []
    ): Notification {
        $messages = [
            'created' => 'A new asset has been created',
            'updated' => 'Asset information has been updated',
            'assigned' => 'Asset has been assigned to a user',
            'unassigned' => 'Asset has been unassigned',
            'maintenance' => 'Asset has been scheduled for maintenance',
            'disposed' => 'Asset has been disposed of',
            'status_changed' => 'Asset status has been changed',
        ];

        $titles = [
            'created' => 'New Asset Created',
            'updated' => 'Asset Updated',
            'assigned' => 'Asset Assigned',
            'unassigned' => 'Asset Unassigned',
            'maintenance' => 'Maintenance Scheduled',
            'disposed' => 'Asset Disposed',
            'status_changed' => 'Status Changed',
        ];

        $message = $messages[$action] ?? 'Asset has been modified';
        $title = $titles[$action] ?? 'Asset Update';

        return $this->create(
            'asset_update',
            $title,
            "{$message}: {$asset->name} ({$asset->asset_tag})",
            $user,
            array_merge([
                'asset_id' => $asset->id,
                'asset_name' => $asset->name,
                'asset_tag' => $asset->asset_tag,
                'action' => $action,
            ], $additionalData),
            false,
            route('assets.show', $asset->id),
            'View Asset'
        );
    }

    /**
     * Create a notification for user actions.
     */
    public function createUserNotification(
        User $user,
        string $action,
        array $additionalData = []
    ): Notification {
        $messages = [
            'login' => 'User has logged in',
            'logout' => 'User has logged out',
            'profile_updated' => 'User profile has been updated',
            'password_changed' => 'User password has been changed',
            'role_changed' => 'User role has been changed',
        ];

        $titles = [
            'login' => 'User Login',
            'logout' => 'User Logout',
            'profile_updated' => 'Profile Updated',
            'password_changed' => 'Password Changed',
            'role_changed' => 'Role Changed',
        ];

        $message = $messages[$action] ?? 'User action performed';
        $title = $titles[$action] ?? 'User Action';

        return $this->create(
            'user_action',
            $title,
            "{$message}: {$user->name}",
            $user,
            array_merge([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'action' => $action,
            ], $additionalData)
        );
    }

    /**
     * Create a system notification.
     */
    public function createSystemNotification(
        string $title,
        string $message,
        User $user,
        string $type = 'info',
        bool $isUrgent = false,
        ?string $actionUrl = null,
        ?string $actionText = null
    ): Notification {
        return $this->create(
            $type,
            $title,
            $message,
            $user,
            [],
            $isUrgent,
            $actionUrl,
            $actionText
        );
    }

    /**
     * Create a maintenance notification.
     */
    public function createMaintenanceNotification(
        string $title,
        string $message,
        User $user,
        ?Carbon $scheduledAt = null,
        bool $isUrgent = false
    ): Notification {
        return $this->create(
            'maintenance',
            $title,
            $message,
            $user,
            [
                'scheduled_at' => $scheduledAt?->toISOString(),
            ],
            $isUrgent,
            null,
            null,
            $scheduledAt
        );
    }

    /**
     * Get notifications for a user.
     */
    public function getNotificationsForUser(
        User $user,
        int $limit = 50,
        bool $unreadOnly = false
    ): Collection {
        $query = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->notExpired()
            ->orderBy('created_at', 'desc');

        if ($unreadOnly) {
            $query->unread();
        }

        return $query->limit($limit)->get();
    }

    /**
     * Get unread notification count for a user.
     */
    public function getUnreadCountForUser(User $user): int
    {
        return Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->unread()
            ->notExpired()
            ->count();
    }

    /**
     * Mark notifications as read.
     */
    public function markAsRead(array $notificationIds, User $user): int
    {
        return Notification::whereIn('id', $notificationIds)
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->update(['read_at' => now()]);
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(User $user): int
    {
        return Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->unread()
            ->update(['read_at' => now()]);
    }

    /**
     * Delete old notifications.
     */
    public function cleanupOldNotifications(int $days = 30): int
    {
        return Notification::where('created_at', '<', now()->subDays($days))
            ->delete();
    }

    /**
     * Delete expired notifications.
     */
    public function cleanupExpiredNotifications(): int
    {
        return Notification::where('expires_at', '<', now())
            ->delete();
    }

    /**
     * Get notification statistics for a user.
     */
    public function getNotificationStats(User $user): array
    {
        $total = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->notExpired()
            ->count();

        $unread = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->unread()
            ->notExpired()
            ->count();

        $urgent = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->urgent()
            ->unread()
            ->notExpired()
            ->count();

        return [
            'total' => $total,
            'unread' => $unread,
            'urgent' => $urgent,
            'read' => $total - $unread,
        ];
    }
}
