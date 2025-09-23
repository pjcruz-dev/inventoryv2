<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display the notifications page.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $notifications = $this->notificationService->getNotificationsForUser($user, 50);
        $stats = $this->notificationService->getNotificationStats($user);

        return view('notifications.index', compact('notifications', 'stats'));
    }

    /**
     * Get notifications for the current user (API).
     */
    public function getNotifications(Request $request): JsonResponse
    {
        $user = $request->user();
        $unreadOnly = $request->boolean('unread_only', false);
        $limit = $request->integer('limit', 20);

        $notifications = $this->notificationService->getNotificationsForUser(
            $user,
            $limit,
            $unreadOnly
        );

        return response()->json([
            'success' => true,
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'icon' => $notification->icon,
                    'color' => $notification->color,
                    'is_read' => $notification->isRead(),
                    'is_urgent' => $notification->is_urgent,
                    'action_url' => $notification->action_url,
                    'action_text' => $notification->action_text,
                    'time_ago' => $notification->time_ago,
                    'created_at' => $notification->created_at->toISOString(),
                ];
            }),
            'unread_count' => $this->notificationService->getUnreadCountForUser($user),
        ]);
    }

    /**
     * Get notification statistics.
     */
    public function getStats(Request $request): JsonResponse
    {
        $user = $request->user();
        $stats = $this->notificationService->getNotificationStats($user);

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Mark notifications as read.
     */
    public function markAsRead(Request $request): JsonResponse
    {
        $request->validate([
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'integer|exists:notifications,id',
        ]);

        $user = $request->user();
        $count = $this->notificationService->markAsRead(
            $request->notification_ids,
            $user
        );

        return response()->json([
            'success' => true,
            'message' => "Marked {$count} notifications as read",
            'unread_count' => $this->notificationService->getUnreadCountForUser($user),
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();
        $count = $this->notificationService->markAllAsRead($user);

        return response()->json([
            'success' => true,
            'message' => "Marked {$count} notifications as read",
            'unread_count' => 0,
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsReadSingle(Request $request, Notification $notification): JsonResponse
    {
        $user = $request->user();

        // Verify the notification belongs to the user
        if ($notification->notifiable_type !== get_class($user) || 
            $notification->notifiable_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found',
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
            'unread_count' => $this->notificationService->getUnreadCountForUser($user),
        ]);
    }

    /**
     * Delete a notification.
     */
    public function delete(Request $request, Notification $notification): JsonResponse
    {
        $user = $request->user();

        // Verify the notification belongs to the user
        if ($notification->notifiable_type !== get_class($user) || 
            $notification->notifiable_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found',
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted',
            'unread_count' => $this->notificationService->getUnreadCountForUser($user),
        ]);
    }

    /**
     * Delete all read notifications.
     */
    public function deleteRead(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $count = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->read()
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Deleted {$count} read notifications",
            'unread_count' => $this->notificationService->getUnreadCountForUser($user),
        ]);
    }

    /**
     * Get notification dropdown data for the header.
     */
    public function getDropdownData(Request $request): JsonResponse
    {
        $user = $request->user();
        $notifications = $this->notificationService->getNotificationsForUser($user, 10, true);
        $unreadCount = $this->notificationService->getUnreadCountForUser($user);

        return response()->json([
            'success' => true,
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'icon' => $notification->icon,
                    'color' => $notification->color,
                    'is_urgent' => $notification->is_urgent,
                    'action_url' => $notification->action_url,
                    'action_text' => $notification->action_text,
                    'time_ago' => $notification->time_ago,
                ];
            }),
            'unread_count' => $unreadCount,
        ]);
    }
}