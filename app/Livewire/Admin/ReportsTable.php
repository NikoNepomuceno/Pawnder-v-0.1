<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PostReport;

class ReportsTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function approve($reportId)
    {
        $report = PostReport::findOrFail($reportId);
        $report->status = 'approved';
        $report->save();
        
        session()->flash('message', 'Report approved successfully.');
    }

    public function reject($reportId)
    {
        $report = PostReport::findOrFail($reportId);
        $report->status = 'rejected';
        $report->save();
        
        session()->flash('message', 'Report rejected successfully.');
    }

    public function render()
    {
        $reports = PostReport::with(['post', 'reporter'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.reports-table', [
            'reports' => $reports,
        ]);
    }
}
