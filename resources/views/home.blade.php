@extends('layouts.app')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <script src="https://upload-widget.cloudinary.com/global/all.js" type="text/javascript"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endpush

@php
    use App\Helpers\ReactionHelper;
@endphp

@section('content')
    <script>
        function showModal(modal) {
            if (modal) {
                console.log('Showing modal:', modal.id);
                modal.style.display = 'flex';
                modal.style.opacity = '1';
                modal.style.visibility = 'visible';
                document.body.style.overflow = 'hidden';
                if (typeof modal.focus === 'function') modal.focus();
            } else {
                console.error('Modal not found when trying to show it');
            }
        }

        function hideModal(modal) {
            if (modal) {
                console.log('Hiding modal:', modal.id);
                modal.style.display = 'none';
                modal.style.opacity = '0';
                modal.style.visibility = 'hidden';
                document.body.style.overflow = 'auto';
            } else {
                console.error('Modal not found when trying to hide it');
            }
        }

        function validateAndShowConfirmModal() {
            showModal(document.getElementById('createPostConfirmModal'));
        }

        function submitCreatePostForm() {
            document.getElementById('createPostForm').submit();
        }

        function showShareModal(postId) {
            window.postIdToShare = postId;
            showModal(document.getElementById('sharePostConfirmModal'));
        }

        function confirmSharePost() {
            if (!window.postIdToShare) {
                showNotification('No post selected to share.', 'error');
                return;
            }

            const shareButton = document.querySelector(`.share-btn[data-post-id="${window.postIdToShare}"]`);
            if (shareButton) {
                shareButton.disabled = true;
                shareButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sharing...';
            }

            fetch(`/posts/${window.postIdToShare}/share-in-app`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                let data = {};
                try {
                    data = await response.json();
                } catch {
                    const text = await response.text();
                    data.message = text || 'Failed to share post';
                }

                if (!response.ok) {
                    showNotification(data.message || 'Failed to share post', 'error');
                    return;
                }

                if (data.success) {
                    showNotification('Post shared successfully!', 'success');
                    const shareCount = document.getElementById(`share-count-${window.postIdToShare}`);
                    if (shareCount) {
                        shareCount.textContent = data.share_count || (parseInt(shareCount.textContent || '0') + 1);
                    }
                } else {
                    showNotification(data.message || 'Failed to share post', 'error');
                }
            })
            .catch(error => {
                console.error('Error sharing post:', error);
                showNotification('Failed to share post. Please try again.', 'error');
            })
            .finally(() => {
                if (shareButton) {
                    shareButton.disabled = false;
                    shareButton.innerHTML = '<i class="far fa-share-square"></i> Share';
                }
            hideModal(document.getElementById('sharePostConfirmModal'));
                window.postIdToShare = null;
            });
        }

        function showReportModal(postId) {
            console.log('showReportModal called for post:', postId);
            document.getElementById('reportPostForm').action = `/posts/${postId}/report`;
            showModal(document.getElementById('reportPostModal'));
        }

        function showCommentModal(postId) {
            const modal = document.getElementById(`comments-modal-${postId}`);
            if (modal) {
                showModal(modal);
                if (typeof Livewire !== 'undefined') {
                    const lw = Livewire.find(`comments-${postId}`);
                    if (lw && typeof lw.refresh === 'function') {
                        lw.refresh();
                    }
                }
            }
        }

        // --- Like/Reaction Functions ---
        function handleLike(postId) {
            const button = document.querySelector(`.like-btn[data-post-id="${postId}"]`);
            const likeCount = document.getElementById(`like-count-${postId}`);
            const isLiked = button.getAttribute('data-liked') === '1';

            // Optimistic UI update
            updateLikeUI(button, likeCount, !isLiked);

            const url = `/posts/${postId}/reactions`;
            const method = isLiked ? 'DELETE' : 'POST';
            const body = isLiked ? null : JSON.stringify({ reaction_type: 'like' });

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: body
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.total_reactions !== undefined && likeCount) {
                    likeCount.textContent = data.total_reactions;
                }
            })
            .catch(error => {
                // Revert UI on error
                updateLikeUI(button, likeCount, isLiked);
                if (typeof showNotification === 'function') {
                    showNotification('Failed to update reaction. Please try again.', 'error');
                } else {
                    alert('Failed to update reaction. Please try again.');
                }
            });
        }

        function updateLikeUI(button, likeCount, shouldLike) {
            if (!button || !likeCount) return;
            const currentCount = parseInt(likeCount.textContent) || 0;
            if (shouldLike) {
                button.setAttribute('data-liked', '1');
                button.innerHTML = '<i class="fas fa-heart"></i> Liked';
                button.classList.add('reacted');
                likeCount.textContent = currentCount + 1;
            } else {
                button.setAttribute('data-liked', '0');
                button.innerHTML = '<i class="far fa-heart"></i> Like';
                button.classList.remove('reacted');
                likeCount.textContent = Math.max(0, currentCount - 1);
            }
        }

        // Cloudinary Upload Widget for Post Photos
        const cloudName = '{{ config('cloudinary.cloud_name') }}';
        const uploadPreset = '{{ config('cloudinary.upload_preset') }}';

        const postPhotosWidget = cloudinary.createUploadWidget({
            cloudName: cloudName,
            uploadPreset: uploadPreset,
            sources: ['local'],
            multiple: true,
            maxFiles: 5,
            resourceType: 'image',
            clientAllowedFormats: ['image'],
            maxFileSize: 5000000, // 5MB
            styles: {
                palette: {
                    window: "#FFFFFF",
                    windowBorder: "#90A0B3",
                    tabIcon: "#0078FF",
                    menuIcons: "#5A616A",
                    textDark: "#000000",
                    textLight: "#FFFFFF",
                    link: "#0078FF",
                    action: "#FF620C",
                    inactiveTabIcon: "#0E2F5A",
                    error: "#F44235",
                    inProgress: "#0078FF",
                    complete: "#20B832",
                    sourceBg: "#E4EBF1"
                }
            }
        }, (error, result) => {
            if (error) {
                console.error('Upload error:', error);
                alert('Error uploading photo. Please try again.');
                return;
            }

            if (result && result.event === "success") {
                const imageUrl = result.info.secure_url;
                console.log('Uploaded post image:', imageUrl);

                try {
                    // Get the existing photo URLs or initialize an empty array
                    let photoUrls = [];
                    const photoUrlsInput = document.getElementById('photo_urls');

                    if (photoUrlsInput.value) {
                        photoUrls = JSON.parse(photoUrlsInput.value);
                    }

                    // Check if we've reached the maximum number of photos
                    if (photoUrls.length >= 5) {
                        alert('Maximum of 5 photos allowed.');
                        return;
                    }

                    // Add the new photo URL
                    photoUrls.push(imageUrl);

                    // Update the hidden input
                    photoUrlsInput.value = JSON.stringify(photoUrls);

                    // Update the preview
                    const preview = document.getElementById('photo-preview');
                    preview.innerHTML = '';

                    photoUrls.forEach((url, index) => {
                        const container = document.createElement('div');
                        container.className = 'preview-image-container';

                        const img = document.createElement('img');
                        img.src = url;
                        img.className = 'preview-image';

                        const removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'remove-image-btn';
                        removeBtn.textContent = '×';

                        removeBtn.onclick = function() {
                            photoUrls.splice(index, 1);
                            photoUrlsInput.value = JSON.stringify(photoUrls);
                            container.remove();
                        };

                        container.appendChild(img);
                        container.appendChild(removeBtn);
                        preview.appendChild(container);
                    });
                } catch (e) {
                    console.error('Error handling upload result:', e);
                    alert('Error processing uploaded photo. Please try again.');
                }
            }
        });

        // Open post photos upload widget when the button is clicked
        document.addEventListener('DOMContentLoaded', function() {
            const uploadButton = document.getElementById('upload_widget');

            if (uploadButton) {
                uploadButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Upload button clicked');
                    postPhotosWidget.open();
                });
            } else {
                console.error('Upload button not found in DOM');
            }
        });

        // Timer for taken down posts
        function checkTakenDownPosts() {
            const takenDownPosts = document.querySelectorAll('.post-card[data-taken-down]');
            takenDownPosts.forEach(post => {
                const takenDownTime = new Date(post.getAttribute('data-taken-down'));
                const now = new Date();
                const elapsedSeconds = (now - takenDownTime) / 1000; // Convert to seconds
                // console.log('Post ID:', post.getAttribute('data-post-id'));
                // console.log('Taken down time:', takenDownTime);
                // console.log('Current time:', now);
                // console.log('Elapsed seconds:', elapsedSeconds);
                if (elapsedSeconds >= 600) { // Check for 10 minutes
                    post.style.display = 'none';
                }
            });
        }

        // Run the check every second
        setInterval(checkTakenDownPosts, 1000);
        // Initial check
        checkTakenDownPosts();
    </script>
    
    <div class="content">
        <div id="app-notification-container"></div>
        <div class="welcome-panel">
            <div class="welcome-content">
                <h2>Lost or Found a pet? Report it now!</h2>
                <button id="createPostBtn" class="create-post-btn" onclick="showModal(document.getElementById('createPostModal'))">
                    <i class="fas fa-plus"></i>Create Post
                </button>
            </div>
        </div>

        <div class="posts-container">
            @forelse($posts as $post)
                <div class="post-card {{ $post->is_flagged ? 'flagged-post' : '' }}" data-post-id="{{ $post->id }}" {{ $post->is_taken_down ? 'data-taken-down="' . $post->updated_at . '"' : '' }}>
                    @if($post->is_flagged)
                        {{-- Removed flag-notification, only violation banner will be shown if needed --}}
                    @endif

                    @if($post->is_taken_down)
                        <div class="post-header">
                            <div class="post-user-info">
                                <img src="{{ $post->user->profile_picture ?? asset('images/default-profile.png') }}" alt="Profile" class="post-avatar">
                                <div>
                                    <h4 class="post-author">{{ $post->user->username }}</h4>
                                    <span class="post-date">{{ $post->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="violation-banner clean-takedown-banner">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>This post has been taken down for violating community guidelines.</span>
                        </div>
                    @else
                        @if($post->isShared())
                            <div class="post-header">
                                <div class="post-user-info">
                                    <img src="{{ $post->sharedBy->profile_picture ?? asset('images/default-profile.png') }}" alt="Profile" class="post-avatar">
                                    <div>
                                        <h4 class="post-author">
                                            {{ $post->sharedBy->username }}
                                            <span class="post-name">({{ $post->sharedBy->username }})</span>
                                            <span><i class="fas fa-share-alt post-share-icon-margin"></i>shared</span>
                                        </h4>
                                        <span class="post-date">{{ $post->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <div class="post-options">
                                    <button class="post-options-btn" data-post-id="{{ $post->id }}">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="post-options-menu" id="post-options-menu-{{ $post->id }}">
                                        <button class="report-post-btn" data-post-id="{{ $post->id }}" onclick="showReportModal('{{ $post->id }}')">
                                            <i class="fas fa-flag"></i>Report Post
                                        </button>
                                    </div>
                                </div>
                            </div>

                            @php
                                $original = $post->originalPost;
                            @endphp

                            @if($original && !$original->trashed())
                                <div class="original-post-content">
                                    <div class="post-header original-post-header">
                                        <div class="post-user-info">
                                            <img src="{{ $original->user->profile_picture ?? asset('images/default-avatar.jpg') }}" alt="Profile" class="post-avatar">
                                            <div>
                                                <h4 class="post-author">{{ $original->user->name }}</h4>
                                                <span class="post-date">{{ $original->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="post-details">
                                    <span class="post-status {{ $original->status }}">{{ ucfirst($original->status) }}</span>
                                    <span class="post-breed">Breed: {{ $original->breed }}</span>
                                    <span class="post-location">Location: {{ $original->location }}</span>
                                    <span class="post-contact">Contact: {{ $original->contact }}</span>
                                </div>

                                <div class="post-content">
                                    <h3>{{ $original->title }}</h3>
                                    <p class="post-description">{{ $original->description }}</p>
                                </div>

                                @if(count($original->photo_urls ?? []) > 0)
                                    <div class="post-images">
                                        <div class="image-grid {{ count($original->photo_urls) === 1 ? 'single-image' : '' }} 
                                                              {{ count($original->photo_urls) === 2 ? 'two-images' : '' }} 
                                                              {{ count($original->photo_urls) === 3 ? 'three-images' : '' }}">
                                            @foreach($original->photo_urls as $index => $photo_url)
                                                @if($index < 4)
                                                    <div class="grid-item" data-post-id="{{ $original->id }}" data-index="{{ $index }}">
                                                        <img src="{{ $photo_url }}" alt="Pet Photo">
                                                        @if($index === 3 && count($original->photo_urls) > 4)
                                                            <div class="more-indicator">+{{ count($original->photo_urls) - 4 }}</div>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @elseif($original && $original->is_taken_down)
                                <div class="violation-banner">
                                    <i class="fas fa-exclamation-triangle"></i>This post has been taken down for violating community guidelines.
                                </div>
                            @else
                                <div class="deleted-post-message">
                                    <i class="fas fa-trash-alt"></i>
                                    <h3>This post has been deleted</h3>
                                    <p>The original post has been deleted by the author.</p>
                                </div>
                            @endif
                        @else
                            <div class="post-header">
                                <div class="post-user-info">
                                    <img src="{{ $post->user->profile_picture ?? asset('images/default-profile.png') }}" alt="Profile" class="post-avatar">
                                    <div>
                                        <h4 class="post-author">
                                            {{ $post->user->username }}
                                            <span class="post-name">{{ $post->user->name }}</span>
                                        </h4>
                                        <span class="post-date">{{ $post->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                @if(!$post->is_taken_down)
                                    <div class="post-options">
                                        <button class="post-options-btn" data-post-id="{{ $post->id }}">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="post-options-menu" id="post-options-menu-{{ $post->id }}">
                                            <button class="report-post-btn" data-post-id="{{ $post->id }}" onclick="showReportModal('{{ $post->id }}')">
                                                <i class="fas fa-flag"></i>Report Post
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if($post->is_taken_down)
                                <div class="violation-banner" style="margin: 0 15px 15px 15px;">
                                    <i class="fas fa-exclamation-triangle"></i>This post has been taken down for violating community guidelines.
                                </div>
                            @else
                                <div class="post-details" style="padding: 10px 15px;">
                                    <span class="post-status {{ $post->status }}">{{ ucfirst($post->status) }}</span>
                                    <span class="post-breed">Breed: {{ $post->breed }}</span>
                                    <span class="post-location">Location: {{ $post->location }}</span>
                                    <span class="post-contact">Contact: {{ $post->contact }}</span>
                                </div>

                                <div class="post-content">
                                    <h3>{{ $post->title }}</h3>
                                    <p class="post-description">{{ $post->description }}</p>
                                </div>

                                <div class="post-images">
                                    @if(count($post->photo_urls) > 0)
                                        <div class="image-grid {{ count($post->photo_urls) === 1 ? 'single-image' : '' }} 
                                                              {{ count($post->photo_urls) === 2 ? 'two-images' : '' }} 
                                                              {{ count($post->photo_urls) === 3 ? 'three-images' : '' }}">
                                            @foreach($post->photo_urls as $index => $photo_url)
                                                @if($index < 4)
                                                    <div class="grid-item" data-post-id="{{ $post->id }}" data-index="{{ $index }}">
                                                        <img src="{{ $photo_url }}" alt="Pet Photo">
                                                        @if($index === 3 && count($post->photo_urls) > 4)
                                                            <div class="more-indicator">+{{ count($post->photo_urls) - 4 }}</div>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endif

                        @if(!$post->is_taken_down)
                            <div class="post-stats">
                                <span class="reaction-count">
                                    <i class="fas fa-heart"></i>
                                    <span id="like-count-{{ $post->id }}">{{ $post->reaction_counts['like'] ?? 0 }}</span>
                                </span>
                                <span class="comment-count">
                                    <span id="comment-count-{{ $post->id }}">{{ $post->comments_count }}</span>
                                    <span id="comment-text-{{ $post->id }}">{{ $post->comments_count == 1 ? 'Comment' : 'Comments' }}</span>
                                </span>
                                <span class="share-count">
                                    <span id="share-count-{{ $post->id }}">{{ $post->share_count }}</span>
                                    {{ $post->share_count == 1 ? 'share' : 'shares' }}
                                </span>
                            </div>

                            <div class="post-actions">
                                <button class="post-action-btn like-btn {{ $post->current_user_reaction === 'like' ? 'reacted' : '' }}"
                                        data-post-id="{{ $post->id }}"
                                        data-liked="{{ $post->current_user_reaction === 'like' ? '1' : '0' }}"
                                        onclick="handleLike('{{ $post->id }}')">
                                    <i class="fas fa-heart"></i>
                                    <span>{{ $post->current_user_reaction === 'like' ? 'Liked' : 'Like' }}</span>
                                </button>
                                <button class="post-action-btn comment-btn" data-post-id="{{ $post->id }}" onclick="showCommentModal('{{ $post->id }}')">
                                    <i class="far fa-comment"></i>Comment
                                </button>
                                <button class="post-action-btn share-btn" data-post-id="{{ $post->id }}" onclick="showShareModal('{{ $post->id }}')">
                                    <i class="far fa-share-square"></i>Share
                                </button>
                            </div>
                        @endif

                        <div class="modal comments-modal" id="comments-modal-{{ $post->id }}">
                            <div class="modal-content comments-modal-content">
                                <span class="close-modal" onclick="hideModal(document.getElementById('comments-modal-{{ $post->id }}'))">&times;</span>
                                <livewire:post-comments :post="$post" :wire:key="'comments-'.$post->id"/>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="no-results-message no-results-message-styling">
                    <i class="fas fa-search"></i>No results found
                    <div class="no-results-image-container">
                        <img src="/images/no-results-sticker.png" alt="No results sticker" class="no-results-image-styling">
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Report Post Modal --}}
    <div id="reportPostModal" class="modal">
        <div class="report-modal-content">
            <span class="close-modal" id="closeReportModal" onclick="hideModal(document.getElementById('reportPostModal'))">&times;</span>
            <h2>Report Post</h2>
            <p>Is this post inappropriate or violating our community guidelines?</p>
            <form id="reportPostForm" action="" method="POST">
                @csrf
                <input type="hidden" id="report_post_id" name="post_id">
                <div class="form-group">
                    <label for="reason">Reason for reporting:</label>
                    <textarea id="reason" name="reason" required placeholder="Please explain why you're reporting this post..."></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="cancel-btn" id="cancelReportBtn" onclick="hideModal(document.getElementById('reportPostModal'))">Cancel</button>
                    <button type="submit" class="submit-btn" id="submitReportBtn">Submit Report</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Create Post Modal --}}
    <div id="createPostModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" id="closeModal" onclick="hideModal(document.getElementById('createPostModal'))">&times;</span>
            <h2>Create a New Post</h2>
            <form id="createPostForm" action="{{ route('posts.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="title">Post Title</label>
                    <input type="text" id="title" name="title" required class="uppercase-input" oninput="this.value = this.value.toUpperCase();">
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="found">Found</option>
                        <option value="not_found" selected>Not Found</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="breed">Pet Breed</label>
                    <input type="text" id="breed" name="breed" required>
                </div>
                <div class="form-group">
                    <label for="location">Last Seen Location</label>
                    <input type="text" id="location" name="location" required>
                </div>
                <div class="form-group">
                    <label for="contact">Contact Information</label>
                    <input type="text" id="contact" name="contact" required>
                </div>
                <div class="form-group">
                    <label for="description">Post Description</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label>Pet Photos</label>
                    <div class="file-input-container">
                        <button type="button" class="file-input-button" id="upload_widget">
                            <i class="fas fa-camera"></i>Upload Photos
                        </button>
                    </div>
                    <div id="photo-preview" class="photo-preview"></div>
                    <input type="hidden" id="photo_urls" name="photo_urls">
                </div>
                <div class="form-actions">
                    <button type="button" class="cancel-btn" id="cancelBtn" onclick="hideModal(document.getElementById('createPostModal'))">Cancel</button>
                    <button type="button" class="submit-btn" id="submitCreatePostBtn" onclick="validateAndShowConfirmModal()">Create Post</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Create Post Confirm Modal --}}
    <div id="createPostConfirmModal" class="modal">
        <div class="modal-content modal-content-small">
            <h3>Confirm Post Creation</h3>
            <p>Are you sure you want to create this post?</p>
            <div class="form-actions">
                <button type="button" class="cancel-btn" id="cancelCreatePostBtn" onclick="hideModal(document.getElementById('createPostConfirmModal'))">Cancel</button>
                <button type="button" class="submit-btn" id="confirmCreatePostBtn" onclick="submitCreatePostForm()">Create Post</button>
            </div>
        </div>
    </div>

    {{-- Share Post Confirm Modal --}}
    <div id="sharePostConfirmModal" class="modal">
        <div class="modal-content modal-content-small">
            <h3>Confirm Share</h3>
            <p>Do you want to share this post to your feed?</p>
            <div class="form-actions">
                <button type="button" class="cancel-btn" id="cancelSharePostBtn" onclick="hideModal(document.getElementById('sharePostConfirmModal'))">Cancel</button>
                <button type="button" class="submit-btn" id="confirmSharePostBtn" onclick="confirmSharePost()">Share</button>
            </div>
        </div>
    </div>

    {{-- Lightbox Modal --}}
    <div class="lightbox-modal" id="lightbox-modal">
        <span class="lightbox-close">&times;</span>
        <div class="lightbox-content">
            <div class="lightbox-prev"><i class="fas fa-chevron-left"></i></div>
            <img class="lightbox-image" src="" alt="Full size image">
            <div class="lightbox-next"><i class="fas fa-chevron-right"></i></div>
        </div>
        <div class="lightbox-counter"></div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const lightboxModal = document.getElementById('lightbox-modal');
        const lightboxImage = document.querySelector('.lightbox-image');
        const lightboxClose = document.querySelector('.lightbox-close');
        const lightboxPrev = document.querySelector('.lightbox-prev');
        const lightboxNext = document.querySelector('.lightbox-next');
        const lightboxCounter = document.querySelector('.lightbox-counter');
        let currentImageIndex = 0;
        let currentPostImages = [];

        // Open lightbox when clicking on an image
        document.querySelectorAll('.grid-item').forEach(item => {
            item.addEventListener('click', function () {
                const postId = this.getAttribute('data-post-id');
                const index = parseInt(this.getAttribute('data-index'));
                // Get all images for this post
                const postImages = Array.from(document.querySelectorAll(`.grid-item[data-post-id="${postId}"] img`)).map(item => item.src);
                // If there are more than 4 images, we need to fetch all image URLs
                if (document.querySelector(`.grid-item[data-post-id="${postId}"] .more-indicator`)) {
                    // This would ideally be an AJAX call to get all image URLs
                    // For now, we'll use what we have
                    fetch(`/posts/${postId}/photos`).then(response => response.json()).then(data => {
                        if (data.success) {
                            currentPostImages = data.photo_urls;
                            openLightbox(index);
                        }
                    }).catch(() => {
                        // Fallback to the images we have
                        currentPostImages = postImages;
                        openLightbox(index);
                });
            } else {
                    currentPostImages = postImages;
                    openLightbox(index);
                }
            });
        });

        function openLightbox(index) {
                currentImageIndex = index;
                updateLightboxImage();
                lightboxModal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }

            function updateLightboxImage() {
                lightboxImage.src = currentPostImages[currentImageIndex];
                lightboxCounter.textContent = `${currentImageIndex + 1} / ${currentPostImages.length}`;
                lightboxPrev.style.display = currentPostImages.length > 1 ? 'flex' : 'none';
                lightboxNext.style.display = currentPostImages.length > 1 ? 'flex' : 'none';
            }

        // Close lightbox
        lightboxClose.addEventListener('click', function () {
                    lightboxModal.style.display = 'none';
                    document.body.style.overflow = '';
        });

        // Navigate to previous image
        lightboxPrev.addEventListener('click', function () {
                    currentImageIndex = (currentImageIndex - 1 + currentPostImages.length) % currentPostImages.length;
                    updateLightboxImage();
        });

        // Navigate to next image
        lightboxNext.addEventListener('click', function () {
            currentImageIndex = (currentImageIndex + 1) % currentPostImages.length;
            updateLightboxImage();
        });

        // Close lightbox when clicking outside the image
        lightboxModal.addEventListener('click', function (event) {
            if (event.target === lightboxModal) {
                lightboxModal.style.display = 'none';
                document.body.style.overflow = '';
            }
        });

        // Keyboard navigation
        document.addEventListener('keydown', function (event) {
            if (lightboxModal.style.display === 'block') {
                if (event.key === 'Escape') {
                    lightboxModal.style.display = 'none';
                    document.body.style.overflow = '';
                } else if (event.key === 'ArrowLeft') {
                    currentImageIndex = (currentImageIndex - 1 + currentPostImages.length) % currentPostImages.length;
                    updateLightboxImage();
                } else if (event.key === 'ArrowRight') {
                    currentImageIndex = (currentImageIndex + 1) % currentPostImages.length;
                    updateLightboxImage();
                }
            }
        });

        // Toggle post options menu (three dots)
        document.querySelectorAll('.post-options-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const postId = this.getAttribute('data-post-id');
                const menu = document.getElementById(`post-options-menu-${postId}`);
                // Hide all other menus
                document.querySelectorAll('.post-options-menu').forEach(m => {
                    if (m !== menu) m.classList.remove('show');
                });
                // Toggle this menu
                if (menu) menu.classList.toggle('show');
            });
        });

        // Hide the menu if clicking outside
        document.addEventListener('click', function() {
            document.querySelectorAll('.post-options-menu').forEach(menu => {
                menu.classList.remove('show');
            });
        });

        // Intercept report form submission
        const reportForm = document.getElementById('reportPostForm');
        if (reportForm) {
            reportForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(reportForm);
                const action = reportForm.action;
                fetch(action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Report submitted successfully!', 'success');
                    } else {
                        showNotification(data.message || 'Failed to submit report.', 'error');
                    }
                    hideModal(document.getElementById('reportPostModal'));
                    reportForm.reset();
                })
                .catch(() => {
                    showNotification('Failed to submit report. Please try again.', 'error');
                });
            });
        }
    });

    // Livewire event listener for comment count updates (replaced with window event listener)
    window.addEventListener('commentCountUpdated', event => {
        const postId = event.detail ? event.detail.postId : undefined;
        const count = event.detail ? event.detail.count : undefined;
        const commentCountElement = document.getElementById(`comment-count-${postId}`);
        const commentTextElement = document.getElementById(`comment-text-${postId}`);
        if (commentCountElement && commentTextElement) {
            commentCountElement.textContent = count;
            commentTextElement.textContent = count == 1 ? 'Comment' : 'Comments';
        }
    });
    </script>
@endsection