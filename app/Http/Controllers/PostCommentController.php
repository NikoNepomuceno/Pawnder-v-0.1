<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostComment;
use App\Notifications\NewComment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Database\Eloquent\Collection;

/**
 * Controller for handling post comments.
 */
class PostCommentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Store a new comment.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Post $post): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|min:1|max:1000',
            'parent_id' => 'nullable|exists:post_comments,id',
        ]);

        try {
            $comment = new PostComment([
                'content' => $validated['content'],
                'parent_id' => $validated['parent_id'] ?? null,
                'user_id' => Auth::id(),
            ]);

            $post->comments()->save($comment);
            $comment->load('user');

            if ($post->user_id !== Auth::id()) {
                $this->sendCommentNotification($post, $comment);
            }

            return response()->json([
                'success' => true,
                'comment' => $comment,
                'comment_count' => $post->allComments()->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to store comment:', [
                'error' => $e->getMessage(),
                'post_id' => $post->id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to store comment'
            ], 500);
        }
    }

    /**
     * Update a comment.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\PostComment $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, PostComment $comment): JsonResponse
    {
        $this->authorize('update', $comment);

        $validated = $request->validate([
            'content' => 'required|string|min:1|max:1000',
        ]);

        try {
            $comment->update([
                'content' => $validated['content'],
            ]);

            return response()->json([
                'success' => true,
                'comment' => $comment,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update comment:', [
                'error' => $e->getMessage(),
                'comment_id' => $comment->id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update comment'
            ], 500);
        }
    }

    /**
     * Delete a comment.
     *
     * @param \App\Models\PostComment $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(PostComment $comment): JsonResponse
    {
        if ($comment->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            $post = $comment->post;
            $comment->delete();

            return response()->json([
                'success' => true,
                'comment_count' => $post->allComments()->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete comment:', [
                'error' => $e->getMessage(),
                'comment_id' => $comment->id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete comment'
            ], 500);
        }
    }

    /**
     * Get comments for a post.
     *
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Post $post): JsonResponse
    {
        try {
            $comments = $post->comments()
                ->with(['user', 'replies.user'])
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'comments' => $comments,
                'comment_count' => $post->allComments()->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch comments:', [
                'error' => $e->getMessage(),
                'post_id' => $post->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch comments'
            ], 500);
        }
    }

    /**
     * Send a notification to the post owner about a new comment.
     *
     * @param \App\Models\Post $post
     * @param \App\Models\PostComment $comment
     * @return void
     */
    private function sendCommentNotification(Post $post, PostComment $comment): void
    {
        try {
            $notification = new NewComment($post, $comment);
            $notification->commenterUsername = Auth::user()->username;
            $notification->commenterId = Auth::id();
            $post->user->notify($notification);
        } catch (\Exception $e) {
            Log::error('Failed to send notification:', [
                'error' => $e->getMessage(),
                'post_id' => $post->id,
                'comment_id' => $comment->id
            ]);
        }
    }
}
