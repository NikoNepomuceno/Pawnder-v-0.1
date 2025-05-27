<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Post;
use App\Models\PostComment;
use Illuminate\Support\Facades\Auth;

class PostComments extends Component
{
    public $post;
    public $comments;
    public $newComment = '';
    public $commentCount = 0;
    public $editingCommentId = null;
    public $editCommentContent = '';
    public $replyingToCommentId = null;
    public $replyContent = '';

    protected $listeners = ['refreshComments' => 'loadComments'];

    public function mount(Post $post)
    {
        $this->post = $post;
        $this->loadComments();
    }

    public function loadComments()
    {
        $this->comments = $this->post->comments()
            ->with(['user', 'replies.user'])
            ->latest()
            ->get();

        $this->updateCommentCount();
    }

    protected function updateCommentCount()
    {
        $this->commentCount = $this->post->allComments()->count();
        // Emit event to update count in parent view
        $this->dispatch('commentCountUpdated', postId: $this->post->id, count: $this->commentCount);
    }

    public function addComment()
    {
        $this->validate([
            'newComment' => 'required|min:1|max:1000'
        ]);

        $comment = new PostComment([
            'content' => $this->newComment,
            'user_id' => Auth::id(),
        ]);

        $this->post->comments()->save($comment);

        // Notify post owner if not commenting on own post
        if ($this->post->user_id !== Auth::id()) {
            $notification = new \App\Notifications\NewComment($this->post, $comment);
            $notification->commenterUsername = Auth::user()->username;
            $notification->commenterId = Auth::id();
            $this->post->user->notify($notification);
        }

        $this->newComment = '';
        $this->loadComments();
        $this->updateCommentCount();
    }

    public function startEdit($commentId)
    {
        $comment = PostComment::find($commentId);
        if ($comment && $comment->user_id === Auth::id()) {
            $this->editingCommentId = $commentId;
            $this->editCommentContent = $comment->content;
        }
    }

    public function cancelEdit()
    {
        $this->editingCommentId = null;
        $this->editCommentContent = '';
    }

    public function updateComment()
    {
        $this->validate([
            'editCommentContent' => 'required|min:1|max:1000',
        ]);
        $comment = PostComment::find($this->editingCommentId);
        if ($comment && $comment->user_id === Auth::id()) {
            $comment->content = $this->editCommentContent;
            $comment->save();
            $this->editingCommentId = null;
            $this->editCommentContent = '';
            $this->loadComments();
            $this->updateCommentCount();
        }
    }

    public function startReply($commentId)
    {
        $this->replyingToCommentId = $commentId;
        $this->replyContent = '';
    }

    public function cancelReply()
    {
        $this->replyingToCommentId = null;
        $this->replyContent = '';
    }

    public function addReply()
    {
        $this->validate([
            'replyContent' => 'required|min:1|max:1000',
        ]);
        $parentComment = PostComment::find($this->replyingToCommentId);
        if ($parentComment) {
            $reply = new PostComment([
                'content' => $this->replyContent,
                'user_id' => Auth::id(),
                'parent_id' => $parentComment->id,
            ]);
            $this->post->comments()->save($reply);
            $this->replyingToCommentId = null;
            $this->replyContent = '';
            $this->loadComments();
            $this->updateCommentCount();
        }
    }

    public function deleteComment($commentId)
    {
        $comment = PostComment::find($commentId);
        if ($comment && $comment->user_id === Auth::id()) {
            $comment->delete();
            $this->loadComments();
            $this->updateCommentCount();
        }
    }

    public function render()
    {
        return view('livewire.post-comments');
    }
}
