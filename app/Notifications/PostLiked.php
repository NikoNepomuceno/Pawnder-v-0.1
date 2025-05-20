<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostLiked extends Notification
{
    use Queueable;

    public $likerUsername;
    public $likerId;
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
            'message' => '<strong>' . $this->likerUsername . '</strong> liked your post ' . $this->post->pet_name,
            'post_id' => $this->post->id,
            'user_id' => $this->likerId,
            'type' => 'like'
        ];
    }
}
