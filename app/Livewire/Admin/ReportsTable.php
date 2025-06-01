<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\PostReport;
use Livewire\WithPagination;
use App\Notifications\PostTakenDown;
use Illuminate\Support\Facades\Auth;

class ReportsTable extends Component
{
    use WithPagination;

    public $status;
    public $selectedReport = null;
    public $showReportDetails = false;
    public $showApproveConfirm = false;
    public $showArchiveConfirm = false;

    protected $listeners = [
        'reportStatusUpdated' => '$refresh',
        'openReportModal' => 'openReportModal',
        'closeReportModal' => 'closeReportModal',
    ];

    public function mount($status)
    {
        $this->status = $status;
    }

    public function viewReport($reportId)
    {
        $this->selectedReport = PostReport::with(['reporter', 'post'])->find($reportId);
        $this->showReportDetails = true;
    }

    public function approveReport($reportId)
    {
        try {
            $report = PostReport::find($reportId);

            if (!$report) {
                throw new \Exception('Report not found.');
            }

            $post = $report->post;

            if (!$post) {
                throw new \Exception('Associated post not found.');
            }

            // Update report status
            $report->update([
                'status' => 'resolved',  // Changed from 'approved' to 'resolved'
                'reviewed_at' => now(),
                'reviewed_by' => Auth::id(),
                'admin_notes' => 'Report was approved and post was taken down.'
            ]);

            // Take down the post
            $post->update([
                'status' => 'taken_down',
                'is_taken_down' => true
            ]);

            // No need to modify shared posts - they will automatically show takedown banner

            // Notify the post owner
            $post->user->notify(new PostTakenDown($post));

            $this->dispatch('showNotification',
                'Report approved and post has been taken down successfully.',
                'success'
            );
            $this->dispatch('reportStatusUpdated');
            $this->showReportDetails = false;
            $this->showApproveConfirm = false;
        } catch (\Exception $e) {
            // Close all modals so admin can see the error message clearly
            $this->showReportDetails = false;
            $this->showApproveConfirm = false;

            $this->dispatch('showNotification',
                'Failed to approve report: ' . $e->getMessage(),
                'error'
            );
        }
    }

    public function showArchiveConfirmation($reportId)
    {
        $this->selectedReport = PostReport::with(['reporter', 'post'])->find($reportId);
        $this->showArchiveConfirm = true;
    }

    public function archiveReport($reportId)
    {
        try {
            $report = PostReport::find($reportId);

            if (!$report) {
                throw new \Exception('Report not found.');
            }

            $report->update([
                'status' => 'dismissed',  // Changed from 'archived' to 'dismissed'
                'reviewed_at' => now(),
                'reviewed_by' => Auth::id(),
                'admin_notes' => 'Report was archived by admin.'
            ]);

            $this->dispatch('showNotification',
                'Report has been archived successfully.',
                'success'
            );
            $this->dispatch('reportStatusUpdated');
            $this->showReportDetails = false;
            $this->showArchiveConfirm = false;
        } catch (\Exception $e) {
            // Close modals so admin can see the error message clearly
            $this->showReportDetails = false;
            $this->showArchiveConfirm = false;

            $this->dispatch('showNotification',
                'Failed to archive report: ' . $e->getMessage(),
                'error'
            );
        }
    }

    public function openReportModal()
    {
        $this->showReportDetails = true;
    }

    public function closeReportModal()
    {
        $this->showReportDetails = false;
    }

    public function render()
    {
        $reports = PostReport::where('status', $this->status)
            ->with(['reporter', 'post'])
            ->latest()
            ->paginate(10);

        return view('livewire.admin.reports-table', [
            'reports' => $reports
        ]);
    }
}
