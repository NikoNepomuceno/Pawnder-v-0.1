<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\PostComment;
use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewComment extends Notification
{
    use Queueable;

    public $commenterUsername;
    public $commenterId;
    protected $post;
    protected $comment;

    public function __construct(Post $post, PostComment $comment)
    {
        $this->post = $post;
        $this->comment = $comment;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => '<strong>' . $this->commenterUsername . '</strong> commented on your post ' . $this->post->pet_name,
            'post_id' => $this->post->id,
            'comment_id' => $this->comment->id,
            'user_id' => $this->commenterId,
            'type' => 'comment'
        ];
    }
}
