<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\PostReport;

class DashboardStats extends Component
{
    public $totalReports;
    public $pendingReports;
    public $approvedReports;
    public $archivedReports;

    protected $listeners = ['reportStatusUpdated' => 'refreshStats'];

    public function mount()
    {
        $this->refreshStats();
    }

    public function refreshStats()
    {
        $this->totalReports = PostReport::count();
        $this->pendingReports = PostReport::where('status', 'pending')->count();
        $this->approvedReports = PostReport::where('status', 'resolved')->count();
        $this->archivedReports = PostReport::where('status', 'dismissed')->count();
    }

    public function render()
    {
        return view('livewire.admin.dashboard-stats');
    }
}
