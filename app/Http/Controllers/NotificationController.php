<?php

namespace App\Http\Controllers;

use App\Repositories\Notifications\NotificationRepository;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    private $repository;

    public function __construct(NotificationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function userNotifications()
    {
        try {
            $notifications = $this->repository->notifications();

            return response()->json([
                'notifications' => $notifications
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function unreadNotificationsCount()
    {
        try {
            $count = $this->repository->unreadCount();

            return response()->json([
                'count' => $count
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function markAllAsRead()
    {
        try {
            $this->repository->allAsRead();

            return response()->json([
                'message' => 'Mark all as read'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function markAsRead($notificationId)
    {
        try {
            $this->repository->asRead($notificationId);

            return response()->json([
                'message' => 'Mark as read'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function loadMoreData(Request $request)
    {
        try {
            $moreNotifications = $this->repository->loadData($request);

            return response()->json([
                'notifications' => $moreNotifications
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
