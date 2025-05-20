<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostShared extends Notification
{
    use Queueable;

    public $sharerUsername;
    public $sharerId;
    protected $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => '<strong>' . $this->sharerUsername . '</strong> Shared your post ' . $this->post->title,
            'post_id' => $this->post->id,
            'user_id' => $this->sharerId,
            'type' => 'share'
        ];
    }
}
