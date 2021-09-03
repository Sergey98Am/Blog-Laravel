<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class UserCreatePost extends Notification
{
    use Queueable;

    public $post_user_name;
    public $post_id;
    public $post_title;
    public $url;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($post_user_name, $post_id, $post_title)
    {
        $this->post_user_name = $post_user_name;
        $this->post_id = $post_id;
        $this->post_title = $post_title;
        $this->url = "/admin/post/$this->post_id";
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $class_name_to_snake_case = Str::snake(class_basename(__CLASS__));
        $url = "$this->url/?notify_id=$this->id&notify_type=$class_name_to_snake_case";

        return [
            'title' => $this->post_title,
            'url' => $url,
            "text" => "$this->post_user_name created new post '$this->post_title'",
            "post_id" => $this->post_id
        ];
    }
}
