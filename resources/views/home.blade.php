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
                modal.style.display = 'flex'; // Always use flex for all modals
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
            // Your validation logic here, then show confirm modal
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
            // Your share logic here
            hideModal(document.getElementById('sharePostConfirmModal'));
        }
        function showReportModal(postId) {
            document.getElementById('reportPostForm').action = `/posts/${postId}/report`;
            showModal(document.getElementById('reportPostModal'));
        }
        function showCommentModal(postId) {
            const modal = document.getElementById(`comments-modal-${postId}`);
            if (modal) {
                showModal(modal);
                // Only refresh if the Livewire component exists and has a refresh method
                if (typeof Livewire !== 'undefined') {
                    const lw = Livewire.find(`comments-${postId}`);
                    if (lw && typeof lw.refresh === 'function') {
                        lw.refresh();
                    }
                }
            }
        }
    </script>
    
    <div class="content">
        <div id="app-notification-container"></div>
        
        <div class="welcome-panel">
            <div class="welcome-content">
                <h2>Lost or Found a pet? Report it now!</h2>
                <button id="createPostBtn" class="create-post-btn" onclick="showModal(document.getElementById('createPostModal'))">
                    <i class="fas fa-plus"></i> Create Post
                </button>
            </div>
        </div>

        <div class="posts-container">
            @forelse($posts as $post)
                <div class="post-card {{ $post->is_flagged ? 'flagged-post' : '' }}" data-post-id="{{ $post->id }}">
                    @if($post->is_flagged)
                        {{-- Removed flag-notification, only violation banner will be shown if needed --}}
                    @endif

                    @if($post->isShared())
                        <div class="post-header">
                            <div class="post-user-info">
                                <img src="{{ $post->sharedBy->profile_picture ?? asset('images/default-profile.png') }}" 
                                     alt="Profile" 
                                     class="post-avatar">
                                <div>
                                    <h4 class="post-author">
                                        {{ $post->sharedBy->username }}
                                        <span class="post-name" style="font-weight:normal; color:#888;">
                                            ({{ $post->sharedBy->username }})
                                        </span>
                                        <span style="font-weight:normal; color:#888;">
                                            <i class="fas fa-share-alt" style="margin-right: 4px;"></i>shared
                                        </span>
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
                                        <i class="fas fa-flag"></i> Report Post
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
                                        <img src="{{ $original->user->profile_picture ?? asset('images/default-avatar.jpg') }}" 
                                             alt="Profile" 
                                             class="post-avatar">
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
                                <i class="fas fa-exclamation-triangle"></i>
                                This post has been taken down for violating community guidelines.
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
                                <img src="{{ $post->user->profile_picture ?? asset('images/default-profile.png') }}" 
                                     alt="Profile" 
                                     class="post-avatar">
                                <div>
                                    <h4 class="post-author">
                                        {{ $post->user->username }}
                                        <span class="post-name" style="font-weight:normal; color:#888;">
                                            {{ $post->user->name }}
                                        </span>
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
                                            <i class="fas fa-flag"></i> Report Post
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if($post->is_taken_down)
                            <div class="violation-banner" style="margin: 0 15px 15px 15px;">
                                <i class="fas fa-exclamation-triangle"></i>
                                This post has been taken down for violating community guidelines.
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
                                <i class="far fa-comment"></i> Comment
                            </button>
                            <button class="post-action-btn share-btn" data-post-id="{{ $post->id }}" onclick="showShareModal('{{ $post->id }}')">
                                <i class="far fa-share-square"></i> Share
                            </button>
                        </div>
                    @endif

                    <div class="modal comments-modal" id="comments-modal-{{ $post->id }}">
                        <div class="modal-content comments-modal-content">
                            <span class="close-modal" onclick="hideModal(document.getElementById('comments-modal-{{ $post->id }}'))">&times;</span>
                            <livewire:post-comments :post="$post" :wire:key="'comments-'.$post->id"/>
                        </div>
                    </div>
                </div>
            @empty
                <div class="no-results-message" style="text-align:center; color:#888; font-size:1.2rem; margin:40px 0;">
                    <i class="fas fa-search"></i> No results found
                    <div style="margin-top:24px; display:flex; justify-content:center;">
                        <img src="/images/no-results-sticker.png" alt="No results sticker" style="max-width:120px; opacity:0.85;">
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
                    <input type="text" id="title" name="title" required style="text-transform: uppercase;" 
                           oninput="this.value = this.value.toUpperCase();">
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
                            <i class="fas fa-camera"></i> Upload Photos
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
        <div class="modal-content" style="max-width: 400px;">
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
        <div class="modal-content" style="max-width: 400px;">
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
        // Basic test to verify JavaScript is running
        console.log('Initial script test');
        
        // Test jQuery
        $(document).ready(function() {
            console.log('jQuery is loaded');
            
            // Test button click
            $('#testButton').on('click', function() {
                console.log('Test button clicked');
                alert('Test button clicked');
            });
        });

        console.log('Script starting...');

        document.addEventListener('DOMContentLoaded', function () {
            console.log('DOM fully loaded');
            
            // Add debugging logs
            console.log('Create Post Button:', document.getElementById('createPostBtn'));
            console.log('Create Post Modal:', document.getElementById('createPostModal'));
            console.log('Share Post Modal:', document.getElementById('sharePostConfirmModal'));
            console.log('Report Post Modal:', document.getElementById('reportPostModal'));
            
            // Add a global click handler to debug all clicks
            document.addEventListener('click', function(e) {
                console.log('Global click detected:', e.target);
            });

            // Modal related elements (declare ONCE at the top)
            const createPostBtn = document.getElementById('createPostBtn');
            const createPostModal = document.getElementById('createPostModal');
            const closeModal = document.getElementById('closeModal');
            const cancelBtn = document.getElementById('cancelBtn');
            const createPostForm = document.getElementById('createPostForm');
            const submitCreatePostBtn = document.getElementById('submitCreatePostBtn');
            const createPostConfirmModal = document.getElementById('createPostConfirmModal');
            const cancelCreatePostBtn = document.getElementById('cancelCreatePostBtn');
            const confirmCreatePostBtn = document.getElementById('confirmCreatePostBtn');
            const sharePostConfirmModal = document.getElementById('sharePostConfirmModal');
            const cancelSharePostBtn = document.getElementById('cancelSharePostBtn');
            const confirmSharePostBtn = document.getElementById('confirmSharePostBtn');
            const reportPostModal = document.getElementById('reportPostModal');
            const closeReportModal = document.getElementById('closeReportModal');
            const cancelReportBtn = document.getElementById('cancelReportBtn');
            const reportPostForm = document.getElementById('reportPostForm');
            let postIdToShare = null;
            let postIdToReport = null;

            // Function to show modal
            function showModal(modal) {
                if (modal) {
                    console.log('Showing modal:', modal.id);
                    modal.style.display = 'flex'; // Always use flex for all modals
                    modal.style.opacity = '1';
                    modal.style.visibility = 'visible';
                    document.body.style.overflow = 'hidden';
                    if (typeof modal.focus === 'function') modal.focus();
                } else {
                    console.error('Modal not found when trying to show it');
                }
            }

            // Function to hide modal
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

            // Create Post Modal
            if (createPostBtn) {
                createPostBtn.addEventListener('click', function(e) {
                    console.log('Create Post button clicked');
                    e.preventDefault();
                    showModal(createPostModal);
                });
            } else {
                console.error('Create Post button not found');
            }

            // Initialize status if element exists
            const statusElement = document.getElementById('status');
            if (statusElement) {
                statusElement.value = 'not_found';
            }

            // Event listener for comment count updates
            window.addEventListener('commentCountUpdated', function(event) {
                const postId = event.detail?.postId;
                const count = event.detail?.count;
                
                if (postId && count !== undefined) {
                    const commentCountElement = document.getElementById(`comment-count-${postId}`);
                    const commentTextElement = document.getElementById(`comment-text-${postId}`);
                    
                    if (commentCountElement && commentTextElement) {
                        commentCountElement.textContent = count;
                        commentTextElement.textContent = count === 1 ? 'Comment' : 'Comments';
                    }
                }
            });

            window.addEventListener('shareCountUpdated', event => {
                const postId = event.detail ? event.detail.postId : undefined;
                const count = event.detail ? event.detail.count : undefined;

                const shareCountElement = document.getElementById(`share-count-${postId}`);

                if (shareCountElement) {
                    shareCountElement.textContent = count;
                }
            });

            // Handle comment button click
            document.addEventListener('click', function(e) {
                if (e.target.closest('.comment-btn')) {
                    const button = e.target.closest('.comment-btn');
                    const postId = button.getAttribute('data-post-id');
                    showCommentModal(postId);
                }
            });

            // Close comment modals
            document.querySelectorAll('.comments-modal .close-modal').forEach(closeBtn => {
                closeBtn.addEventListener('click', function() {
                    const modal = this.closest('.comments-modal');
                    if (modal) {
                        hideModal(modal);
                    }
                });
            });

            // Close comment modals when clicking outside
            document.querySelectorAll('.comments-modal').forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        hideModal(this);
                    }
                });
            });

            window.addEventListener('click', function (event) {
                if (event.target === createPostModal) {
                    hideModal(createPostModal);
                }

                if (event.target === createPostConfirmModal) {
                    hideModal(createPostConfirmModal);
                }
            });

            submitCreatePostBtn.addEventListener('click', function (e) {
                e.preventDefault();

                const title = document.getElementById('title').value;
                const status = document.getElementById('status').value;
                const breed = document.getElementById('breed').value;
                const location = document.getElementById('location').value;
                const contact = document.getElementById('contact').value;
                const description = document.getElementById('description').value;
                const photoUrls = document.getElementById('photo_urls').value;

                if (!title || !status || !breed || !location || !contact || !description) {
                    alert('Please fill in all required fields');
                    return;
                }

                if (!photoUrls || photoUrls === '[]') {
                    alert('Please upload at least one photo');
                    return;
                }

                showModal(createPostConfirmModal);
            });

            cancelCreatePostBtn.addEventListener('click', function () {
                hideModal(createPostConfirmModal);
            });

            confirmCreatePostBtn.addEventListener('click', function () {
                createPostForm.submit();
            });

            // Initialize Cloudinary widget
            const cloudinaryWidget=cloudinary.createUploadWidget( {

                    cloudName: '{{ config('cloudinary.cloud_name') }}',
                    uploadPreset: '{{ config('cloudinary.upload_preset') }}',
                    sources: ['local'],
                    multiple: true,
                    showCompletedButton: true,
                    singleUploadAutoClose: false,
                    maxFiles: 5,
                    resourceType: 'image',
                    clientAllowedFormats: ['image'],
                    maxFileSize: 5000000,
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
                }

                , (error, result)=> {
                    if ( !error && result && result.event==="success") {
                        let urls=[];

                        try {
                            urls=JSON.parse(document.getElementById('photo_urls').value || '[]');
                        }

                        catch (e) {
                            urls=[];
                        }

                        urls.push(result.info.secure_url);

                        document.getElementById('photo_urls').value=JSON.stringify(urls);

                        updatePhotoPreview(urls);
                    }
                }

            );

            document.getElementById('upload_widget').addEventListener('click', function () {
                    cloudinaryWidget.open();
                }

            );

            function updatePhotoPreview(urls) {
                const preview=document.getElementById('photo-preview');
                preview.innerHTML='';

                urls.forEach((url, index)=> {
                        const imgContainer=document.createElement('div');
                        imgContainer.className='preview-image-container';

                        const img=document.createElement('img');
                        img.src=url;
                        img.className='preview-image';

                        const removeBtn=document.createElement('button');
                        removeBtn.innerHTML='×';
                        removeBtn.className='remove-image';
                        removeBtn.onclick=()=> removeImage(index);

                        imgContainer.appendChild(img);
                        imgContainer.appendChild(removeBtn);
                        preview.appendChild(imgContainer);
                    }

                );
            }

            function removeImage(index) {
                let urls=JSON.parse(document.getElementById('photo_urls').value);
                urls.splice(index, 1);
                document.getElementById('photo_urls').value=JSON.stringify(urls);
                updatePhotoPreview(urls);
            }

            const lightboxModal=document.getElementById('lightbox-modal');
            const lightboxImage=document.querySelector('.lightbox-image');
            const lightboxClose=document.querySelector('.lightbox-close');
            const lightboxPrev=document.querySelector('.lightbox-prev');
            const lightboxNext=document.querySelector('.lightbox-next');
            const lightboxCounter=document.querySelector('.lightbox-counter');

            let currentPostId=null;
            let currentImageIndex=0;
            let currentPostImages=[];

            // Move openLightbox and updateLightboxImage here so they have access to the above variables
            function openLightbox(postId, index) {
                currentPostId = postId;
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

            // Handle image lightbox
            document.addEventListener('click', function(e) {
                    const gridItem=e.target.closest('.grid-item');

                    if (gridItem) {
                        const postId=gridItem.getAttribute('data-post-id');
                        const index=parseInt(gridItem.getAttribute('data-index'));

                        const postImages=Array.from(document.querySelectorAll(`.grid-item[data-post-id="${postId}"]`)) .map(item=> item.querySelector('img').src);

                        if (document.querySelector(`.grid-item[data-post-id="${postId}"] .more-indicator`)) {
                            fetch(`/posts/$ {
                                    postId
                                }

                                /photos`) .then(response=> response.json()) .then(data=> {
                                    if (data.success) {
                                        currentPostImages=data.photo_urls;
                                        openLightbox(postId, index);
                                    }
                                }

                            ) .catch(()=> {
                                    currentPostImages=postImages;
                                    openLightbox(postId, index);
                                }

                            );
                        }

                        else {
                            currentPostImages=postImages;
                            openLightbox(postId, index);
                        }
                    }

                });

            // Lightbox navigation and close logic using event delegation
            lightboxModal.addEventListener('click', function(event) {
                // Close lightbox if clicking the close button or outside the modal content
                if (event.target === lightboxModal || event.target.classList.contains('lightbox-close')) {
                    lightboxModal.style.display = 'none';
                    document.body.style.overflow = '';
                    return;
                }
                // Next image if clicking .lightbox-next or its child
                if (event.target.closest('.lightbox-next')) {
                    currentImageIndex = (currentImageIndex + 1) % currentPostImages.length;
                    updateLightboxImage();
                    return;
                }
                // Previous image if clicking .lightbox-prev or its child
                if (event.target.closest('.lightbox-prev')) {
                    currentImageIndex = (currentImageIndex - 1 + currentPostImages.length) % currentPostImages.length;
                    updateLightboxImage();
                    return;
                }
            });

            // Toggle post options menu (ellipsis)
            document.addEventListener('click', function(e) {
                // If the ellipsis button is clicked
                if (e.target.closest('.post-options-btn')) {
                    e.preventDefault();
                    e.stopPropagation();
                    const btn = e.target.closest('.post-options-btn');
                    const postId = btn.getAttribute('data-post-id');
                    const menu = document.getElementById(`post-options-menu-${postId}`);
                    // Hide all other menus first
                    document.querySelectorAll('.post-options-menu').forEach(m => m.classList.remove('show'));
                    // Toggle this menu
                    if (menu) menu.classList.toggle('show');
                } else {
                    // Hide all menus if clicking outside
                    document.querySelectorAll('.post-options-menu').forEach(m => m.classList.remove('show'));
                }
            });
        });

        $.ajaxSetup( {
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }

        );

        // Like functionality
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
                showAppNotification('Failed to update reaction. Please try again.', 'error');
            });
        }

        // Helper function to update like UI
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

        // Event delegation for like buttons
        document.addEventListener('click', function(e) {
            const likeButton = e.target.closest('.like-btn');
            if (likeButton) {
                const postId = likeButton.getAttribute('data-post-id');
                if (postId) {
                    handleLike(postId);
                }
            }
        });

        // Share Post Modal Elements
        const sharePostConfirmModal=document.getElementById('sharePostConfirmModal');
        const cancelSharePostBtn=document.getElementById('cancelSharePostBtn');
        const confirmSharePostBtn=document.getElementById('confirmSharePostBtn');
        let postIdToShare=null;

        // Share button click handler
        document.addEventListener('click', function(e) {
            if (e.target.closest('.share-btn')) {
                console.log('Share button clicked');
                e.preventDefault();
                e.stopPropagation();

                const button = e.target.closest('.share-btn');
                postIdToShare = button.getAttribute('data-post-id');
                console.log('Post ID to share:', postIdToShare);

                showModal(sharePostConfirmModal);
                document.body.style.overflow = 'hidden';
            }
        });

        // Share modal close handlers
        cancelSharePostBtn.addEventListener('click', function() {
                hideModal(sharePostConfirmModal);
                postIdToShare=null;
            }

        );

        confirmSharePostBtn.addEventListener('click', function() {
                if (postIdToShare) {
                    sharePost(postIdToShare);
                }
            }

        );

        // Share post function
        function sharePost(postId) {
            if ( !postId) {
                showAppNotification('Invalid post ID', 'error');
                return;
            }

            const shareButton=document.querySelector(`.share-btn[data-post-id="${postId}"]`);

            if (shareButton) {
                shareButton.disabled=true;
                shareButton.innerHTML='<i class="fas fa-spinner fa-spin"></i> Sharing...';
            }

            fetch(`/posts/${postId}/share-in-app`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({})
            })
            .then(async response => {
                let data = {};
                try {
                    data = await response.json();
                } catch {
                    // If not JSON, try to get text
                    const text = await response.text();
                    data.message = text || 'Failed to share post';
                }
                if (!response.ok) {
                    showAppNotification(data.message || 'Failed to share post', 'error');
                    return;
                }
                if (!data) return;
                if (data.success) {
                    showAppNotification('Post shared successfully!');
                    const shareCount = document.getElementById(`share-count-${postId}`);
                    if (shareCount) {
                        shareCount.textContent = data.share_count || (parseInt(shareCount.textContent || '0') + 1);
                    }
                } else {
                    showAppNotification(data.message || 'Failed to share post', 'error');
                }
            })
            .catch(error => {
                console.error('Error sharing post:', error);
                showAppNotification(error.message || 'An error occurred while sharing the post', 'error');
            })
            .finally(() => {
                hideModal(document.getElementById('sharePostConfirmModal'));
                postIdToShare = null;
                if (shareButton) {
                    shareButton.disabled = false;
                    shareButton.innerHTML = '<i class="far fa-share-square"></i> Share';
                }
            });
        }

        // Close modals when clicking outside
        window.addEventListener('click', function(e) {

                // Close modals when clicking on the overlay
                if (e.target.classList.contains('modal')) {
                    e.target.style.display='none';
                    document.body.style.overflow='auto';
                }
            }

        );

        // Close buttons for modals
        document.querySelectorAll('.close-modal').forEach(closeBtn=> {
                closeBtn.addEventListener('click', function() {
                        const modal=this.closest('.modal');

                        if (modal) {
                            hideModal(modal);
                        }
                    }

                );
            }

        );

        // Report Post Modal Elements
        const reportPostModal=document.getElementById('reportPostModal');
        const closeReportModal=document.getElementById('closeReportModal');
        const cancelReportBtn=document.getElementById('cancelReportBtn');
        const reportPostForm=document.getElementById('reportPostForm');
        let postIdToReport=null;

        // Report button click handler
        document.addEventListener('click', function(e) {
            if (e.target.closest('.report-post-btn')) {
                console.log('Report button clicked');
                e.preventDefault();
                e.stopPropagation();

                const button = e.target.closest('.report-post-btn');
                postIdToReport = button.getAttribute('data-post-id');
                console.log('Post ID to report:', postIdToReport);

                reportPostForm.action = `/posts/${postIdToReport}/report`;
                showModal(reportPostModal);
                document.body.style.overflow = 'hidden';
            }
        });

        // Report modal close handlers
        closeReportModal.addEventListener('click', function() {
                hideModal(reportPostModal);
                document.body.style.overflow='auto';
                postIdToReport=null;
                reportPostForm.reset();
            }

        );

        cancelReportBtn.addEventListener('click', function() {
                hideModal(reportPostModal);
                document.body.style.overflow='auto';
                postIdToReport=null;
                reportPostForm.reset();
            }

        );

        // Report form submission
        reportPostForm.addEventListener('submit', function(e) {
                e.preventDefault();

                if ( !postIdToReport) {
                    showAppNotification('Invalid post ID', 'error');
                    return;
                }

                const reason=document.getElementById('reason').value;

                if ( !reason.trim()) {
                    showAppNotification('Please provide a reason for reporting', 'error');
                    return;
                }

                fetch(this.action, {

                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }

                        ,
                        body: JSON.stringify( {
                                reason: reason
                            }

                        )
                    }

                ) .then(response=> response.json()) .then(data=> {
                        if (data.success) {
                            showAppNotification('Post reported successfully. Thank you for helping keep our community safe.', 'success');
                            hideModal(reportPostModal);
                            document.body.style.overflow='auto';
                            postIdToReport=null;
                            reportPostForm.reset();
                        }

                        else {
                            showAppNotification(data.message || 'Failed to report post. Please try again.', 'error');
                        }
                    }

                ) .catch(error=> {
                        console.error('Error reporting post:', error);
                        showAppNotification('An error occurred while reporting the post', 'error');
                    }

                );
            }

        );

        function closeAllModals() {
            const modals=document.querySelectorAll('.modal');

            modals.forEach(modal=> {
                    hideModal(modal);
                }

            );
            document.body.style.overflow='auto';
        }

        // Update modal close handlers
        document.querySelectorAll('.close-modal').forEach(closeBtn=> {
                closeBtn.addEventListener('click', function() {
                        const modal=this.closest('.modal');

                        if (modal) {
                            hideModal(modal);
                        }
                    }

                );
            }

        );

        // Update window click handler
        window.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal')) {
                    closeAllModals();
                }
            }

        );

        // Show notification function
        function showAppNotification(message, type='success') {
            const container=document.getElementById('app-notification-container');
            if ( !container) return;

            const notification=document.createElement('div');

            notification.className=`alert alert-${type} alert-dismissible fade show`;
            notification.setAttribute('role', 'alert');

            const messageSpan=document.createElement('span');
            messageSpan.textContent=message;
            notification.appendChild(messageSpan);

            const closeButton=document.createElement('button');
            closeButton.setAttribute('type', 'button');
            closeButton.className='close-alert-btn';
            closeButton.innerHTML='&times;';
            closeButton.setAttribute('aria-label', 'Close');

            closeButton.onclick = () => {
                notification.style.animation = 'slideOut 0.3s forwards';
                setTimeout(() => notification.remove(), 300);
            };

            notification.appendChild(closeButton);
            container.appendChild(notification);

            setTimeout(() => {
                if (notification.parentNode === container) {
                    notification.style.animation = 'slideOut 0.3s forwards';
                    setTimeout(() => notification.remove(), 300);
                }
            }, 5000);
        }

        // Share functionality
        function showShareModal(postId) {
            window.postIdToShare = postId;
            showModal(document.getElementById('sharePostConfirmModal'));
        }

        function confirmSharePost() {
            if (!window.postIdToShare) {
                showAppNotification('Invalid post ID', 'error');
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
                    // If not JSON, try to get text
                    const text = await response.text();
                    data.message = text || 'Failed to share post';
                }
                if (!response.ok) {
                    showAppNotification(data.message || 'Failed to share post', 'error');
                    return;
                }
                if (!data) return;
                if (data.success) {
                    showAppNotification('Post shared successfully!');
                    const shareCount = document.getElementById(`share-count-${window.postIdToShare}`);
                    if (shareCount) {
                        shareCount.textContent = data.share_count || (parseInt(shareCount.textContent || '0') + 1);
                    }
                } else {
                    showAppNotification(data.message || 'Failed to share post', 'error');
                }
            })
            .catch(error => {
                console.error('Error sharing post:', error);
                showAppNotification(error.message || 'An error occurred while sharing the post', 'error');
            })
            .finally(() => {
                hideModal(document.getElementById('sharePostConfirmModal'));
                window.postIdToShare = null;
                if (shareButton) {
                    shareButton.disabled = false;
                    shareButton.innerHTML = '<i class="far fa-share-square"></i> Share';
                }
            });
        }

        // Report functionality
        function showReportModal(postId) {
            const reportForm = document.getElementById('reportPostForm');
            if (reportForm) {
                reportForm.action = `/posts/${postId}/report`;
                showModal(document.getElementById('reportPostModal'));
            }
        }
    </script>

    <style>
        .comments-modal .modal-content {
            max-width: 600px;
            max-height: fit-content;
            margin: 5vh auto;
            padding: 20px;
            background: white;
            border-radius: 12px;
            position: relative;
        }

        .comments-container {
            display: flex;
            flex-direction: column;
            height: 100%;
            max-height: calc(80vh - 40px);
        }

        .comments-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            margin-bottom: 15px;
        }

        .comments-header h3 {
            margin: 0;
            font-size: 1.2em;
            color: #1c1e21;
        }

        .comments-sort {
            font-size: 0.9em;
            color: #65676b;
            cursor: pointer;
        }

        .comments-list {
            flex: 1;
            overflow-y: auto;
            padding: 10px 0;
            margin-bottom: 15px;
        }

        .comment {
            display: flex;
            gap: 10px;
            padding: 8px 0;
        }

        .comment-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }

        .comment-content {
            flex: 1;
            background: #f0f2f5;
            border-radius: 18px;
            padding: 8px 12px;
        }

        .comment-user {
            font-weight: 600;
            color: #050505;
            font-size: 0.9em;
        }

        .comment-body {
            margin: 4px 0 0;
            color: #050505;
            font-size: 0.9em;
        }

        .comment-actions {
            display: flex;
            gap: 12px;
            margin-top: 4px;
            padding-left: 12px;
        }

        .comment-action {
            font-size: 0.75em;
            color: #65676b;
            font-weight: 600;
            cursor: pointer;
        }

        .comment-form {
            display: flex;
            gap: 8px;
            align-items: center;
            padding: 10px 0;
            border-top: 1px solid #eee;
        }

        .comment-input {
            flex: 1;
            border: none;
            background: #f0f2f5;
            border-radius: 20px;
            padding: 8px 12px;
            font-size: 0.9em;
        }

        .comment-submit {
            background: none;
            border: none;
            color: #1877f2;
            cursor: pointer;
            padding: 8px;
        }

        .comment-submit:disabled {
            color: #bec3c9;
            cursor: not-allowed;
        }

        .post-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 15px;
        }

        .post-user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .post-options {
            position: relative;
        }

        .post-options-btn {
            background: none;
            border: none;
            color: #65676b;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 50%;
        }

        .post-options-btn:hover {
            background-color: #f0f2f5;
        }

        .post-options-menu {
            position: absolute;
            right: 0;
            top: 100%;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            min-width: 150px;
            display: none;
            z-index: 10;
        }

        .post-options-menu.show {
            display: block;
        }

        .post-options-menu button {
            display: block;
            width: 100%;
            text-align: left;
            padding: 10px 15px;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 14px;
            color: #1c1e21;
        }

        .post-options-menu button:hover {
            background-color: #f0f2f5;
        }

        .report-post-btn {
            color: #e41e3f !important;
        }

        .flagged-post {
            border: 1px solid #e41e3f;
            position: relative;
        }

        .flag-notification {
            background-color: #ffebee;
            color: #e41e3f;
            padding: 8px 15px;
            font-size: 14px;
            text-align: center;
            border-bottom: 1px solid #e41e3f;
        }

        #reportPostModal textarea {
            width: 100%;
            min-height: 100px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 15px;
            resize: vertical;
        }

        .reaction-count i {
            color: #e41e3f;
        }

        .post-action-btn.like-btn i {
            color: #65676b;
        }

        .post-action-btn.like-btn.reacted i {
            color: #e41e3f;
        }

        .post-action-btn.like-btn:hover i {
            color: #e41e3f;
        }

        .deleted-post-message {
            text-align: center;
            padding: 40px 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin: 15px;
        }

        .deleted-post-message i {
            font-size: 2.5rem;
            color: #6c757d;
            margin-bottom: 15px;
        }

        .deleted-post-message h3 {
            color: #343a40;
            margin-bottom: 10px;
            font-size: 1.2rem;
        }

        .deleted-post-message p {
            color: #6c757d;
            margin: 0;
            font-size: 0.9rem;
        }

        .violation-banner {
            background: #ffeaea;
            color: #d32f2f;
            border: 1px solid #f44336;
            padding: 24px 20px;
            border-radius: 8px;
            margin-top: 10px;
            font-size: 1.1rem;
            text-align: center;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: center;
        }

        .violation-banner i {
            font-size: 1.5rem;
            color: #d32f2f;
        }

    </style>
@endsection