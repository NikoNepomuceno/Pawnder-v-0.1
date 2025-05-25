<div>
    <div wire:poll.5s>
        @if (session()->has('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif

        <div class="reports-list">
            @forelse($reports as $report)
                <div class="report-card" wire:key="report-{{ $report->id }}">
                    <div class="report-header">
                        <div class="report-info">
                            <h4>Report #{{ $report->id }}</h4>
                            <span class="report-date">{{ $report->created_at->diffForHumans() }}</span>
                        </div>
                        <button class="view-btn" wire:click="viewReport({{ $report->id }})">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                    </div>
                    <div class="report-content">
                        <p class="report-reason">{{ Str::limit($report->reason, 100) }}</p>
                        <div class="report-meta">
                            <span><i class="fas fa-user"></i> Reported by: {{ $report->reporter->name }}</span>
                            <span><i class="fas fa-file-alt"></i> Post ID: {{ $report->post_id }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="no-reports">
                    <i class="fas fa-inbox"></i>
                    <p>No {{ $status }} reports found</p>
                </div>
            @endforelse
        </div>

        <div class="pagination-container">
            {{ $reports->links() }}
        </div>

        @if($showReportDetails && !$showApproveConfirm && $selectedReport)
            <div class="report-modal show" id="reportDetailsModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Report Details</h3>
                        <button class="close-btn" wire:click="$set('showReportDetails', false)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="report-details">
                            <div class="detail-section">
                                <h4>Report Information</h4>
                                <p><strong>Report ID:</strong> #{{ $selectedReport->id }}</p>
                                <p><strong>Status:</strong> {{ ucfirst($selectedReport->status) }}</p>
                                <p><strong>Reported At:</strong> {{ $selectedReport->created_at->format('M d, Y H:i') }}</p>
                                <p><strong>Reason:</strong> {{ $selectedReport->reason }}</p>
                            </div>

                            <div class="detail-section">
                                <h4>Reporter Information</h4>
                                <p><strong>Name:</strong> {{ $selectedReport->reporter->name }}</p>
                                <p><strong>Email:</strong> {{ $selectedReport->reporter->email }}</p>
                            </div>

                            <div class="detail-section">
                                <h4>Reported Post</h4>
                                <livewire:admin.post-view :post="$selectedReport->post" />
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        @if($selectedReport->status === 'pending')
                            <button class="archive-btn" wire:click="archiveReport({{ $selectedReport->id }})">
                                <i class="fas fa-archive"></i> Archive Report
                            </button>
                            <button class="approve-btn" wire:click="$set('showApproveConfirm', true)">
                                <i class="fas fa-check"></i> Approve Report
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if($showApproveConfirm)
        <div class="approve-modal-overlay" id="approveReportModal">
            <div class="approve-modal-content">
                <div class="modal-header">
                    <h3>Confirm Report Approval</h3>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve this report and take down the post?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="cancel-btn" wire:click="$set('showApproveConfirm', false)">Cancel</button>
                    <button type="button" class="submit-btn" wire:click="approveReport({{ $selectedReport->id }})">Approve</button>
                </div>
            </div>
        </div>
    @endif

    <style>
    .approve-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        backdrop-filter: blur(2px);
    }

    .approve-modal-content {
        background: white;
        border-radius: 12px;
        width: 90%;
        max-width: 400px;
        border: 2px solid #4CAF50;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .approve-modal-content .modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid #eee;
    }

    .approve-modal-content .modal-body {
        padding: 1.5rem;
    }

    .approve-modal-content .modal-footer {
        padding: 1.5rem;
        border-top: 1px solid #eee;
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }

    .approve-modal-content .cancel-btn,
    .approve-modal-content .submit-btn {
        padding: 0.5rem 1.5rem;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-weight: 500;
    }

    .approve-modal-content .cancel-btn {
        background: #f0f2f5;
        color: #1a3d2b;
    }

    .approve-modal-content .submit-btn {
        background: #4CAF50;
        color: white;
    }

    .approve-modal-content .cancel-btn:hover {
        background: #e4e6e9;
    }

    .approve-modal-content .submit-btn:hover {
        background: #45a049;
    }
    </style>

    <style>
    .reports-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .report-card {
        background: var(--admin-bg);
        border-radius: 0.8rem;
        padding: 1.2rem;
        box-shadow: 0 2px 8px rgba(27, 67, 50, 0.05);
    }
    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    .report-info h4 {
        margin: 0;
        color: var(--admin-primary);
        font-size: 1.1rem;
    }
    .report-date {
        font-size: 0.9rem;
        color: var(--admin-text-secondary);
    }
    .view-btn {
        background: #1b4332; /* Forest green color */
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .view-btn:hover {
        background: #2d6a4f; /* Lighter forest green on hover */
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .view-btn:active {
        background: #1b4332; /* Back to original color when active */
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .report-content {
        color: var(--admin-text);
    }
    .report-reason {
        margin: 0 0 1rem;
        line-height: 1.5;
    }
    .report-meta {
        display: flex;
        gap: 1.5rem;
        font-size: 0.9rem;
        color: var(--admin-text-secondary);
    }
    .report-meta span {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .no-reports {
        text-align: center;
        padding: 3rem;
        background: var(--admin-bg);
        border-radius: 0.8rem;
        color: var(--admin-text-secondary);
    }
    .no-reports i {
        font-size: 2rem;
        margin-bottom: 1rem;
    }
    /* Modal Styles */
    .report-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(27, 67, 50, 0.15);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    .report-modal.show {
        opacity: 1;
        visibility: visible;
    }
    .report-modal .modal-content {
        background: white;
        border-radius: 1.2rem;
        width: 90%;
        max-width: 800px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 12px 40px rgba(27, 67, 50, 0.15);
        border: 1px solid #e5e7eb;
        transform: scale(0.95);
        opacity: 0;
        transition: transform 0.3s ease, opacity 0.3s ease;
    }
    .report-modal.show .modal-content {
        transform: scale(1);
        opacity: 1;
    }
    .report-modal .modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: white;
        position: sticky;
        top: 0;
        z-index: 2;
        border-top-left-radius: 1.2rem;
        border-top-right-radius: 1.2rem;
    }
    .report-modal .modal-header h3 {
        margin: 0;
        color: #1b4332;
        font-size: 1.4rem;
        font-weight: 600;
    }
    .report-modal .close-btn {
        background: none;
        border: none;
        font-size: 1.2rem;
        color: #6b7280;
        cursor: pointer;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .report-modal .close-btn:hover {
        background: #f3f4f6;
        color: #1b4332;
    }
    .report-modal .modal-body {
        padding: 1.5rem;
    }
    .report-modal .detail-section {
        margin-bottom: 1.5rem;
        background: white;
        border-radius: 0.8rem;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        border: 1px solid #e5e7eb;
    }
    .report-modal .detail-section:last-child {
        margin-bottom: 0;
    }
    .report-modal .detail-section h4 {
        color: #1b4332;
        margin: 0 0 1rem;
        font-size: 1.1rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e5e7eb;
    }
    .report-modal .detail-section p {
        margin: 0.75rem 0;
        color: #374151;
        line-height: 1.6;
        display: flex;
        align-items: baseline;
        gap: 0.5rem;
    }
    .report-modal .detail-section strong {
        color: #1b4332;
        font-weight: 600;
        min-width: 120px;
    }
    .report-modal .modal-footer {
        padding: 1.5rem;
        border-top: 1px solid #e5e7eb;
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }
    .report-modal .archive-btn,
    .report-modal .approve-btn {
        padding: 0.5rem 1.5rem;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .report-modal .archive-btn {
        background: #f3f4f6;
        color: #1b4332;
    }
    .report-modal .approve-btn {
        background: #1b4332;
        color: white;
    }
    .report-modal .archive-btn:hover {
        background: #e5e7eb;
    }
    .report-modal .approve-btn:hover {
        background: #2d6a4f;
    }
    /* Scrollbar Styles */
    .report-modal .modal-content::-webkit-scrollbar {
        width: 8px;
    }
    .report-modal .modal-content::-webkit-scrollbar-track {
        background: #f3f4f6;
        border-radius: 4px;
    }
    .report-modal .modal-content::-webkit-scrollbar-thumb {
        background: #1b4332;
        border-radius: 4px;
    }
    .report-modal .modal-content::-webkit-scrollbar-thumb:hover {
        background: #2d6a4f;
    }
    /* Mobile Responsiveness */
    @media (max-width: 640px) {
        .report-modal .modal-content {
            width: 95%;
            margin: 1rem;
        }
        
        .report-modal .modal-header {
            padding: 1rem;
        }
        
        .report-modal .modal-body {
            padding: 1rem;
        }
        
        .report-modal .modal-footer {
            padding: 1rem;
            flex-direction: column;
        }
        
        .report-modal .archive-btn,
        .report-modal .approve-btn {
            width: 100%;
            justify-content: center;
        }
    }
    </style>
</div>