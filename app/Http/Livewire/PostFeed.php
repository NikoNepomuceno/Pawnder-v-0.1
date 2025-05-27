<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;

class PostFeed extends Component
{
    use WithPagination;

    protected $listeners = ['refreshPost' => 'refreshPostData', 'refresh' => '$refresh'];

    public function refreshPostData($postId)
    {
        // Trigger a full re-render of the component
        $this->emitSelf('refresh');
    }

    public function render()
    {
        $posts = Post::with([
            'user:id,name,profile_picture',
            'originalPost.user:id,name,profile_picture',
            'sharedBy:id,name,profile_picture',
        ])
            ->withCount('comments')
            ->latest()
            ->paginate(10);

        return view('livewire.post-feed', [
            'posts' => $posts,
        ]);
    }
}
