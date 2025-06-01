@extends('layouts.app')

@section('title', 'Settings')

@push('head')
    <link rel="stylesheet" href="{{ asset('css/settings.css') }}">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const themeCards = document.querySelectorAll('.theme-card');

            // Set initial theme from localStorage
            const savedTheme = localStorage.getItem('theme') || 'light';
            updateThemeSelection(savedTheme);

            // Apply saved theme
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-mode');
            } else {
                document.body.classList.remove('dark-mode');
            }

            // Listen for theme card clicks
            themeCards.forEach(card => {
                card.addEventListener('click', function () {
                    const selectedTheme = this.getAttribute('data-theme');

                    // Update theme selection visually
                    updateThemeSelection(selectedTheme);

                    // Apply theme
                    if (selectedTheme === 'dark') {
                        document.body.classList.add('dark-mode');
                        localStorage.setItem('theme', 'dark');
                    } else {
                        document.body.classList.remove('dark-mode');
                        localStorage.setItem('theme', 'light');
                    }

                    // Add selection animation
                    this.classList.add('theme-selected-animation');
                    setTimeout(() => {
                        this.classList.remove('theme-selected-animation');
                    }, 300);
                });
            });

            function updateThemeSelection(theme) {
                themeCards.forEach(card => {
                    if (card.getAttribute('data-theme') === theme) {
                        card.classList.add('selected');
                    } else {
                        card.classList.remove('selected');
                    }
                });
            }

            // Load trash count
            loadTrashCount();

            function loadTrashCount() {
                const trashCountElement = document.getElementById('trash-count');
                if (trashCountElement) {
                    // You can implement an API endpoint to get the actual count
                    // For now, we'll use a placeholder
                    fetch('/api/trash/count', {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            trashCountElement.textContent = data.count || '0';
                        })
                        .catch(error => {
                            console.log('Could not load trash count, using default');
                            trashCountElement.textContent = '0';
                        });
                }
            }
        });
    </script>
@endpush

@section('content')
    <div class="settings-container">
        <h1 class="settings-title">Settings</h1>
        <div class="settings-section">
            <p>Welcome to your settings page. Customize your preferences here.</p>
            <!-- Add your settings form or options here -->
            <div class="theme-option">
                <div class="theme-header">
                    <div class="theme-info">
                        <h2 class="theme-title">
                            <i class="fas fa-palette"></i>
                            Appearance
                        </h2>
                        <p class="theme-description">Choose your preferred theme for the best viewing experience</p>
                    </div>
                </div>

                <div class="theme-selector">
                    <div class="theme-cards">
                        <div class="theme-card light-theme" data-theme="light">
                            <div class="theme-preview">
                                <div class="preview-header"></div>
                                <div class="preview-content">
                                    <div class="preview-line"></div>
                                    <div class="preview-line short"></div>
                                </div>
                            </div>
                            <div class="theme-details">
                                <div class="theme-icon">
                                    <i class="fas fa-sun"></i>
                                </div>
                                <div class="theme-text">
                                    <h3>Light Mode</h3>
                                    <p>Clean and bright interface</p>
                                </div>
                            </div>
                            <div class="theme-check">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>

                        <div class="theme-card dark-theme" data-theme="dark">
                            <div class="theme-preview">
                                <div class="preview-header"></div>
                                <div class="preview-content">
                                    <div class="preview-line"></div>
                                    <div class="preview-line short"></div>
                                </div>
                            </div>
                            <div class="theme-details">
                                <div class="theme-icon">
                                    <i class="fas fa-moon"></i>
                                </div>
                                <div class="theme-text">
                                    <h3>Dark Mode</h3>
                                    <p>Easy on the eyes in low light</p>
                                </div>
                            </div>
                            <div class="theme-check">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="trash-option">
                <div class="trash-header">
                    <div class="trash-info">
                        <h2 class="trash-title">
                            <i class="fas fa-trash-restore"></i>
                            Trash Management
                        </h2>
                        <p class="trash-description">View and manage your deleted posts. Deleted posts are kept for 30 days
                            before being permanently removed.</p>
                    </div>
                </div>

                <div class="trash-content">
                    <div class="trash-card">
                        <div class="trash-visual">
                            <div class="trash-icon-container">
                                <i class="fas fa-trash-alt"></i>
                            </div>
                            <div class="trash-stats">
                                <div class="stat-item">
                                    <span class="stat-number" id="trash-count">--</span>
                                    <span class="stat-label">Deleted Posts</span>
                                </div>
                                <div class="stat-divider"></div>
                                <div class="stat-item">
                                    <span class="stat-number">30</span>
                                    <span class="stat-label">Days Retention</span>
                                </div>
                            </div>
                        </div>

                        <div class="trash-details">
                            <div class="trash-features">
                                <div class="feature-item">
                                    <i class="fas fa-undo"></i>
                                    <span>Restore deleted posts</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-trash"></i>
                                    <span>Permanently delete posts</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-clock"></i>
                                    <span>30-day automatic cleanup</span>
                                </div>
                            </div>

                            <div class="trash-actions">
                                <a href="{{ route('trash.index') }}" class="trash-btn primary">
                                    <i class="fas fa-folder-open"></i>
                                    <span>Open Trash</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection