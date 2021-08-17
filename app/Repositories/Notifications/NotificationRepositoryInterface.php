<?php

namespace App\Repositories\Notifications;

interface NotificationRepositoryInterface
{
    public function notifications();

    public function unreadCount();

    public function allAsRead();

    public function asRead($notificationId);

    public function loadData($request);
}
