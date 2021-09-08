<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class UserAddReply extends Notification
{
    use Queueable;

    public $comment_user_name;
    public $comment_post_id;
    public $comment_parent_id;
    public $comment_id;
    public $parent_comment_text;
    public $url;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($comment_user_name, $comment_post_id, $comment_parent_id, $comment_id, $parent_comment_text, $url)
    {
        $this->comment_user_name = $comment_user_name;
        $this->comment_post_id = $comment_post_id;
        $this->comment_parent_id = $comment_parent_id;
        $this->comment_id = $comment_id;
        $this->parent_comment_text = $parent_comment_text;
        $this->url = $url;
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
     * @return array
     */
    public function toArray($notifiable)
    {
        $class_name_to_snake_case = Str::snake(class_basename(__CLASS__));
        $url = "$this->url/?comment_id=$this->comment_parent_id&reply_id=$this->comment_id&notify_id=$this->id&notify_type=$class_name_to_snake_case";
        $truncated_comment_text = substr($this->parent_comment_text, 0, 10);
        $multi_point = strlen($this->parent_comment_text) > 10 ? "..." : "";

        return [
            'parent_comment_text' => $this->parent_comment_text,
            'url' => $url,
            "text" => "$this->comment_user_name replied to the '$truncated_comment_text.$multi_point' comment",
            "comment_id" => $this->comment_id
        ];
    }
}
