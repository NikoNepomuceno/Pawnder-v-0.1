<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostReaction;
use App\Notifications\PostLiked;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Helpers\ReactionHelper;

class PostReactionController extends Controller
{
    /**
     * Store a new reaction or update an existing one.
     */
    public function store(Request $request, Post $post)
    {
        // Only allow 'like' as the reaction type
        $request->validate([
            'reaction_type' => 'required|in:like',
        ]);

        // Find existing reaction or create a new one
        $reaction = PostReaction::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'post_id' => $post->id,
            ],
            [
                'reaction_type' => 'like',
            ]
        );

        // Get the icon for the reaction using ReactionHelper
        $icon = ReactionHelper::getReactionIcon('like');

        // Send notification to post owner only if not already notified
        if ($post->user_id !== Auth::id()) {
            // Check if a notification already exists for this user/post/reaction
            $alreadyNotified = DB::table('notifications')
                ->where('notifiable_id', $post->user_id)
                ->where('type', 'App\\Notifications\\PostLiked')
                ->where('data', 'like', '%"post_id":' . $post->id . '%')
                ->where('data', 'like', '%"user_id":' . Auth::id() . '%')
                ->exists();

            if (!$alreadyNotified) {
                try {
                    $notification = new PostLiked($post);
                    $notification->likerUsername = Auth::user()->username;
                    $notification->likerId = Auth::id();
                    $post->user->notify($notification);
                    Log::info('Notification sent to user ' . $post->user_id . ' for like on post ' . $post->id);
                } catch (\Exception $e) {
                    Log::error('Failed to send notification: ' . $e->getMessage());
                }
            }
        }

        // Count likes only
        $likeCount = $post->fresh()->reactions()->where('reaction_type', 'like')->count();

        return response()->json([
            'success' => true,
            'reaction' => $reaction,
            'icon' => $icon,
            'total_reactions' => $likeCount,
        ]);
    }

    /**
     * Remove a like.
     */
    public function destroy(Post $post)
    {
        $deleted = PostReaction::where('user_id', Auth::id())
            ->where('post_id', $post->id)
            ->where('reaction_type', 'like')
            ->delete();

        // Count likes only
        $likeCount = $post->fresh()->reactions()->where('reaction_type', 'like')->count();

        return response()->json([
            'success' => $deleted > 0,
            'total_reactions' => $likeCount,
        ]);
    }
}
