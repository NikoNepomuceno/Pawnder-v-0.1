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

        // Exclude posts created by the current user
        $currentUserId = Auth::id();
        $query->where('user_id', '!=', $currentUserId);

        // Only show valid posts (not deleted and with valid shared posts)
        $query->valid();

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Combined Pet type and Breed filtering logic
        if ($request->filled('pet_type') || $request->filled('breed_filter')) {
            $petType = $request->filled('pet_type') ? strtolower($request->pet_type) : null;
            $breedFilter = $request->filled('breed_filter') ? strtolower($request->breed_filter) : null;

            // Check if breed filter is an "All [Animal] Breeds" selection
            if ($breedFilter && preg_match('/^all_(\w+)_breeds$/', $breedFilter, $matches)) {
                $animalType = $matches[1];
                $allBreeds = $this->getAllBreedsForAnimalType($animalType);

                if (!empty($allBreeds)) {
                    // Filter by specific animal type breeds only (breed field only)
                    $query->where(function ($q) use ($allBreeds) {
                        foreach ($allBreeds as $index => $breed) {
                            if ($index === 0) {
                                $q->whereRaw('LOWER(breed) LIKE ?', ["%{$breed}%"]);
                            } else {
                                $q->orWhereRaw('LOWER(breed) LIKE ?', ["%{$breed}%"]);
                            }
                        }
                    });
                }
            } elseif ($breedFilter) {
                // Specific breed filter (search in breed field only for precision)
                $query->whereRaw('LOWER(breed) LIKE ?', ["%{$breedFilter}%"]);
            } elseif ($petType) {
                // Pet type only (search across breed, title, and description)
                $query->where(function ($q) use ($petType) {
                    $q->whereRaw('LOWER(breed) LIKE ?', ["%{$petType}%"])
                        ->orWhereRaw('LOWER(title) LIKE ?', ["%{$petType}%"])
                        ->orWhereRaw('LOWER(description) LIKE ?', ["%{$petType}%"]);
                });
            }
        }

        // Search filter
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(breed) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(location) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(mobile_number) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"])
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
            return response()->json(['success' => true, 'message' => 'Post moved to trash successfully!']);
        }

        return redirect()->route('home')->with('success', 'Post moved to trash successfully!');
    }

    /**
     * Display the user's deleted posts (trash).
     *
     * @return \Illuminate\View\View
     */
    public function trash(): View
    {
        $deletedPosts = Post::onlyTrashed()
            ->where('user_id', Auth::id())
            ->with(['user', 'originalPost.user'])
            ->withCount('comments')
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('trash.index', compact('deletedPosts'));
    }

    /**
     * Restore a deleted post from trash.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function restore(int $id): JsonResponse|RedirectResponse
    {
        $post = Post::onlyTrashed()->where('id', $id)->where('user_id', Auth::id())->first();

        if (!$post) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Post not found in trash.'], 404);
            }
            return redirect()->route('trash.index')->with('error', 'Post not found in trash.');
        }

        try {
            $post->restore();

            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Post restored successfully!']);
            }

            return redirect()->route('trash.index')->with('success', 'Post restored successfully!');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to restore post.'], 500);
            }
            return redirect()->route('trash.index')->with('error', 'Failed to restore post.');
        }
    }

    /**
     * Permanently delete a post from trash.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function forceDelete(int $id): JsonResponse|RedirectResponse
    {
        $post = Post::onlyTrashed()->where('id', $id)->where('user_id', Auth::id())->first();

        if (!$post) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Post not found in trash.'], 404);
            }
            return redirect()->route('trash.index')->with('error', 'Post not found in trash.');
        }

        try {
            $post->forceDelete();

            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Post permanently deleted!']);
            }

            return redirect()->route('trash.index')->with('success', 'Post permanently deleted!');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to permanently delete post.'], 500);
            }
            return redirect()->route('trash.index')->with('error', 'Failed to permanently delete post.');
        }
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
            // Check if user is trying to share their own post
            if ($post->user_id === Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot share your own post'
                ], 400);
            }

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

    /**
     * Get the count of deleted posts for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTrashCount(): JsonResponse
    {
        $count = Post::onlyTrashed()
            ->where('user_id', Auth::id())
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get all breeds for a specific animal type.
     *
     * @param  string  $animalType
     * @return array
     */
    private function getAllBreedsForAnimalType(string $animalType): array
    {
        $breedData = [
            'dog' => [
                'golden retriever', 'labrador', 'german shepherd', 'bulldog', 'poodle',
                'beagle', 'rottweiler', 'yorkshire terrier', 'dachshund', 'siberian husky',
                'shih tzu', 'chihuahua', 'border collie', 'boxer', 'cocker spaniel'
            ],
            'cat' => [
                'persian', 'siamese', 'maine coon', 'british shorthair', 'ragdoll',
                'bengal', 'russian blue', 'scottish fold', 'sphynx', 'abyssinian'
            ],
            'bird' => [
                'budgerigar', 'canary', 'cockatiel', 'lovebird', 'parrot',
                'finch', 'macaw', 'conure'
            ],
            'rabbit' => [
                'holland lop', 'netherland dwarf', 'mini rex', 'lionhead',
                'flemish giant', 'angora'
            ]
        ];

        return $breedData[$animalType] ?? [];
    }
}
