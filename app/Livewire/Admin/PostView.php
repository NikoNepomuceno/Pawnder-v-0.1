<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class PostView extends Component
{
    public $post;

    public function mount($post)
    {
        $this->post = $post;
    }

    public function render()
    {
        return view('livewire.admin.post-view');
    }
}
