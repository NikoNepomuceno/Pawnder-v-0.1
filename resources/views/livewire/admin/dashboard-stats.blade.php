<div>
    <div wire:poll.30s>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-flag"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Reports</h3>
                    <p class="stat-number">{{ $totalReports }}</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3>Pending Reports</h3>
                    <p class="stat-number">{{ $pendingReports }}</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon approved">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Approved Reports</h3>
                    <p class="stat-number">{{ $approvedReports }}</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon archived">
                    <i class="fas fa-archive"></i>
                </div>
                <div class="stat-content">
                    <h3>Archived Reports</h3>
                    <p class="stat-number">{{ $archivedReports }}</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .stat-card {
            background: var(--admin-bg);
            border-radius: 1rem;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 2px 8px rgba(27, 67, 50, 0.05);
            text-align: center;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            background: var(--admin-primary);
            color: white;
        }

        .stat-icon.pending {
            background: #f59e0b;
        }

        .stat-icon.approved {
            background: #10b981;
        }

        .stat-icon.archived {
            background: #6b7280;
        }

        .stat-content h3 {
            font-size: 0.9rem;
            color: var(--admin-text-secondary);
            margin: 0;
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--admin-text);
            margin: 0.3rem 0 0;
        }
    </style>
</div>