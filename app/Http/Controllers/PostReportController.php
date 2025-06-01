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
            'reasons' => 'required|string', // JSON string of selected reasons
            'violation_reasons' => 'required|array|min:1', // Array of violation reasons
            'violation_reasons.*' => 'required|string|in:' . implode(',', PostReport::VIOLATION_CATEGORIES),
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

        // Decode the JSON reasons and validate
        $selectedReasons = json_decode($validated['reasons'], true);
        if (!is_array($selectedReasons) || empty($selectedReasons)) {
            return response()->json(['success' => false, 'message' => 'Please select at least one violation reason.'], 422);
        }

        // Create new report
        $report = new PostReport();
        $report->post_id = $postId;
        $report->reported_by = Auth::id();
        $report->violation_reasons = $selectedReasons; // Store as JSON
        $report->reason = $this->formatReasonsForDisplay($selectedReasons); // Keep for backward compatibility
        $report->status = 'pending';
        $report->save();

        return response()->json(['success' => true, 'message' => 'Post reported successfully. Our admins will review it shortly.']);
    }

    /**
     * Format violation reasons for display (backward compatibility).
     */
    private function formatReasonsForDisplay(array $reasons): string
    {
        $reasonLabels = PostReport::VIOLATION_LABELS;
        $formattedReasons = array_map(function($reason) use ($reasonLabels) {
            return $reasonLabels[$reason] ?? $reason;
        }, $reasons);

        return 'Violation Categories: ' . implode(', ', $formattedReasons);
    }
}
