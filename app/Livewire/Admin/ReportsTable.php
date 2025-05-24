<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\PostReport;
use App\Models\Post;
use Livewire\WithPagination;

class ReportsTable extends Component
{
    use WithPagination;

    public $status;
    public $selectedReport = null;
    public $showReportDetails = false;
    public $showApproveConfirm = false;

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
                throw new \Exception('Report not found');
            }

            // Update report status
            $report->update([
                'status' => 'resolved',
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id(),
                'admin_notes' => 'Post was taken down for violating community guidelines.',
            ]);

            // Handle post takedown
            if ($report->post) {
                $report->post->update([
                    'is_taken_down' => true,
                    'status' => 'taken_down',
                ]);

                // Update shared posts if any
                $sharedPosts = Post::where('shared_post_id', $report->post->id)
                    ->update([
                        'is_taken_down' => true,
                        'status' => 'taken_down',
                    ]);
            }

            $this->dispatch('reportStatusUpdated');
            $this->showReportDetails = false;
            $this->showApproveConfirm = false;

            // Add success message
            session()->flash('message', 'Report has been successfully resolved. The post and any shared copies have been taken down.');
        } catch (\Exception $e) {
            // Add error message
            session()->flash('error', 'Failed to resolve report: ' . $e->getMessage());
            throw $e; // Re-throw to show error in the UI
        }
    }

    public function archiveReport($reportId)
    {
        $report = PostReport::find($reportId);
        $report->update([
            'status' => 'dismissed',
            'reviewed_at' => now(),
        ]);

        $this->dispatch('reportStatusUpdated');
        $this->showReportDetails = false;
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
