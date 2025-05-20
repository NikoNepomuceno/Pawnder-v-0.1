<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\PostService;
use App\Http\Requests\PostRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Notifications\PostShared;
use Illuminate\Support\Facades\Log;
use Livewire\Livewire;

/**
 * Handles post-related operations including CRUD, sharing, and filtering.
 */
class PostController extends Controller
{
    use AuthorizesRequests;

    /**
     * The post service instance.
     *
     * @var \App\Services\PostService
     */
    protected PostService $postService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\PostService  $postService
     * @return void
     */
    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Display a listing of posts with optional filtering.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $query = Post::with(['user', 'originalPost.user'])->withCount('comments');

        // Category filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(breed) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(location) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(contact) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"]);
            });
        }

        $posts = $query->latest()->get();
        return view('home', compact('posts'));
    }

    /**
     * Store a newly created post.
     *
     * @param  \App\Http\Requests\PostRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PostRequest $request): RedirectResponse
    {
        try {
            $this->postService->createPost($request->validated());
            return redirect()->route('home')->with('success', 'Post created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create post: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified post.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\View\View
     */
    public function show(Post $post): View
    {
        return view('posts.show', compact('post'));
    }

    /**
     * Update the specified post.
     *
     * @param  \App\Http\Requests\PostRequest  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PostRequest $request, Post $post): JsonResponse
    {
        $this->authorize('update', $post);
        $this->postService->updatePost($post, $request->validated());
        return response()->json(['success' => true, 'message' => 'Post updated successfully!']);
    }

    /**
     * Remove the specified post.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Post $post): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $post);
        $this->postService->deletePost($post);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Post deleted successfully!']);
        }

        return redirect()->route('home')->with('success', 'Post deleted successfully!');
    }

    /**
     * Share a post and increment its share count.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function share(Request $request, Post $post): JsonResponse
    {
        $shareCount = $this->postService->sharePost($post);
        return response()->json([
            'success' => true,
            'share_count' => $shareCount
        ]);
    }

    /**
     * Share a post in-app by creating a new post referencing the original.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function shareInApp(Request $request, Post $post): JsonResponse
    {
        try {
            $result = $this->postService->shareInApp($post);
            return response()->json([
                'success' => true,
                'shared_post_id' => $result['shared_post_id'],
                'share_count' => $result['share_count'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
