<div>
    <div wire:poll.30s>

        <div class="reports-list">
            @forelse($reports as $report)
                <div class="report-card" wire:key="report-{{ $report->id }}">
                    <div class="report-header">
                        <div class="report-title-section">
                            <div class="report-id-badge">
                                <i class="fas fa-flag"></i>
                                <span>Report #{{ $report->id }}</span>
                            </div>
                            <div class="report-timestamp">
                                <i class="fas fa-clock"></i>
                                <span>{{ $report->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <button class="view-details-btn" wire:click="viewReport({{ $report->id }})">
                            <i class="fas fa-external-link-alt"></i>
                            <span>View Details</span>
                        </button>
                    </div>

                    <div class="report-body">
                        @if($report->hasViolationCategories())
                            <div class="violations-section">
                                <div class="violation-header">
                                    <h5 class="violation-title">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Violations
                                    </h5>
                                    <span class="violation-count-badge">
                                        {{ count($report->violation_reasons) }}
                                        violation{{ count($report->violation_reasons) > 1 ? 's' : '' }}
                                    </span>
                                </div>
                                <div class="violation-badges-grid">
                                    @if($report->violation_reasons && is_array($report->violation_reasons))
                                        @foreach(array_slice($report->violation_reasons, 0, 6) as $violation)
                                            <span class="violation-badge-item">{{ ucwords(str_replace('_', ' ', $violation)) }}</span>
                                        @endforeach
                                        @if(count($report->violation_reasons) > 6)
                                            <span class="violation-badge-more">+{{ count($report->violation_reasons) - 6 }} more</span>
                                        @endif
                                    @else
                                        <span class="violation-badge-item">{{ $report->formatted_violation_reasons ?? 'No specific violations listed' }}</span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="violations-section">
                                <div class="violation-header">
                                    <h5 class="violation-title">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Report Reason
                                    </h5>
                                </div>
                                <p class="violation-description">{{ Str::limit($report->reason, 120) }}</p>
                            </div>
                        @endif

                        <div class="report-metadata">
                            <div class="metadata-item">
                                <div class="metadata-icon">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="metadata-content">
                                    <span class="metadata-label">Reported by</span>
                                    <span class="metadata-value">{{ $report->reporter->name }}</span>
                                </div>
                            </div>
                            <div class="metadata-item">
                                <div class="metadata-icon">
                                    <i class="fas fa-file-text"></i>
                                </div>
                                <div class="metadata-content">
                                    <span class="metadata-label">Post ID</span>
                                    <span class="metadata-value">#{{ $report->post_id }}</span>
                                </div>
                            </div>
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
                        <div class="modal-title-section">
                            <div class="modal-icon">
                                <i class="fas fa-flag"></i>
                            </div>
                            <div class="modal-title-content">
                                <h3>Report Details</h3>
                                <span class="modal-subtitle">Report #{{ $selectedReport->id }}</span>
                            </div>
                        </div>
                        <button class="close-btn" wire:click="$set('showReportDetails', false)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="report-details">
                            <!-- Report Information Section -->
                            <div class="detail-section">
                                <div class="section-header">
                                    <div class="section-icon">
                                        <i class="fas fa-info-circle"></i>
                                    </div>
                                    <h4>Report Information</h4>
                                </div>
                                <div class="section-content">
                                    <div class="info-grid">
                                        <div class="info-item">
                                            <span class="info-label">Report ID</span>
                                            <span class="info-value">#{{ $selectedReport->id }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Status</span>
                                            <span class="status-badge status-{{ $selectedReport->status }}">
                                                {{ ucfirst($selectedReport->status) }}
                                            </span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Reported At</span>
                                            <span
                                                class="info-value">{{ $selectedReport->created_at->format('M d, Y \a\t H:i') }}</span>
                                        </div>
                                    </div>

                                    @if($selectedReport->hasViolationCategories())
                                        <div class="violation-details">
                                            <h5 class="violation-section-title">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Violation Categories
                                            </h5>
                                            <div class="violation-categories">
                                                @foreach($selectedReport->grouped_violation_reasons as $category => $reasons)
                                                    <div class="category-group">
                                                        <h6 class="category-title">{{ $category }}</h6>
                                                        <div class="violation-badges">
                                                            @foreach($reasons as $reason)
                                                                <span class="violation-badge">{{ $reason }}</span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="violation-details">
                                            <h5 class="violation-section-title">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Report Reason
                                            </h5>
                                            <p class="reason-text">{{ $selectedReport->reason }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Reporter Information Section -->
                            <div class="detail-section">
                                <div class="section-header">
                                    <div class="section-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <h4>Reporter Information</h4>
                                </div>
                                <div class="section-content">
                                    <div class="reporter-info">
                                        <div class="reporter-avatar">
                                            <i class="fas fa-user-circle"></i>
                                        </div>
                                        <div class="reporter-details">
                                            <div class="info-item">
                                                <span class="info-label">Name</span>
                                                <span class="info-value">{{ $selectedReport->reporter->name }}</span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Email</span>
                                                <span class="info-value">{{ $selectedReport->reporter->email }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Reported Post Section -->
                            <div class="detail-section">
                                <div class="section-header">
                                    <div class="section-icon">
                                        <i class="fas fa-file-text"></i>
                                    </div>
                                    <h4>Reported Post</h4>
                                </div>
                                <div class="section-content">
                                    <div class="post-container">
                                        <livewire:admin.post-view :post="$selectedReport->post" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        @if($selectedReport->status === 'pending')
                            <div class="action-buttons">
                                <button class="action-btn archive-btn" wire:click="$set('showArchiveConfirm', true)">
                                    <i class="fas fa-archive"></i>
                                    <span>Archive Report</span>
                                </button>
                                <button class="action-btn approve-btn" wire:click="$set('showApproveConfirm', true)">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Approve Report</span>
                                </button>
                            </div>
                        @else
                            <div class="status-message">
                                <i class="fas fa-info-circle"></i>
                                <span>This report has been {{ $selectedReport->status }}.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if($showApproveConfirm)
        <div class="approve-modal-overlay" id="approveReportModal">
            <div class="approve-modal-content">
                <div class="approve-modal-header">
                    <div class="approve-modal-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="approve-modal-title">
                        <h3>Confirm Report Approval</h3>
                        <p class="approve-modal-subtitle">This action cannot be undone</p>
                    </div>
                </div>
                <div class="approve-modal-body">
                    <div class="warning-content">
                        <div class="warning-message">
                            <p>Are you sure you want to approve this report and take down the post?</p>
                        </div>
                        <div class="action-consequences">
                            <h4>This will:</h4>
                            <ul>
                                <li><i class="fas fa-check"></i> Mark the report as resolved</li>
                                <li><i class="fas fa-eye-slash"></i> Hide the post from public view</li>
                                <li><i class="fas fa-bell"></i> Notify the post author</li>
                                <li><i class="fas fa-ban"></i> Apply takedown notice to shared posts</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="approve-modal-footer">
                    <button type="button" class="approve-cancel-btn" wire:click="$set('showApproveConfirm', false)">
                        <i class="fas fa-times"></i>
                        <span>Cancel</span>
                    </button>
                    <button type="button" class="approve-submit-btn" wire:click="approveReport({{ $selectedReport->id }})">
                        <i class="fas fa-gavel"></i>
                        <span>Approve & Take Down</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($showArchiveConfirm)
        <div class="archive-modal-overlay" id="archiveReportModal">
            <div class="archive-modal-content">
                <div class="archive-modal-header">
                    <div class="archive-modal-icon">
                        <i class="fas fa-archive"></i>
                    </div>
                    <div class="archive-modal-title">
                        <h3>Confirm Report Archive</h3>
                        <p class="archive-modal-subtitle">Dismiss this report without action</p>
                    </div>
                </div>
                <div class="archive-modal-body">
                    <div class="info-content">
                        <div class="info-message">
                            <p>Are you sure you want to archive this report without taking action?</p>
                        </div>
                        <div class="action-consequences">
                            <h4>This will:</h4>
                            <ul>
                                <li><i class="fas fa-archive"></i> Mark the report as dismissed</li>
                                <li><i class="fas fa-file-alt"></i> Keep the post visible and active</li>
                                <li><i class="fas fa-clock"></i> Record the archive timestamp</li>
                                <li><i class="fas fa-user-shield"></i> Log your admin decision</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="archive-modal-footer">
                    <button type="button" class="archive-cancel-btn" wire:click="$set('showArchiveConfirm', false)">
                        <i class="fas fa-times"></i>
                        <span>Cancel</span>
                    </button>
                    <button type="button" class="archive-submit-btn" wire:click="archiveReport({{ $selectedReport->id }})">
                        <i class="fas fa-archive"></i>
                        <span>Archive Report</span>
                    </button>
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
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1001;
            backdrop-filter: blur(4px);
            animation: fadeIn 0.3s ease-out;
        }

        .approve-modal-content {
            background: white;
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            border: 1px solid #e5e7eb;
            animation: slideUp 0.3s ease-out;
            overflow: hidden;
        }

        .approve-modal-header {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            padding: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            border-bottom: 1px solid #f3f4f6;
        }

        .approve-modal-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
            flex-shrink: 0;
        }

        .approve-modal-icon i {
            color: white;
            font-size: 1.5rem;
        }

        .approve-modal-title h3 {
            margin: 0;
            color: #92400e;
            font-size: 1.375rem;
            font-weight: 700;
        }

        .approve-modal-subtitle {
            margin: 0.25rem 0 0;
            color: #b45309;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .approve-modal-body {
            padding: 2rem;
        }

        .warning-content {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .warning-message p {
            margin: 0;
            color: #374151;
            font-size: 1rem;
            line-height: 1.6;
            font-weight: 500;
        }

        .action-consequences {
            background: #f9fafb;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #e5e7eb;
        }

        .action-consequences h4 {
            margin: 0 0 1rem;
            color: #374151;
            font-size: 0.95rem;
            font-weight: 600;
        }

        .action-consequences ul {
            margin: 0;
            padding: 0;
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .action-consequences li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #4b5563;
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .action-consequences li i {
            color: #6b7280;
            font-size: 0.8rem;
            width: 16px;
            flex-shrink: 0;
        }

        .approve-modal-footer {
            padding: 2rem;
            border-top: 1px solid #e5e7eb;
            background: #f9fafb;
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .approve-cancel-btn,
        .approve-submit-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            font-size: 0.875rem;
        }

        .approve-cancel-btn {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .approve-submit-btn {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            box-shadow: 0 2px 4px rgba(220, 38, 38, 0.2);
        }

        .approve-cancel-btn:hover {
            background: #e5e7eb;
            transform: translateY(-1px);
        }

        .approve-submit-btn:hover {
            background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(220, 38, 38, 0.3);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Archive Modal Styles */
        .archive-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1001;
            backdrop-filter: blur(4px);
            animation: fadeIn 0.3s ease-out;
        }

        .archive-modal-content {
            background: white;
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            border: 1px solid #e5e7eb;
            animation: slideUp 0.3s ease-out;
            overflow: hidden;
        }

        .archive-modal-header {
            background: linear-gradient(135deg, #e0f2fe 0%, #b3e5fc 100%);
            padding: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            border-bottom: 1px solid #f3f4f6;
        }

        .archive-modal-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
            flex-shrink: 0;
        }

        .archive-modal-icon i {
            color: white;
            font-size: 1.5rem;
        }

        .archive-modal-title h3 {
            margin: 0;
            color: #0c4a6e;
            font-size: 1.375rem;
            font-weight: 700;
        }

        .archive-modal-subtitle {
            margin: 0.25rem 0 0;
            color: #0369a1;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .archive-modal-body {
            padding: 2rem;
        }

        .info-content {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .info-message p {
            margin: 0;
            color: #374151;
            font-size: 1rem;
            line-height: 1.6;
            font-weight: 500;
        }

        .archive-modal-footer {
            padding: 2rem;
            border-top: 1px solid #e5e7eb;
            background: #f9fafb;
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .archive-cancel-btn,
        .archive-submit-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            font-size: 0.875rem;
        }

        .archive-cancel-btn {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .archive-submit-btn {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: white;
            box-shadow: 0 2px 4px rgba(2, 132, 199, 0.2);
        }

        .archive-cancel-btn:hover {
            background: #e5e7eb;
            transform: translateY(-1px);
        }

        .archive-submit-btn:hover {
            background: linear-gradient(135deg, #0369a1 0%, #075985 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(2, 132, 199, 0.3);
        }

        .reports-list {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .report-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
            transition: all 0.2s ease;
            overflow: hidden;
        }

        .report-card:hover {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07), 0 2px 4px rgba(0, 0, 0, 0.06);
            transform: translateY(-1px);
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 1.5rem 1.5rem 0 1.5rem;
            margin-bottom: 1rem;
        }

        .report-title-section {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .report-id-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            font-size: 1.1rem;
            color: #1f2937;
        }

        .report-id-badge i {
            color: #dc2626;
            font-size: 1rem;
        }

        .report-timestamp {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #6b7280;
        }

        .report-timestamp i {
            font-size: 0.75rem;
        }

        .view-details-btn {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            color: white;
            border: none;
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .view-details-btn:hover {
            background: linear-gradient(135deg, #374151 0%, #4b5563 100%);
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
        }

        .view-details-btn:active {
            transform: translateY(0);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .report-body {
            padding: 0 1.5rem 1.5rem 1.5rem;
        }

        .violations-section {
            margin-bottom: 1.25rem;
        }

        .violation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .violation-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
            font-size: 0.95rem;
            font-weight: 600;
            color: #374151;
        }

        .violation-title i {
            color: #f59e0b;
            font-size: 0.875rem;
        }

        .violation-count-badge {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .violation-description {
            margin: 0;
            color: #4b5563;
            line-height: 1.6;
            font-size: 0.9rem;
        }

        .violation-badges-grid {
            display: grid;
            grid-template-columns: repeat(3, max-content);
            gap: 0.375rem;
            max-width: 100%;
            justify-content: start;
        }

        .violation-badge-item {
            background: #f3f4f6;
            color: #4b5563;
            padding: 0.375rem 0.5rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
            text-align: center;
            line-height: 1.2;
            border: 1px solid #e5e7eb;
        }

        .violation-badge-more {
            background: #e5e7eb;
            color: #6b7280;
            padding: 0.375rem 0.5rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
            text-align: center;
            line-height: 1.2;
            border: 1px solid #d1d5db;
            font-style: italic;
        }

        /* Responsive adjustments for violation badges */
        @media (max-width: 768px) {
            .violation-badges-grid {
                grid-template-columns: repeat(3, max-content);
                gap: 0.25rem;
            }

            .violation-badge-item,
            .violation-badge-more {
                padding: 0.25rem 0.375rem;
                font-size: 0.7rem;
            }
        }

        @media (max-width: 480px) {
            .violation-badges-grid {
                grid-template-columns: repeat(2, max-content);
                gap: 0.25rem;
            }

            .violation-badge-item,
            .violation-badge-more {
                padding: 0.25rem 0.375rem;
                font-size: 0.65rem;
            }
        }

        .report-metadata {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #f3f4f6;
        }

        .metadata-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .metadata-icon {
            width: 32px;
            height: 32px;
            background: #f3f4f6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .metadata-icon i {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .metadata-content {
            display: flex;
            flex-direction: column;
            gap: 0.125rem;
        }

        .metadata-label {
            font-size: 0.75rem;
            color: #9ca3af;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .metadata-value {
            font-size: 0.875rem;
            color: #374151;
            font-weight: 500;
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
            padding: 2rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            position: sticky;
            top: 0;
            z-index: 2;
            border-top-left-radius: 1.2rem;
            border-top-right-radius: 1.2rem;
        }

        .modal-title-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .modal-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        .modal-icon i {
            color: white;
            font-size: 1.25rem;
        }

        .modal-title-content h3 {
            margin: 0;
            color: #1f2937;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .modal-subtitle {
            color: #6b7280;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .report-modal .close-btn {
            background: #f3f4f6;
            border: none;
            font-size: 1.1rem;
            color: #6b7280;
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .report-modal .close-btn:hover {
            background: #e5e7eb;
            color: #374151;
            transform: scale(1.05);
        }

        .report-modal .modal-body {
            padding: 2rem;
            background: #f9fafb;
        }

        .report-modal .detail-section {
            margin-bottom: 2rem;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
            border: 1px solid #e5e7eb;
        }

        .report-modal .detail-section:last-child {
            margin-bottom: 0;
        }

        .section-header {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-icon {
            width: 36px;
            height: 36px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .section-icon i {
            color: #6366f1;
            font-size: 1rem;
        }

        .section-header h4 {
            margin: 0;
            color: #1f2937;
            font-size: 1.125rem;
            font-weight: 600;
        }

        .section-content {
            padding: 1.5rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .info-label {
            font-size: 0.75rem;
            color: #9ca3af;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .info-value {
            font-size: 0.95rem;
            color: #374151;
            font-weight: 500;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.75rem;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-resolved {
            background: #d1fae5;
            color: #065f46;
        }

        .status-dismissed {
            background: #f3f4f6;
            color: #374151;
        }

        .violation-section-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0 0 1rem;
            font-size: 1rem;
            font-weight: 600;
            color: #374151;
        }

        .violation-section-title i {
            color: #f59e0b;
        }

        .violation-categories {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .category-group {
            background: #f9fafb;
            border-radius: 12px;
            padding: 1rem;
            border: 1px solid #e5e7eb;
        }

        .category-title {
            margin: 0 0 0.75rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
        }

        .violation-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .violation-badge {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: white;
            padding: 0.375rem 0.75rem;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .reason-text {
            margin: 0;
            color: #4b5563;
            line-height: 1.6;
            font-size: 0.95rem;
            background: #f9fafb;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .reporter-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .reporter-avatar {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .reporter-avatar i {
            color: white;
            font-size: 1.5rem;
        }

        .reporter-details {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            flex: 1;
        }

        .post-container {
            background: #f9fafb;
            border-radius: 12px;
            padding: 1rem;
            border: 1px solid #e5e7eb;
        }

        .report-modal .modal-footer {
            padding: 2rem;
            border-top: 1px solid #e5e7eb;
            background: white;
            display: flex;
            justify-content: center;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
        }

        .action-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
            font-size: 0.875rem;
        }

        .archive-btn {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .approve-btn {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: white;
            box-shadow: 0 2px 4px rgba(220, 38, 38, 0.2);
        }

        .archive-btn:hover {
            background: #e5e7eb;
            transform: translateY(-1px);
        }

        .approve-btn:hover {
            background: linear-gradient(135deg, #b91c1c 0%, #dc2626 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(220, 38, 38, 0.3);
        }

        .status-message {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #6b7280;
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Hide Scrollbar */
        .report-modal .modal-content {
            scrollbar-width: none;
            /* Firefox */
            -ms-overflow-style: none;
            /* Internet Explorer 10+ */
        }

        .report-modal .modal-content::-webkit-scrollbar {
            display: none;
            /* WebKit */
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .reports-list {
                gap: 1rem;
            }

            .report-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
                padding: 1.25rem 1.25rem 0 1.25rem;
            }

            .report-title-section {
                width: 100%;
            }

            .view-details-btn {
                align-self: flex-end;
                padding: 0.5rem 1rem;
                font-size: 0.8rem;
            }

            .report-body {
                padding: 0 1.25rem 1.25rem 1.25rem;
            }

            .report-metadata {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }

            .metadata-item {
                gap: 0.5rem;
            }

            .metadata-icon {
                width: 28px;
                height: 28px;
            }
        }

        @media (max-width: 768px) {
            .report-modal .modal-content {
                width: 95%;
                margin: 1rem;
                max-height: 95vh;
            }

            .report-modal .modal-header {
                padding: 1.5rem;
            }

            .modal-title-section {
                gap: 0.75rem;
            }

            .modal-icon {
                width: 40px;
                height: 40px;
            }

            .modal-title-content h3 {
                font-size: 1.25rem;
            }

            .report-modal .modal-body {
                padding: 1.5rem;
            }

            .section-header {
                padding: 1.25rem;
            }

            .section-content {
                padding: 1.25rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .reporter-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .reporter-avatar {
                width: 50px;
                height: 50px;
            }

            .action-buttons {
                flex-direction: column;
                width: 100%;
            }

            .action-btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 640px) {
            .report-modal .modal-content {
                width: 95%;
                margin: 1rem;
            }

            .report-modal .modal-header {
                padding: 1rem;
            }

            .modal-title-section {
                gap: 0.5rem;
            }

            .modal-icon {
                width: 36px;
                height: 36px;
            }

            .modal-title-content h3 {
                font-size: 1.125rem;
            }

            .modal-subtitle {
                font-size: 0.8rem;
            }

            .report-modal .modal-body {
                padding: 1rem;
            }

            .report-modal .modal-footer {
                padding: 1rem;
            }

            .section-header {
                padding: 1rem;
                gap: 0.5rem;
            }

            .section-icon {
                width: 32px;
                height: 32px;
            }

            .section-content {
                padding: 1rem;
            }

            .violation-categories {
                gap: 0.75rem;
            }

            .category-group {
                padding: 0.75rem;
            }

            .violation-badges {
                gap: 0.375rem;
            }

            .reporter-avatar {
                width: 44px;
                height: 44px;
            }

            .reporter-avatar i {
                font-size: 1.25rem;
            }

            .report-header {
                padding: 1rem 1rem 0 1rem;
            }

            .report-body {
                padding: 0 1rem 1rem 1rem;
            }

            .violation-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .view-details-btn {
                width: 100%;
                justify-content: center;
            }
        }

        /* Enhanced Alert Styles with Slide Down Animation */
        .alert {
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-left: 4px solid;
            animation: slideDownFromTop 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
            transform-origin: top center;
        }

        .alert i {
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .alert-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border-left-color: #10b981;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .alert-success i {
            color: #10b981;
        }

        .alert-error {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border-left-color: #ef4444;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }

        .alert-error i {
            color: #ef4444;
        }

        .alert-info {
            background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
            color: #075985;
            border-left-color: #0284c7;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2);
        }

        .alert-info i {
            color: #0284c7;
        }

        .alert::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            border-radius: 12px 12px 0 0;
            animation: progressBar 4s linear;
        }

        .alert-success::before {
            background: linear-gradient(90deg, #10b981 0%, #059669 100%);
        }

        .alert-error::before {
            background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);
        }

        .alert-info::before {
            background: linear-gradient(90deg, #0284c7 0%, #0369a1 100%);
        }

        @keyframes slideDownFromTop {
            0% {
                transform: translateY(-100px) scale(0.95);
                opacity: 0;
            }

            60% {
                transform: translateY(10px) scale(1.02);
                opacity: 0.9;
            }

            100% {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }

        @keyframes progressBar {
            0% {
                width: 100%;
            }

            100% {
                width: 0%;
            }
        }

        /* Auto-hide animation */
        .alert.fade-out {
            animation: slideUpAndFade 0.5s ease-in-out forwards;
        }

        @keyframes slideUpAndFade {
            0% {
                transform: translateY(0) scale(1);
                opacity: 1;
            }

            100% {
                transform: translateY(-50px) scale(0.95);
                opacity: 0;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Auto-hide alerts after 5 seconds
            function setupAlertAutoHide() {
                const alerts = document.querySelectorAll('.alert:not(.processed)');

                alerts.forEach(function (alert) {
                    alert.classList.add('processed');

                    // Add click to dismiss functionality
                    alert.style.cursor = 'pointer';
                    alert.addEventListener('click', function () {
                        dismissAlert(alert);
                    });

                    // Auto-hide after 5 seconds
                    setTimeout(function () {
                        if (alert && alert.parentNode) {
                            dismissAlert(alert);
                        }
                    }, 5000);
                });
            }

            function dismissAlert(alert) {
                alert.classList.add('fade-out');
                setTimeout(function () {
                    if (alert && alert.parentNode) {
                        alert.remove();
                    }
                }, 500);
            }

            // Run on page load
            setupAlertAutoHide();

            // Run when Livewire updates the page
            document.addEventListener('livewire:navigated', setupAlertAutoHide);
            document.addEventListener('livewire:load', setupAlertAutoHide);

            // For Livewire v3
            Livewire.hook('morph.updated', () => {
                setTimeout(setupAlertAutoHide, 100);
            });

            // Listen for Livewire events and trigger global notifications
            document.addEventListener('livewire:init', () => {
                Livewire.on('showNotification', (message, type) => {
                    if (window.showNotification) {
                        window.showNotification(message, type);
                    }
                });
            });

            // Check for session flash messages and trigger global notifications
            @if(session('success'))
                if (window.showNotification) {
                    window.showNotification('{{ session('success') }}', 'success');
                }
            @endif

            @if(session('error'))
                if (window.showNotification) {
                    window.showNotification('{{ session('error') }}', 'error');
                }
            @endif

            @if(session('message'))
                if (window.showNotification) {
                    window.showNotification('{{ session('message') }}', 'success');
                }
            @endif
        });
    </script>
</div>