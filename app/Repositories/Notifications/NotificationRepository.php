<?php

namespace App\Repositories\Notifications;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class NotificationRepository implements NotificationRepositoryInterface
{
    public $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    public function notifications(): Collection
    {
        $notifications = $this->user->notifications()->orderBy('id', 'DESC')->limit(10)->get();

        return $notifications;
    }

    public function unreadCount(): int
    {
        $count = $this->user->unreadNotifications->count();

        return $count;
    }

    public function allAsRead()
    {
        $this->user->unreadNotifications->markAsread();
    }

    public function asRead($notificationId)
    {
        $unreadNotifications = $this->user->unreadNotifications;
        $unreadNotifications->where('uuid', $notificationId)->markAsRead();
    }

    public function loadData($request)
    {
        $notification = $this->user->notifications()->where('uuid', $request->id)->first();

        if ($notification) {
            $moreNotifications = $this->user->notifications()->where('id', '<', $notification->id)->orderBy('id', 'DESC')->limit(10)->get();
        }

        return $moreNotifications ?? null;
    }
}
