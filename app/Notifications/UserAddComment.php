<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class UserAddComment extends Notification
{
    use Queueable;

    public $comment_user_name;
    public $post_id;
    public $comment_post_title;
    public $comment_id;
    public $comment_text;
    public $url;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($comment_user_name, $post_id, $comment_post_title, $comment_id, $comment_text, $url)
    {
        $this->comment_user_name = $comment_user_name;
        $this->post_id = $post_id;
        $this->comment_post_title = $comment_post_title;
        $this->comment_id = $comment_id;
        $this->comment_text = $comment_text;
        $this->url = $url;
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
        $url = "$this->url/?comment_id=$this->comment_id&notify_id=$this->id&notify_type=$class_name_to_snake_case";
        $truncated_comment_text =  substr($this->comment_text,0,10);
        $multi_point = strlen($this->comment_text) > 10 ? "..." : "";

        return [
            'comment_text' => $this->comment_text,
            'url' => $url,
            "text" => "$this->comment_user_name created new comment '$truncated_comment_text.$multi_point' on '$this->comment_post_title' post",
            "post_id" => $this->post_id,
            "comment_id" => $this->comment_id
        ];
    }
}
