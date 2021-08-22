<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CheckPost extends Notification
{
    use Queueable;

    public $post_id;
    public $post_title;
    public $post_checked;
    public $checked;
    public $url;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($post_id, $post_title, $post_checked)
    {
        $this->post_id = $post_id;
        $this->post_title = $post_title;
        $this->post_checked = $post_checked;
        $this->checked = $post_checked ? 'post-checked' : 'post-not-checked';
        $this->url = "post_id/$this->post_id?checked=$this->checked";
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return string[]
     */
    public function toArray($notifiable)
    {
        $url = "/notification/notify_id/$this->id/$this->url";

        return [
            "url" => $url,
            "text" => $this->post_checked ? "Admin approved '$this->post_title' post" : "Admin disapproved '$this->post_title' post",
        ];
    }
}