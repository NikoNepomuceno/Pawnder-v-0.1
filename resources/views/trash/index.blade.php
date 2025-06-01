@extends('layouts.app')

@section('title', 'Trash')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/trash.css') }}">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush

@section('content')
    <div class="trash-container">
        <div class="trash-header">
            <div class="trash-title-section">
                <h1 class="trash-title">
                    <i class="fas fa-trash-alt"></i>
                    Trash
                </h1>
                <p class="trash-subtitle">Deleted posts are kept for 30 days before being permanently removed.</p>
            </div>
            <div class="trash-actions-header">
                <a href="{{ route('settings') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back to Settings
                </a>
            </div>
        </div>

        <div class="trash-content">
            @if ($deletedPosts->isEmpty())
                <div class="empty-trash">
                    <div class="empty-trash-icon">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                    <h3>Trash is empty</h3>
                    <p>You don't have any deleted posts.</p>
                    <a href="{{ route('home') }}" class="create-post-btn">
                        <i class="fas fa-plus"></i>
                        Create a Post
                    </a>
                </div>
            @else
                <div class="trash-stats">
                    <span class="post-count">{{ $deletedPosts->count() }} {{ $deletedPosts->count() === 1 ? 'post' : 'posts' }}
                        in trash</span>
                </div>

                <div class="trash-posts-list">
                    @foreach ($deletedPosts as $post)
                        <div class="trash-post-item" data-post-id="{{ $post->id }}">
                            <div class="trash-post-content">
                                <div class="trash-post-icon">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="trash-post-info">
                                    <h3 class="trash-post-title">
                                        @if($post->isShared())
                                            @php
                                                $original = $post->originalPost;
                                            @endphp
                                            @if($original && !$original->trashed())
                                                {{ $original->title }}
                                                <span class="shared-indicator">
                                                    <i class="fas fa-share-alt"></i>
                                                    Shared
                                                </span>
                                            @else
                                                Shared Post (Original Deleted)
                                            @endif
                                        @else
                                            {{ $post->title }}
                                        @endif
                                    </h3>
                                    <div class="trash-post-meta">
                                        <span class="deleted-time">
                                            <i class="fas fa-trash-alt"></i>
                                            Deleted {{ $post->deleted_at->diffForHumans() }}
                                        </span>
                                        @if(!$post->isShared())
                                            <span class="post-type">
                                                <i class="fas fa-tag"></i>
                                                {{ ucfirst($post->status) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="trash-post-actions">
                                <button class="trash-actions-btn" data-post-id="{{ $post->id }}">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="trash-actions-menu" id="trash-actions-menu-{{ $post->id }}">
                                    <button class="restore-btn" data-post-id="{{ $post->id }}">
                                        <i class="fas fa-undo"></i>
                                        Restore
                                    </button>
                                    <button class="permanent-delete-btn" data-post-id="{{ $post->id }}">
                                        <i class="fas fa-trash"></i>
                                        Delete Forever
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Restore Confirmation Modal -->
    <div id="restoreConfirmModal" class="modal">
        <div class="modal-content">
            <h3>Restore Post</h3>
            <p>Are you sure you want to restore this post? It will be moved back to your active posts.</p>
            <div class="form-actions">
                <button type="button" class="cancel-btn" id="cancelRestoreBtn">Cancel</button>
                <button type="button" class="submit-btn" id="confirmRestoreBtn">Restore</button>
            </div>
        </div>
    </div>

    <!-- Permanent Delete Confirmation Modal -->
    <div id="permanentDeleteConfirmModal" class="modal">
        <div class="modal-content">
            <h3>Permanently Delete Post</h3>
            <p>Are you sure you want to permanently delete this post? This action cannot be undone.</p>
            <div class="form-actions">
                <button type="button" class="cancel-btn" id="cancelPermanentDeleteBtn">Cancel</button>
                <button type="button" class="submit-btn danger-btn" id="confirmPermanentDeleteBtn">Delete Forever</button>
            </div>
        </div>
    </div>

    <script>
        // Trash functionality
        const TrashManager = {
            currentPostId: null,
            modals: {
                restore: null,
                permanentDelete: null
            },

            init() {
                // Initialize modal references
                this.modals.restore = document.getElementById('restoreConfirmModal');
                this.modals.permanentDelete = document.getElementById('permanentDeleteConfirmModal');

                // Bind event listeners
                this.bindEventListeners();
            },

            bindEventListeners() {
                // Three-dot menu handlers
                document.querySelectorAll('.trash-actions-btn').forEach(button => {
                    button.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        const postId = button.getAttribute('data-post-id');
                        const menuId = `trash-actions-menu-${postId}`;
                        const menu = document.getElementById(menuId);

                        if (!menu) {
                            console.error('Menu not found:', menuId);
                            return;
                        }

                        // Close all other menus
                        document.querySelectorAll('.trash-actions-menu').forEach(m => {
                            if (m.id !== menuId) {
                                m.classList.remove('show');
                            }
                        });

                        // Toggle current menu
                        const isShowing = menu.classList.contains('show');
                        if (isShowing) {
                            menu.classList.remove('show');
                            // Reset position
                            menu.style.left = '';
                            menu.style.top = '';
                        } else {
                            // Calculate position relative to the button
                            const buttonRect = button.getBoundingClientRect();
                            const menuWidth = 180; // min-width from CSS
                            const viewportWidth = window.innerWidth;
                            const viewportHeight = window.innerHeight;

                            // Position the menu
                            let left = buttonRect.right - menuWidth;
                            let top = buttonRect.bottom + 4;

                            // Adjust if menu would go off the right edge
                            if (left < 10) {
                                left = buttonRect.left;
                            }

                            // Adjust if menu would go off the bottom edge
                            if (top + 100 > viewportHeight) { // Approximate menu height
                                top = buttonRect.top - 100 - 4;
                            }

                            menu.style.left = left + 'px';
                            menu.style.top = top + 'px';
                            menu.classList.add('show');
                        }
                    });
                });

                // Close menus when clicking outside
                document.addEventListener('click', (e) => {
                    if (!e.target.closest('.trash-post-actions')) {
                        document.querySelectorAll('.trash-actions-menu').forEach(menu => {
                            menu.classList.remove('show');
                            // Reset position
                            menu.style.left = '';
                            menu.style.top = '';
                        });
                    }
                });

                // Restore button handlers
                document.querySelectorAll('.restore-btn').forEach(button => {
                    button.addEventListener('click', (e) => {
                        e.preventDefault();
                        const postId = button.getAttribute('data-post-id');
                        this.showRestoreModal(postId);
                        // Close the menu
                        document.querySelectorAll('.trash-actions-menu').forEach(menu => {
                            menu.classList.remove('show');
                            // Reset position
                            menu.style.left = '';
                            menu.style.top = '';
                        });
                    });
                });

                // Permanent delete button handlers
                document.querySelectorAll('.permanent-delete-btn').forEach(button => {
                    button.addEventListener('click', (e) => {
                        e.preventDefault();
                        const postId = button.getAttribute('data-post-id');
                        this.showPermanentDeleteModal(postId);
                        // Close the menu
                        document.querySelectorAll('.trash-actions-menu').forEach(menu => {
                            menu.classList.remove('show');
                            // Reset position
                            menu.style.left = '';
                            menu.style.top = '';
                        });
                    });
                });

                // Modal action handlers
                document.getElementById('confirmRestoreBtn')?.addEventListener('click', () => {
                    this.restorePost();
                });

                document.getElementById('confirmPermanentDeleteBtn')?.addEventListener('click', () => {
                    this.permanentDeletePost();
                });

                // Cancel button handlers
                document.getElementById('cancelRestoreBtn')?.addEventListener('click', () => {
                    this.hideModal(this.modals.restore);
                });

                document.getElementById('cancelPermanentDeleteBtn')?.addEventListener('click', () => {
                    this.hideModal(this.modals.permanentDelete);
                });

                // Close modals when clicking outside
                window.addEventListener('click', (event) => {
                    Object.values(this.modals).forEach(modal => {
                        if (event.target === modal) {
                            this.hideModal(modal);
                        }
                    });
                });
            },

            showRestoreModal(postId) {
                this.currentPostId = postId;
                this.showModal(this.modals.restore);
            },

            showPermanentDeleteModal(postId) {
                this.currentPostId = postId;
                this.showModal(this.modals.permanentDelete);
            },

            restorePost() {
                if (!this.currentPostId) return;

                fetch(`/trash/${this.currentPostId}/restore`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove the post item from the DOM
                            const postItem = document.querySelector(`.trash-post-item[data-post-id="${this.currentPostId}"]`);
                            if (postItem) {
                                postItem.classList.add('fade-out');
                                setTimeout(() => postItem.remove(), 400);
                            }
                            this.showNotification(data.message || 'Post restored successfully!', 'success');
                            this.updatePostCount();
                        } else {
                            this.showNotification(data.message || 'Failed to restore post.', 'error');
                        }
                    })
                    .catch(error => {
                        this.showNotification('Failed to restore post. Please try again.', 'error');
                        console.error('Error restoring post:', error);
                    })
                    .finally(() => {
                        this.hideModal(this.modals.restore);
                        this.currentPostId = null;
                    });
            },

            permanentDeletePost() {
                if (!this.currentPostId) return;

                fetch(`/trash/${this.currentPostId}/force-delete`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove the post item from the DOM
                            const postItem = document.querySelector(`.trash-post-item[data-post-id="${this.currentPostId}"]`);
                            if (postItem) {
                                postItem.classList.add('fade-out');
                                setTimeout(() => postItem.remove(), 400);
                            }
                            this.showNotification(data.message || 'Post permanently deleted!', 'success');
                            this.updatePostCount();
                        } else {
                            this.showNotification(data.message || 'Failed to permanently delete post.', 'error');
                        }
                    })
                    .catch(error => {
                        this.showNotification('Failed to permanently delete post. Please try again.', 'error');
                        console.error('Error permanently deleting post:', error);
                    })
                    .finally(() => {
                        this.hideModal(this.modals.permanentDelete);
                        this.currentPostId = null;
                    });
            },

            updatePostCount() {
                const remainingPosts = document.querySelectorAll('.trash-post-item:not(.fade-out)').length;
                const countElement = document.querySelector('.post-count');
                if (countElement) {
                    countElement.textContent = `${remainingPosts} ${remainingPosts === 1 ? 'post' : 'posts'} in trash`;
                }

                // Show empty state if no posts remain
                if (remainingPosts === 0) {
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                }
            },

            showModal(modal) {
                if (!modal) return;
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            },

            hideModal(modal) {
                if (!modal) return;
                modal.style.display = 'none';
                document.body.style.overflow = '';
            },

            showNotification(message, type = 'success') {
                // Use the global notification system if available
                if (typeof window.showNotification === 'function') {
                    window.showNotification(message, type);
                } else {
                    // Fallback: alert
                    alert(message);
                }
            }
        };

        // Initialize the TrashManager when the DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            TrashManager.init();
        });
    </script>
@endsection