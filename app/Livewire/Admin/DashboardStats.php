<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\PostReport;

class DashboardStats extends Component
{
    public $totalReports;
    public $pendingReports;

    public function mount()
    {
        $this->updateStats();
    }

    public function updateStats()
    {
        $this->totalReports = PostReport::count();
        $this->pendingReports = PostReport::where('status', 'pending')->count();
    }

    public function render()
    {
        return view('livewire.admin.dashboard-stats');
    }
}
