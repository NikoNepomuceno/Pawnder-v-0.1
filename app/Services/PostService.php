<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use App\Notifications\PostShared;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

/**
 * Service class for handling post-related business logic.
 */
class PostService
{

    /**
     * Create a new post.
     *
     * @param array<string, mixed> $data The post data
     * @return \App\Models\Post
     * @throws \Exception When post creation fails
     */
    public function createPost(array $data): Post
    {
        try {
            $post = new Post($data);
            $post->user_id = Auth::id();
            $post->save();
            return $post;
        } catch (\Exception $e) {
            Log::error('Post creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing post.
     *
     * @param \App\Models\Post $post The post to update
     * @param array<string, mixed> $data The update data
     * @return \App\Models\Post
     */
    public function updatePost(Post $post, array $data): Post
    {
        try {
            $post->update($data);
            return $post;
        } catch (\Exception $e) {
            Log::error('Post update failed:', [
                'post_id' => $post->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a post.
     *
     * @param \App\Models\Post $post The post to delete
     * @return bool
     */
    public function deletePost(Post $post): bool
    {
        try {
            // If this is a shared post, delete the share record from post_shares table
            if ($post->isShared()) {
                \App\Models\PostShare::where('user_id', Auth::id())
                    ->where('post_id', $post->shared_post_id)
                    ->delete();
            }

            // If this is an original post, update all shared posts
            if (!$post->isShared()) {
                // Get all posts that share this post
                $sharedPosts = Post::where('shared_post_id', $post->id)->get();

                // Update each shared post
                foreach ($sharedPosts as $sharedPost) {
                    $sharedPost->update([
                        'shared_post_id' => null,
                        'title' => 'This post has been deleted',
                        'description' => 'The original post has been deleted by the author.',
                        'photo_urls' => [],
                    ]);
                }
            }

            // Soft delete the post
            return $post->delete();
        } catch (\Exception $e) {
            Log::error('Post deletion failed:', [
                'post_id' => $post->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Share a post and increment its share count.
     *
     * @param \App\Models\Post $post The post to share
     * @return int The updated share count
     */
    public function sharePost(Post $post): int
    {
        try {
            $post->increment('share_count');

            if ($post->user_id !== Auth::id()) {
                $this->sendShareNotification($post);
            }

            return $post->share_count;
        } catch (\Exception $e) {
            Log::error('Post share failed:', [
                'post_id' => $post->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Share a post within the application.
     *
     * @param \App\Models\Post $post The post to share
     * @return array{shared_post_id: int, share_count: int}
     * @throws \Exception When sharing is not allowed
     */
    public function shareInApp(Post $post): array
    {
        try {
            $user = Auth::user();

            if ($post->user_id === $user->id) {
                throw new \Exception('You cannot share your own post.');
            }

            if ($post->shares()->where('user_id', $user->id)->exists()) {
                throw new \Exception('You have already shared this post.');
            }

            $sharedPost = $this->createSharedPost($post, $user);
            $this->createShareRecord($post, $user);
            $this->incrementShareCount($post);
            $this->sendShareNotification($post, $user);

            return [
                'shared_post_id' => $sharedPost->id,
                'share_count' => $post->fresh()->share_count,
            ];
        } catch (\Exception $e) {
            Log::error('In-app post share failed:', [
                'post_id' => $post->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create a new post that shares the original post.
     *
     * @param \App\Models\Post $post The original post
     * @param \App\Models\User $user The user sharing the post
     * @return \App\Models\Post
     */
    private function createSharedPost(Post $post, User $user): Post
    {
        return Post::create([
            'user_id' => $user->id,
            'title' => $post->title,
            'description' => $post->description,
            'status' => $post->status,
            'breed' => $post->breed,
            'location' => $post->location,
            'mobile_number' => $post->mobile_number,
            'email' => $post->email,
            'shared_post_id' => $post->id,
            'photo_urls' => $post->photo_urls,
            'was_shared' => true,
        ]);
    }

    /**
     * Create a share record for the post.
     *
     * @param \App\Models\Post $post The post being shared
     * @param \App\Models\User $user The user sharing the post
     * @return \Illuminate\Database\Eloquent\Model
     */
    private function createShareRecord(Post $post, User $user): Model
    {
        return $post->shares()->create(['user_id' => $user->id]);
    }

    /**
     * Increment the share count for a post.
     *
     * @param \App\Models\Post $post The post to increment
     * @return int The new share count
     */
    private function incrementShareCount(Post $post): int
    {
        $post->increment('share_count');
        return $post->share_count;
    }

    /**
     * Send a notification to the post owner about the share.
     *
     * @param \App\Models\Post $post The post being shared
     * @param \App\Models\User|null $user The user sharing the post
     * @return void
     */
    private function sendShareNotification(Post $post, ?User $user = null): void
    {
        try {
            $user = $user ?? Auth::user();
            $notification = new PostShared($post);
            $notification->sharerUsername = $user->username;
            $notification->sharerId = $user->id;
            $post->user->notify($notification);
        } catch (\Exception $e) {
            Log::error('Failed to send notification:', [
                'post_id' => $post->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
