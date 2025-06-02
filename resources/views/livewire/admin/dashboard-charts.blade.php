<div>
    <div wire:poll.30s>
        <div class="charts-container">
            <!-- Bar Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-chart-bar"></i>
                        Reports Overview
                    </h3>
                </div>
                <div class="chart-wrapper">
                    <canvas id="statusBarChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Line Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-chart-line"></i>
                        Reports Trend (Last 7 Days)
                    </h3>
                </div>
                <div class="chart-wrapper">
                    <canvas id="trendLineChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Post Status Section -->
            <div class="post-status-section">
                <!-- Post Status Chart -->
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">
                            <i class="fas fa-chart-bar"></i>
                            Post Status Overview
                        </h3>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="postStatusChart" width="400" height="200"></canvas>
                    </div>

                    <!-- Chart Insights -->
                    <div class="chart-insights">
                        <div class="insight-item" id="dominantStatus">
                            <i class="fas fa-chart-line insight-icon"></i>
                            <span class="insight-text">
                                @php
                                    $foundCount = $chartData['postStatus']['data'][0] ?? 0;
                                    $notFoundCount = $chartData['postStatus']['data'][1] ?? 0;
                                    $total = $foundCount + $notFoundCount;
                                    if ($total > 0) {
                                        if ($foundCount > $notFoundCount) {
                                            $percentage = round(($foundCount / $total) * 100);
                                            echo "Most posts are currently found ({$percentage}%)";
                                        } else {
                                            $percentage = round(($notFoundCount / $total) * 100);
                                            echo "Most posts are currently not found ({$percentage}%)";
                                        }
                                    } else {
                                        echo "No posts available";
                                    }
                                @endphp
                            </span>
                        </div>

                        <div class="insight-item" id="todaysPosts">
                            <i class="fas fa-plus-circle insight-icon"></i>
                            <span class="insight-text">
                                @php
                                    $todaysPosts = \App\Models\Post::whereDate('created_at', today())->count();
                                    echo $todaysPosts === 1 ? "1 new post added today" : "{$todaysPosts} new posts added today";
                                @endphp
                            </span>
                        </div>

                        <div class="insight-item" id="lastUpdated">
                            <i class="fas fa-clock insight-icon"></i>
                            <span class="insight-text">Last updated: <span id="updateTime">just now</span></span>
                        </div>
                    </div>
                </div>

                <!-- Post Status Statistics -->
                <div class="stats-card">
                    <div class="chart-header">
                        <h3 class="chart-title">
                            <i class="fas fa-chart-bar"></i>
                            Status Statistics
                        </h3>
                    </div>
                    <div class="stats-wrapper">
                        <div class="stat-item found-stat">
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Found Posts</div>
                                <div class="stat-value" id="foundCount">{{ $chartData['postStatus']['data'][0] ?? 0 }}
                                </div>
                                <div class="stat-percentage" id="foundPercentage">
                                    {{ $chartData['postStatus']['data'][0] + $chartData['postStatus']['data'][1] > 0 ?
    round(($chartData['postStatus']['data'][0] / ($chartData['postStatus']['data'][0] + $chartData['postStatus']['data'][1])) * 100, 1) : 0 }}%
                                </div>
                            </div>
                        </div>

                        <div class="stat-item not-found-stat">
                            <div class="stat-icon">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Not Found Posts</div>
                                <div class="stat-value" id="notFoundCount">
                                    {{ $chartData['postStatus']['data'][1] ?? 0 }}
                                </div>
                                <div class="stat-percentage" id="notFoundPercentage">
                                    {{ $chartData['postStatus']['data'][0] + $chartData['postStatus']['data'][1] > 0 ?
    round(($chartData['postStatus']['data'][1] / ($chartData['postStatus']['data'][0] + $chartData['postStatus']['data'][1])) * 100, 1) : 0 }}%
                                </div>
                            </div>
                        </div>

                        <div class="stat-item total-stat">
                            <div class="stat-icon">
                                <i class="fas fa-list"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Total Posts</div>
                                <div class="stat-value" id="totalPosts">
                                    {{ ($chartData['postStatus']['data'][0] ?? 0) + ($chartData['postStatus']['data'][1] ?? 0) }}
                                </div>
                                <div class="stat-percentage">100%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .charts-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-top: 2rem;
        }

        .post-status-section {
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .stats-card {
            background: var(--admin-bg);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(27, 67, 50, 0.05);
            border: 1px solid rgba(27, 67, 50, 0.08);
        }

        .stats-wrapper {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border-radius: 0.75rem;
            background: rgba(27, 67, 50, 0.02);
            border: 1px solid rgba(27, 67, 50, 0.05);
            transition: all 0.2s ease;
        }

        .stat-item:hover {
            background: rgba(27, 67, 50, 0.04);
            border-color: rgba(27, 67, 50, 0.1);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .found-stat .stat-icon {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .not-found-stat .stat-icon {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .total-stat .stat-icon {
            background: rgba(27, 67, 50, 0.1);
            color: #1b4332;
        }

        .stat-content {
            flex: 1;
        }

        .stat-label {
            font-size: 0.875rem;
            color: #6b7280;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .stat-value {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--admin-primary);
            line-height: 1;
            margin-bottom: 0.25rem;
        }

        .stat-percentage {
            font-size: 0.875rem;
            font-weight: 600;
            color: #6b7280;
        }

        .found-stat .stat-percentage {
            color: #10b981;
        }

        .not-found-stat .stat-percentage {
            color: #ef4444;
        }

        .chart-card {
            background: var(--admin-bg);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(27, 67, 50, 0.05);
            border: 1px solid rgba(27, 67, 50, 0.08);
        }

        .chart-header {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(27, 67, 50, 0.1);
        }

        .chart-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--admin-primary);
            margin: 0;
        }

        .chart-title i {
            color: var(--admin-primary);
            font-size: 1rem;
        }

        .chart-wrapper {
            position: relative;
            height: 300px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chart-wrapper canvas {
            max-height: 100%;
            width: 100% !important;
            height: auto !important;
        }

        /* Specific styling for post status chart to increase height */
        .post-status-section .chart-wrapper {
            height: 240px;
        }

        /* Chart Insights Styling */
        .chart-insights {
            margin-top: 1.5rem;
            padding: 1rem;
            background: rgba(27, 67, 50, 0.02);
            border-radius: 0.75rem;
            border: 1px solid rgba(27, 67, 50, 0.05);
        }

        .insight-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            font-size: 0.875rem;
            color: #6b7280;
        }

        .insight-item:last-child {
            margin-bottom: 0;
        }

        .insight-icon {
            width: 16px;
            height: 16px;
            color: var(--admin-primary);
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        .insight-text {
            line-height: 1.4;
            font-weight: 500;
        }

        /* Color coding for different insight types */
        .insight-item:first-child .insight-icon {
            color: #1b4332;
        }

        .insight-item:nth-child(2) .insight-icon {
            color: #10b981;
        }

        .insight-item:last-child .insight-icon {
            color: #6b7280;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .charts-container {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .post-status-section {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
        }

        @media (max-width: 768px) {

            .chart-card,
            .stats-card {
                padding: 1rem;
            }

            .chart-wrapper {
                height: 250px;
            }

            .post-status-section .chart-wrapper {
                height: 200px;
            }

            .chart-insights {
                margin-top: 1rem;
                padding: 0.75rem;
            }

            .insight-item {
                font-size: 0.8rem;
                margin-bottom: 0.5rem;
            }

            .stat-item {
                padding: 0.75rem;
            }

            .stat-icon {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }

            .stat-value {
                font-size: 1.5rem;
            }

            .stats-wrapper {
                gap: 1rem;
            }
        }
    </style>

    <script>
        // Global chart instances
        window.statusBarChart = null;
        window.trendLineChart = null;
        window.postStatusChart = null;
        window.chartsInitialized = false;

        // Initialize charts when everything is ready
        function initCharts() {
            console.log('Attempting to initialize charts...');
            console.log('Chart available:', typeof Chart !== 'undefined');

            if (typeof Chart !== 'undefined') {
                if (!window.chartsInitialized) {
                    initializeCharts();
                    window.chartsInitialized = true;
                } else {
                    updateCharts();
                }
            } else {
                console.error('Chart.js is not available');
            }
        }

        // Try multiple initialization methods
        document.addEventListener('DOMContentLoaded', initCharts);

        // Also try after a short delay to ensure all scripts are loaded
        setTimeout(initCharts, 500);

        // Listen for Livewire updates - use smarter update strategy
        document.addEventListener('livewire:updated', function () {
            console.log('Livewire updated (30s interval), updating chart data...');
            setTimeout(initCharts, 100);
        });

        // Function to update existing charts with new data
        function updateCharts() {
            const chartData = @json($chartData);

            // Validate chart data
            if (!chartData || !chartData.statusCounts || !chartData.trend || !chartData.postStatus) {
                console.error('Invalid chart data for update:', chartData);
                return;
            }

            try {
                // Update bar chart data
                if (window.statusBarChart && window.statusBarChart.data) {
                    window.statusBarChart.data.labels = chartData.statusCounts.labels;
                    window.statusBarChart.data.datasets[0].data = chartData.statusCounts.data;
                    window.statusBarChart.data.datasets[0].backgroundColor = chartData.statusCounts.colors;
                    window.statusBarChart.data.datasets[0].borderColor = chartData.statusCounts.colors;
                    window.statusBarChart.update('none'); // 'none' for no animation
                } else {
                    console.warn('Bar chart not available for update, reinitializing...');
                    window.chartsInitialized = false;
                    initializeCharts();
                    return;
                }

                // Update line chart data
                if (window.trendLineChart && window.trendLineChart.data) {
                    window.trendLineChart.data.labels = chartData.trend.labels;
                    window.trendLineChart.data.datasets[0].data = chartData.trend.data;
                    window.trendLineChart.update('none'); // 'none' for no animation
                } else {
                    console.warn('Line chart not available for update, reinitializing...');
                    window.chartsInitialized = false;
                    initializeCharts();
                    return;
                }

                // Update post status chart data
                if (window.postStatusChart && window.postStatusChart.data) {
                    const foundCount = chartData.postStatus.data[0] || 0;
                    const notFoundCount = chartData.postStatus.data[1] || 0;
                    const totalCount = foundCount + notFoundCount;

                    window.postStatusChart.data.labels = ['Found Posts', 'Not Found Posts', 'Total Posts'];
                    window.postStatusChart.data.datasets[0].data = [foundCount, notFoundCount, totalCount];
                    window.postStatusChart.data.datasets[0].backgroundColor = [
                        '#10b981', // Green for found
                        '#ef4444', // Red for not found
                        '#1b4332'  // Dark green for total
                    ];
                    window.postStatusChart.data.datasets[0].borderColor = [
                        '#10b981',
                        '#ef4444',
                        '#1b4332'
                    ];
                    window.postStatusChart.update('none'); // 'none' for no animation
                } else {
                    console.warn('Post status chart not available for update, reinitializing...');
                    window.chartsInitialized = false;
                    initializeCharts();
                    return;
                }

                // Update statistics
                updateStatistics(chartData.postStatus);

                // Update insights
                updateInsights(chartData.postStatus);

                console.log('Charts updated with new data');
            } catch (error) {
                console.error('Error updating charts:', error);
                // Fallback: reinitialize charts
                window.chartsInitialized = false;
                initializeCharts();
            }
        }

        // Function to update statistics display
        function updateStatistics(postStatusData) {
            const foundCount = postStatusData.data[0] || 0;
            const notFoundCount = postStatusData.data[1] || 0;
            const totalCount = foundCount + notFoundCount;

            // Update counts
            const foundCountEl = document.getElementById('foundCount');
            const notFoundCountEl = document.getElementById('notFoundCount');
            const totalPostsEl = document.getElementById('totalPosts');

            if (foundCountEl) foundCountEl.textContent = foundCount;
            if (notFoundCountEl) notFoundCountEl.textContent = notFoundCount;
            if (totalPostsEl) totalPostsEl.textContent = totalCount;

            // Update percentages
            const foundPercentageEl = document.getElementById('foundPercentage');
            const notFoundPercentageEl = document.getElementById('notFoundPercentage');

            if (foundPercentageEl) {
                const foundPercentage = totalCount > 0 ? ((foundCount / totalCount) * 100).toFixed(1) : 0;
                foundPercentageEl.textContent = foundPercentage + '%';
            }

            if (notFoundPercentageEl) {
                const notFoundPercentage = totalCount > 0 ? ((notFoundCount / totalCount) * 100).toFixed(1) : 0;
                notFoundPercentageEl.textContent = notFoundPercentage + '%';
            }
        }

        // Function to update insights display
        function updateInsights(postStatusData) {
            const foundCount = postStatusData.data[0] || 0;
            const notFoundCount = postStatusData.data[1] || 0;
            const totalCount = foundCount + notFoundCount;

            // Update dominant status insight
            const dominantStatusEl = document.querySelector('#dominantStatus .insight-text');
            if (dominantStatusEl && totalCount > 0) {
                if (foundCount > notFoundCount) {
                    const percentage = Math.round((foundCount / totalCount) * 100);
                    dominantStatusEl.textContent = `Most posts are currently found (${percentage}%)`;
                } else if (notFoundCount > foundCount) {
                    const percentage = Math.round((notFoundCount / totalCount) * 100);
                    dominantStatusEl.textContent = `Most posts are currently not found (${percentage}%)`;
                } else {
                    dominantStatusEl.textContent = 'Posts are evenly split between found and not found';
                }
            } else if (dominantStatusEl) {
                dominantStatusEl.textContent = 'No posts available';
            }

            // Update last updated time
            const updateTimeEl = document.getElementById('updateTime');
            if (updateTimeEl) {
                const now = new Date();
                const timeString = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                updateTimeEl.textContent = `${timeString}`;
            }
        }

        function initializeCharts() {
            const chartData = @json($chartData);

            // Debug: Log chart data
            console.log('Initializing charts with data:', chartData);

            // Validate chart data
            if (!chartData || !chartData.statusCounts || !chartData.trend || !chartData.postStatus) {
                console.error('Invalid chart data:', chartData);
                return;
            }

            // Only destroy if we're reinitializing
            if (window.statusBarChart && typeof window.statusBarChart.destroy === 'function') {
                window.statusBarChart.destroy();
                window.statusBarChart = null;
            }
            if (window.trendLineChart && typeof window.trendLineChart.destroy === 'function') {
                window.trendLineChart.destroy();
                window.trendLineChart = null;
            }
            if (window.postStatusChart && typeof window.postStatusChart.destroy === 'function') {
                window.postStatusChart.destroy();
                window.postStatusChart = null;
            }

            // Bar Chart
            const barCtx = document.getElementById('statusBarChart');
            if (barCtx) {
                try {
                    window.statusBarChart = new Chart(barCtx, {
                        type: 'bar',
                        data: {
                            labels: chartData.statusCounts.labels,
                            datasets: [{
                                label: 'Count',
                                data: chartData.statusCounts.data,
                                backgroundColor: chartData.statusCounts.colors,
                                borderColor: chartData.statusCounts.colors,
                                borderWidth: 1,
                                borderRadius: 8,
                                borderSkipped: false,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: {
                                duration: 0 // Disable animations for smoother updates
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(27, 67, 50, 0.9)',
                                    titleColor: '#ffffff',
                                    bodyColor: '#ffffff',
                                    borderColor: '#1b4332',
                                    borderWidth: 1,
                                    cornerRadius: 8,
                                    displayColors: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        color: '#6b7280',
                                        font: {
                                            size: 12
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(107, 114, 128, 0.1)',
                                        borderColor: 'rgba(107, 114, 128, 0.2)'
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: '#6b7280',
                                        font: {
                                            size: 12
                                        }
                                    },
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                } catch (error) {
                    console.error('Error creating bar chart:', error);
                }
            }

            // Line Chart
            const lineCtx = document.getElementById('trendLineChart');
            if (lineCtx) {
                try {
                    window.trendLineChart = new Chart(lineCtx, {
                        type: 'line',
                        data: {
                            labels: chartData.trend.labels,
                            datasets: [{
                                label: 'Reports',
                                data: chartData.trend.data,
                                borderColor: '#1b4332',
                                backgroundColor: 'rgba(27, 67, 50, 0.1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: '#1b4332',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 6,
                                pointHoverRadius: 8
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: {
                                duration: 0 // Disable animations for smoother updates
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(27, 67, 50, 0.9)',
                                    titleColor: '#ffffff',
                                    bodyColor: '#ffffff',
                                    borderColor: '#1b4332',
                                    borderWidth: 1,
                                    cornerRadius: 8,
                                    displayColors: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        color: '#6b7280',
                                        font: {
                                            size: 12
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(107, 114, 128, 0.1)',
                                        borderColor: 'rgba(107, 114, 128, 0.2)'
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: '#6b7280',
                                        font: {
                                            size: 12
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(107, 114, 128, 0.1)',
                                        borderColor: 'rgba(107, 114, 128, 0.2)'
                                    }
                                }
                            }
                        }
                    });
                } catch (error) {
                    console.error('Error creating line chart:', error);
                }
            }

            // Post Status Chart (Horizontal Bar)
            const postStatusCtx = document.getElementById('postStatusChart');
            if (postStatusCtx) {
                try {
                    const foundCount = chartData.postStatus.data[0] || 0;
                    const notFoundCount = chartData.postStatus.data[1] || 0;
                    const totalCount = foundCount + notFoundCount;

                    window.postStatusChart = new Chart(postStatusCtx, {
                        type: 'bar',
                        data: {
                            labels: ['Found Posts', 'Not Found Posts', 'Total Posts'],
                            datasets: [{
                                label: 'Count',
                                data: [foundCount, notFoundCount, totalCount],
                                backgroundColor: [
                                    '#10b981', // Green for found
                                    '#ef4444', // Red for not found
                                    '#1b4332'  // Dark green for total
                                ],
                                borderColor: [
                                    '#10b981',
                                    '#ef4444',
                                    '#1b4332'
                                ],
                                borderWidth: 2,
                                borderRadius: 8,
                                borderSkipped: false,
                            }]
                        },
                        options: {
                            indexAxis: 'y', // This makes it horizontal
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: {
                                duration: 0 // Disable animations for smoother updates
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(27, 67, 50, 0.9)',
                                    titleColor: '#ffffff',
                                    bodyColor: '#ffffff',
                                    borderColor: '#1b4332',
                                    borderWidth: 1,
                                    cornerRadius: 8,
                                    displayColors: true,
                                    callbacks: {
                                        label: function (context) {
                                            const label = context.label || '';
                                            const value = context.parsed.x;
                                            if (label === 'Total Posts') {
                                                return `${label}: ${value}`;
                                            }
                                            const percentage = totalCount > 0 ? ((value / totalCount) * 100).toFixed(1) : 0;
                                            return `${label}: ${value} (${percentage}%)`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        color: '#6b7280',
                                        font: {
                                            size: 12
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(107, 114, 128, 0.1)',
                                        borderColor: 'rgba(107, 114, 128, 0.2)'
                                    }
                                },
                                y: {
                                    ticks: {
                                        color: '#6b7280',
                                        font: {
                                            size: 12
                                        }
                                    },
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                } catch (error) {
                    console.error('Error creating post status chart:', error);
                }
            }

            // Initialize insights on first load
            updateInsights(chartData.postStatus);
        }
    </script>
</div>