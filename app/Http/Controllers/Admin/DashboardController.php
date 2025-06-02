<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostReport;
use App\Notifications\PostTakenDown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index()
    {
        $reports = PostReport::with(['post', 'reporter'])
            ->latest()
            ->paginate(10);

        $totalReports = PostReport::count();
        $pendingReports = PostReport::where('status', 'pending')->count();

        return view('admin.dashboard', compact('reports', 'totalReports', 'pendingReports'));
    }

    /**
     * Show all reported posts.
     */
    public function reports()
    {
        $reports = PostReport::with(['post', 'reporter', 'reviewer'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.reports', compact('reports'));
    }

    /**
     * Show details of a specific report.
     */
    public function showReport(PostReport $report)
    {
        $report->load(['post', 'reporter']);
        return view('admin.reports.show', compact('report'));
    }

    /**
     * Review a report and take action.
     */
    public function reviewReport(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:reviewed,rejected',
            'admin_notes' => 'nullable|string|max:1000',
            'flag_post' => 'nullable|boolean',
            'flag_reason' => 'required_if:flag_post,1|string|max:255',
        ]);

        $report = PostReport::findOrFail($id);
        $post = $report->post;

        // Update report status
        $report->status = $request->status;
        $report->admin_notes = $request->admin_notes;
        $report->reviewed_at = now();
        $report->reviewed_by = Auth::id();
        $report->save();

        // Flag the post if requested
        if ($request->flag_post) {
            $post->is_flagged = true;
            $post->flag_reason = $request->flag_reason;
            $post->save();
        }

        return redirect()->route('admin.reports')
            ->with('success', 'Report has been reviewed successfully.');
    }

    /**
     * Show all flagged posts.
     */
    public function flaggedPosts()
    {
        $posts = Post::where('is_flagged', true)
            ->with('user')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('admin.flagged-posts', compact('posts'));
    }

    /**
     * Remove flag from post.
     */
    public function unflagPost($id)
    {
        $post = Post::findOrFail($id);
        $post->is_flagged = false;
        $post->flag_reason = null;
        $post->save();

        return redirect()->route('admin.flagged-posts')
            ->with('success', 'Post has been unflagged successfully.');
    }

    public function approveReport(PostReport $report)
    {
        $report->update(['status' => 'resolved']);  // Changed from 'approved' to 'resolved'

        $post = $report->post;
        $post->update([
            'status' => 'taken_down',
            'is_taken_down' => true
        ]);

        // If this is an original post (not a shared post), we don't need to do anything special
        // The shared posts will automatically show the takedown banner through the new logic
        // No need to modify shared posts themselves

        // Notify the post owner
        $post->user->notify(new PostTakenDown($post));

        return redirect()->route('admin.dashboard')
            ->with('success', 'Report has been approved and post has been taken down.');
    }

    public function rejectReport(PostReport $report)
    {
        $report->update(['status' => 'rejected']);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Report has been rejected.');
    }

    public function unarchiveReport(PostReport $report)
    {
        try {
            // Check if the report is actually archived (dismissed)
            if ($report->status !== 'dismissed') {
                return response()->json([
                    'success' => false,
                    'message' => 'This report is not archived and cannot be unarchived.'
                ], 400);
            }

            // Update the report status back to pending
            $report->update([
                'status' => 'pending',
                'reviewed_at' => null,
                'reviewed_by' => null,
                'admin_notes' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Report has been successfully unarchived and moved back to pending reports.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unarchive report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show all pending reports.
     */
    public function pendingReports()
    {
        if (!\Illuminate\Support\Facades\Auth::user() || !\Illuminate\Support\Facades\Auth::user()->is_admin) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        return view('admin.pending-reports');
    }

    /**
     * Show all approved reports.
     */
    public function approvedReports()
    {
        if (!\Illuminate\Support\Facades\Auth::user() || !\Illuminate\Support\Facades\Auth::user()->is_admin) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $approvedReports = PostReport::with(['reporter', 'reviewer'])
            ->where('status', 'resolved')
            ->whereNotNull('reviewed_at')
            ->orderBy('reviewed_at', 'desc')
            ->paginate(10);

        return view('admin.approved-reports', compact('approvedReports'));
    }

    /**
     * Show all archived reports.
     */
    public function archivedReports()
    {
        if (!\Illuminate\Support\Facades\Auth::user() || !\Illuminate\Support\Facades\Auth::user()->is_admin) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $archivedReports = PostReport::with(['reporter', 'reviewer'])
            ->where('status', 'dismissed')
            ->whereNotNull('reviewed_at')
            ->orderBy('reviewed_at', 'desc')
            ->paginate(10);

        return view('admin.reports-archived', compact('archivedReports'));
    }
}
