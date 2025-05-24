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
            <div class="report-modal" id="reportDetailsModal">
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
        background: var(--admin-primary);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        transition: background 0.2s;
    }
    .view-btn:hover {
        background: var(--admin-primary-dark);
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
        background: rgba(30, 30, 30, 0.18);
        backdrop-filter: blur(6px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }
    .modal-content {
        background: var(--admin-bg);
        border-radius: 1.5rem;
        width: 90%;
        max-width: 800px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 12px 40px 0 rgba(27, 67, 50, 0.22), 0 2px 8px 0 rgba(27, 67, 50, 0.10);
        border: 1px solid var(--admin-border);
    }

    /* Add existing modal styles */
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
    .modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--admin-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--admin-bg);
        position: sticky;
        top: 0;
        z-index: 2;
        box-shadow: 0 2px 8px rgba(27, 67, 50, 0.07);
        border-top-left-radius: 1.5rem;
        border-top-right-radius: 1.5rem;
    }
    .modal-header h3 {
        margin: 0;
        color: var(--admin-primary);
        font-size: 1.4rem;
        font-weight: 600;
    }
    .close-btn {
        background: none;
        border: none;
        font-size: 1.2rem;
        color: var(--admin-text-secondary);
        cursor: pointer;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .close-btn:hover {
        background: var(--admin-bg-alt);
        color: var(--admin-primary);
    }
    .modal-body {
        padding: 1.5rem;
    }
    .detail-section {
        margin-bottom: 2rem;
        background: var(--admin-bg);
        border-radius: 1.1rem;
        padding: 1.2rem;
        box-shadow: 0 4px 18px rgba(27, 67, 50, 0.13), 0 1.5px 4px rgba(27, 67, 50, 0.07);
    }
    .detail-section h4 {
        color: var(--admin-primary);
        margin: 0 0 1rem;
        font-size: 1.1rem;
        font-weight: 600;
    }
    .detail-section p {
        margin: 0.5rem 0;
        color: var(--admin-text);
        line-height: 1.5;
    }
    .post-preview {
        background: var(--admin-bg);
        padding: 1rem;
        border-radius: 0.5rem;
        margin-top: 0.5rem;
    }

    /* Post Card Styles */
    .post-card {
        background: white;
        border-radius: 1.2rem;
        box-shadow: 0 8px 32px rgba(27, 67, 50, 0.18), 0 2px 8px rgba(27, 67, 50, 0.10);
        overflow: hidden;
        margin-top: 1rem;
    }

    .post-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
    }

    .post-user-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .post-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    .post-author {
        font-weight: 600;
        margin: 0;
        font-size: 14px;
    }

    .post-date {
        color: #65676B;
        font-size: 12px;
    }

    .post-details {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 10px;
        font-size: 13px;
    }

    .post-status {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 4px;
        font-weight: bold;
        font-size: 12px;
        text-transform: uppercase;
        color: white;
    }

    .post-status.found {
        background-color: #4CAF50;
    }

    .post-status.not_found {
        background-color: #F44336;
    }

    .post-breed,
    .post-location,
    .post-contact {
        display: inline-block;
        color: #65676B;
        font-weight: 600;
    }

    .post-content {
        padding: 15px;
    }

    .post-content h3 {
        margin-top: 0;
        margin-bottom: 10px;
        color: #1c1e21;
        font-size: 18px;
    }

    .post-description {
        margin-top: 10px;
        color: #1c1e21;
        line-height: 1.5;
        font-size: 15px;
    }

    .post-images {
        position: relative;
        width: 100%;
        overflow: hidden;
        margin-top: 1rem;
    }

    .image-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 4px;
        width: 100%;
    }

    .image-grid.single-image {
        grid-template-columns: 1fr;
    }

    .image-grid.two-images {
        grid-template-columns: repeat(2, 1fr);
    }

    .image-grid.three-images {
        grid-template-columns: repeat(2, 1fr);
        grid-template-rows: auto;
    }

    .image-grid.three-images .grid-item:first-child {
        grid-row: span 2;
    }

    .grid-item {
        position: relative;
        width: 100%;
        padding-bottom: 100%; /* Creates a square aspect ratio */
        overflow: hidden;
        cursor: pointer;
    }

    .grid-item img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: contain; /* Changed from cover to contain */
        background-color: #f8f9fa; /* Light background for images */
    }

    .more-indicator {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 24px;
        font-weight: bold;
    }

    .deleted-post-message {
        text-align: center;
        padding: 2rem;
        background: #f8f9fa;
        border-radius: 8px;
        color: #666;
    }

    .deleted-post-message i {
        font-size: 2rem;
        color: #dc3545;
        margin-bottom: 1rem;
    }

    .deleted-post-message h3 {
        margin: 0.5rem 0;
        color: #333;
    }

    .deleted-post-message p {
        margin: 0;
        color: #666;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        padding: 1.2rem 1.5rem 1.5rem 1.5rem;
        background: var(--admin-bg);
        border-bottom-left-radius: 1.5rem;
        border-bottom-right-radius: 1.5rem;
    }
    .archive-btn, .approve-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1.2rem;
        border: none;
        border-radius: 0.5rem;
        font-size: 1rem;
        cursor: pointer;
        transition: background 0.2s, color 0.2s;
    }
    .archive-btn {
        background: #f5f5f5;
        color: #444;
    }
    .archive-btn:hover {
        background: #e0e0e0;
        color: #222;
    }
    .approve-btn {
        background: #4CAF50;
        color: #fff;
    }
    .approve-btn:hover {
        background: #388e3c;
        color: #fff;
    }
    .modal.show {
        opacity: 1 !important;
        visibility: visible !important;
    }
    </style>
</div>