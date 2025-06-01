<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\PostReport;
use Carbon\Carbon;

class DashboardCharts extends Component
{
    public $chartData;

    protected $listeners = ['reportStatusUpdated' => 'refreshChartData'];

    public function mount()
    {
        $this->refreshChartData();
    }

    public function refreshChartData()
    {
        // Get current status counts
        $totalReports = PostReport::count();
        $pendingReports = PostReport::where('status', 'pending')->count();
        $approvedReports = PostReport::where('status', 'resolved')->count();
        $archivedReports = PostReport::where('status', 'dismissed')->count();

        // Get reports trend for the last 7 days
        $trendData = [];
        $trendLabels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $trendLabels[] = $date->format('M j');
            $trendData[] = PostReport::whereDate('created_at', $date)->count();
        }

        $this->chartData = [
            'statusCounts' => [
                'labels' => ['Total Reports', 'Pending', 'Approved', 'Archived'],
                'data' => [$totalReports, $pendingReports, $approvedReports, $archivedReports],
                'colors' => ['#1b4332', '#f59e0b', '#10b981', '#6b7280']
            ],
            'trend' => [
                'labels' => $trendLabels,
                'data' => $trendData
            ]
        ];
    }

    public function render()
    {
        return view('livewire.admin.dashboard-charts');
    }
}
