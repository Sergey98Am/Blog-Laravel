<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Channels\DatabaseChannel as IlluminateDatabaseChannel;

class DatabaseChannel extends IlluminateDatabaseChannel
{
    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return array
     */

    public function buildPayload($notifiable, Notification $notification)
    {
        return [
            'uuid' => $notification->id,
            'type' => get_class($notification),
            'user_id' => $notifiable->id,
            'post_id' => $notification->post_id ?? null,
            'comment_id' => $notification->comment_id ?? null,
            'data' => $this->getData($notifiable, $notification),
            'read_at' => null
        ];
    }
}
