<div wire:poll.5s>
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <div class="reports-table-container">
        <table class="reports-table">
            <thead>
                <tr>
                    <th>Report ID</th>
                    <th>Post Title</th>
                    <th>Reported By</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Reported At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    <tr>
                        <td>#{{ $report->id }}</td>
                        <td>{{ Str::limit($report->post->title, 30) }}</td>
                        <td>{{ $report->reporter->name }}</td>
                        <td>{{ $report->reason }}</td>
                        <td>
                            <span class="status-badge status-{{ $report->status }}">
                                {{ ucfirst($report->status) }}
                            </span>
                        </td>
                        <td>{{ $report->created_at->diffForHumans() }}</td>
                        <td class="action-buttons">
                            <a href="{{ route('admin.reports.show', $report->id) }}" class="btn btn-view"
                                title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($report->status === 'pending')
                                <button wire:click="approve({{ $report->id }})" class="btn btn-approve" title="Approve Post">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button wire:click="reject({{ $report->id }})" class="btn btn-reject" title="Reject Report">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No reports found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-container">
        {{ $reports->links() }}
    </div>
</div>