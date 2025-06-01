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
        </div>
    </div>

    <style>
        .charts-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-top: 2rem;
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
        }

        .chart-wrapper canvas {
            max-height: 100%;
            width: 100% !important;
            height: auto !important;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .charts-container {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .chart-card {
                padding: 1rem;
            }

            .chart-wrapper {
                height: 250px;
            }
        }
    </style>

    <script>
        // Global chart instances
        window.statusBarChart = null;
        window.trendLineChart = null;
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
            if (!chartData || !chartData.statusCounts || !chartData.trend) {
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

                console.log('Charts updated with new data');
            } catch (error) {
                console.error('Error updating charts:', error);
                // Fallback: reinitialize charts
                window.chartsInitialized = false;
                initializeCharts();
            }
        }

        function initializeCharts() {
            const chartData = @json($chartData);

            // Debug: Log chart data
            console.log('Initializing charts with data:', chartData);

            // Validate chart data
            if (!chartData || !chartData.statusCounts || !chartData.trend) {
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
        }
    </script>
</div>