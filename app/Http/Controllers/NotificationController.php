<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * Get unread notifications for the authenticated user
     */
    public function getUnread(): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['notifications' => [], 'count' => 0]);
        }

        $notifications = Notification::where('user_id', auth()->id())
            ->unread()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'count' => $notifications->count()
        ]);
    }

    /**
     * Get all notifications for the authenticated user
     */
    public function getAll(): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['notifications' => []]);
        }

        $notifications = Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($notifications);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Request $request, $id): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $notification = Notification::where('user_id', auth()->id())
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return response()->json(['error' => 'Notification not found'], 404);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        Notification::where('user_id', auth()->id())
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Delete a notification
     */
    public function delete($id): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $notification = Notification::where('user_id', auth()->id())
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return response()->json(['error' => 'Notification not found'], 404);
        }

        $notification->delete();

        return response()->json(['success' => true]);
    }
}
