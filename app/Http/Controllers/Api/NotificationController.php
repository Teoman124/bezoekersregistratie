<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(15);

        return response()->json($notifications);
    }

    /**
     * Get only unread notifications for the authenticated user.
     */
    public function unread(Request $request): JsonResponse
    {
        $unreadNotifications = $request->user()
            ->unreadNotifications()
            ->latest()
            ->get();

        return response()->json([
            'count' => count($unreadNotifications),
            'notifications' => $unreadNotifications,
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read',
            'notification' => $notification,
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()
            ->unreadNotifications()
            ->update(['read_at' => now()]);

        return response()->json([
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()
            ->notifications()
            ->findOrFail($id);

        $notification->delete();

        return response()->json([
            'message' => 'Notification deleted',
        ]);
    }

    /**
     * Delete all notifications.
     */
    public function destroyAll(Request $request): JsonResponse
    {
        $request->user()
            ->notifications()
            ->delete();

        return response()->json([
            'message' => 'All notifications deleted',
        ]);
    }
}
