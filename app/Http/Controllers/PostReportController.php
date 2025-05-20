<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostReportController extends Controller
{
    /**
     * Report a post.
     */
    public function store(Request $request, $postId)
    {
        // Authentication check
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'You must be logged in to report a post.'], 401);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $post = Post::findOrFail($postId);

        // Check if user has already reported this post
        $existingReport = PostReport::where('post_id', $postId)
            ->where('reported_by', Auth::id())
            ->where('status', 'pending')
            ->first();

        if ($existingReport) {
            return response()->json(['success' => false, 'message' => 'You have already reported this post.'], 409);
        }

        // Create new report
        $report = new PostReport();
        $report->post_id = $postId;
        $report->reported_by = Auth::id();
        $report->reason = $validated['reason'];
        $report->status = 'pending';
        $report->save();

        return response()->json(['success' => true, 'message' => 'Post reported successfully. Our admins will review it shortly.']);
    }
}
