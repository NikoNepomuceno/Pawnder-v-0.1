<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;

class PostFeed extends Component
{
    use WithPagination;

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
