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
    <div class="content">
        <div id="notificationContainer"></div>
        <div id="app-notification-container"></div>
        <div class="welcome-panel">
            <div class="welcome-content">
                <h2>Lost or Found a pet? Report it now !</h2>
                <button id="createPostBtn" class="create-post-btn"
                    onclick="showModal(document.getElementById('createPostModal'))">
                    Create Post
                </button>
            </div>
        </div>
        <div class="posts-container">
            @forelse($posts as $post)
                <div class="post-card {{ $post->is_flagged ? 'flagged-post' : '' }}" data-post-id="{{ $post->id }}" {{ $post->is_taken_down ? 'data-taken-down="' . $post->updated_at . '"' : '' }}>
                    @if($post->is_flagged)
                        {{-- Removed flag-notification, only violation banner will be shown if needed --}}
                    @endif

                    @php
                        $takedownInfo = $post->getTakedownInfo();
                    @endphp

                    @if($takedownInfo)
                        @if($takedownInfo['is_shared_content_taken_down'])
                            {{-- Shared post where original content is taken down --}}
                            <div class="post-header">
                                <div class="post-user-info">
                                    <img src="{{ $takedownInfo['sharer_user']->profile_picture ?? asset('images/default-profile.png') }}"
                                        alt="Profile" class="post-avatar">
                                    <div>
                                        <h4 class="post-author">
                                            {{ $takedownInfo['sharer_user']->username }}
                                            <span class="post-name">({{ $takedownInfo['sharer_user']->name }})</span>
                                            <span><i class="fas fa-share-alt post-share-icon-margin"></i>shared</span>
                                        </h4>
                                        <span class="post-date">{{ $post->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="original-post-content">
                                <div class="post-header original-post-header">
                                    <div class="post-user-info">
                                        <img src="{{ $takedownInfo['display_user']->profile_picture ?? asset('images/default-profile.png') }}"
                                            alt="Profile" class="post-avatar">
                                        <div>
                                            <h4 class="post-author">{{ $takedownInfo['display_user']->username }}</h4>
                                            <span
                                                class="post-date">{{ $takedownInfo['taken_down_post']->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="violation-banner">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    This post has been taken down for violating community guidelines.
                                </div>
                            </div>
                        @else
                            {{-- Regular post that is taken down --}}
                            <div class="post-header">
                                <div class="post-user-info">
                                    <img src="{{ $takedownInfo['display_user']->profile_picture ?? asset('images/default-profile.png') }}"
                                        alt="Profile" class="post-avatar">
                                    <div>
                                        <h4 class="post-author">{{ $takedownInfo['display_user']->username }}</h4>
                                        <span
                                            class="post-date">{{ $takedownInfo['taken_down_post']->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="violation-banner clean-takedown-banner">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>This post has been taken down for violating community guidelines.</span>
                            </div>
                        @endif
                    @else
                        @if($post->isShared())
                            <div class="shared-post-main-container">
                                <div class="post-header">
                                    <div class="post-user-info">
                                        <img src="{{ $post->sharedBy->profile_picture ?? asset('images/default-profile.png') }}"
                                            alt="Profile" class="post-avatar">
                                        <div>
                                            <h4 class="post-author">
                                                {{ $post->sharedBy->username }}
                                                <span class="post-name">({{ $post->sharedBy->name }})</span>
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
                                            <button class="report-post-btn" data-post-id="{{ $post->id }}"
                                                onclick="showReportModal('{{ $post->id }}')">
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
                                                <img src="{{ $original->user->profile_picture ?? asset('images/default-profile.png') }}"
                                                    alt="Profile" class="post-avatar">
                                                <div>
                                                    <h4 class="post-author">{{ $original->user->username }}</h4>
                                                    <span class="post-date">{{ $original->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="post-details">
                                            <span class="post-status {{ $original->status }}">{{ ucfirst($original->status) }}</span>
                                            <span class="post-breed">Breed: {{ $original->breed }}</span>
                                            <span class="post-location">last seen Location: {{ $original->location }}</span>
                                            <span class="post-contact">Contact Number: {{ $original->mobile_number }} | Email:
                                                {{ $original->email }}</span>
                                        </div>
                                        <div class="post-content">
                                            <h3>{{ $original->title }}</h3>
                                            <p class="post-description">{{ $original->description }}</p>
                                        </div>
                                        @if(count($original->photo_urls ?? []) > 0)
                                            <div class="post-images">
                                                <div
                                                    class="image-grid {{ count($original->photo_urls) === 1 ? 'single-image' : '' }}
                                                                                                                                                                                                                                                                                                                                                                                                                            {{ count($original->photo_urls) === 2 ? 'two-images' : '' }}
                                                                                                                                                                                                                                                                                                                                                                                                                            {{ count($original->photo_urls) === 3 ? 'three-images' : '' }}">
                                                    @foreach($original->photo_urls as $index => $photo_url)
                                                        @if($index < 4)
                                                            <div class="grid-item" data-post-id="{{ $original->id }}" data-index="{{ $index }}">
                                                                <img src="{{ $photo_url }}" alt="Pet Photo">
                                                                @if($index === 3 && count($original->photo_urls) > 4)
                                                                    <div class="more-indicator">
                                                                        +{{ count($original->photo_urls) - 4 }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
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
                            </div>
                        @else
                            <div class="post-header">
                                <div class="post-user-info">
                                    <img src="{{ $post->user->profile_picture ?? asset('images/default-profile.png') }}" alt="Profile"
                                        class="post-avatar">
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
                                            <button class="report-post-btn" data-post-id="{{ $post->id }}"
                                                onclick="showReportModal('{{ $post->id }}')">
                                                <i class="fas fa-flag"></i>Report Post
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
                                    <span class="post-location">Last seen Location: {{ $post->location }}</span>
                                    <span class="post-contact">Contact Number: {{ $post->mobile_number }} | Email: {{ $post->email }}</span>
                                </div>
                                <div class="post-content">
                                    <h3>{{ $post->title }}</h3>
                                    <p class="post-description">{{ $post->description }}</p>
                                </div>
                                <div class="post-images">
                                    @if(count($post->photo_urls) > 0)
                                        <div
                                            class="image-grid {{ count($post->photo_urls) === 1 ? 'single-image' : '' }}
                                                                                                                                                                                                                                                                                                                                                                                                                    {{ count($post->photo_urls) === 2 ? 'two-images' : '' }}
                                                                                                                                                                                                                                                                                                                                                                                                                    {{ count($post->photo_urls) === 3 ? 'three-images' : '' }}">
                                            @foreach($post->photo_urls as $index => $photo_url)
                                                @if($index < 4)
                                                    <div class="grid-item" data-post-id="{{ $post->id }}" data-index="{{ $index }}">
                                                        <img src="{{ $photo_url }}" alt="Pet Photo">
                                                        @if($index === 3 && count($post->photo_urls) > 4)
                                                            <div class="more-indicator">
                                                                +{{ count($post->photo_urls) - 4 }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endif

                        @if(!$takedownInfo)
                            <div class="post-stats">
                                <span class="reaction-count">
                                    <i class="fas fa-heart"></i>
                                    <span id="like-count-{{ $post->id }}">{{ $post->reaction_counts['like'] ?? 0 }}</span>
                                </span>
                                <span class="comment-count">
                                    <span id="comment-count-{{ $post->id }}">{{ $post->comments_count }}</span>
                                    <span id="comment-text-{{ $post->id }}">
                                        {{ $post->comments_count == 0 ? 'Comment' : 'Comments' }}
                                    </span>
                                </span>
                                <span class="share-count">
                                    <span id="share-count-{{ $post->id }}">{{ $post->share_count }}</span>
                                    {{ $post->share_count == 0 ? 'share' : 'shares' }}
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
                                <button class="post-action-btn comment-btn" data-post-id="{{ $post->id }}"
                                    onclick="showCommentModal('{{ $post->id }}')">
                                    <i class="far fa-comment"></i>Comment
                                </button>
                                @if($post->user_id !== auth()->id())
                                    <button class="post-action-btn share-btn" data-post-id="{{ $post->id }}"
                                        onclick="showShareModal('{{ $post->id }}')">
                                        <i class="far fa-share-square"></i>Share
                                    </button>
                                @endif
                            </div>
                        @endif

                        <div class="modal comments-modal" id="comments-modal-{{ $post->id }}">
                            <div class="modal-content comments-modal-content">
                                <span class="close-modal"
                                    onclick="hideModal(document.getElementById('comments-modal-{{ $post->id }}'))">
                                    &times;
                                </span>
                                <livewire:post-comments :post="$post" :wire:key="'comments-'.$post->id" />
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
            <span class="close-modal" id="closeReportModal" onclick="hideModal(document.getElementById('reportPostModal'))">
                &times;
            </span>
            <h2>Report Post</h2>
            <p>Please select the reason(s) for reporting this post:</p>
            <form id="reportPostForm" action="" method="POST">
                @csrf
                <input type="hidden" id="report_post_id" name="post_id">
                <input type="hidden" id="selected_reasons" name="reasons">

                <div class="report-categories">
                    <div class="category-section">
                        <h3><i class="fas fa-exclamation-triangle"></i> Content-Related Violations</h3>
                        <div class="violation-options">
                            <label class="violation-option">
                                <input type="checkbox" value="sexual_content" name="violation_reasons[]">
                                <span class="checkmark"></span>
                                Sexual content or nudity
                            </label>
                            <label class="violation-option">
                                <input type="checkbox" value="violence" name="violation_reasons[]">
                                <span class="checkmark"></span>
                                Violence or graphic content
                            </label>
                            <label class="violation-option">
                                <input type="checkbox" value="hate_speech" name="violation_reasons[]">
                                <span class="checkmark"></span>
                                Hate speech or symbols
                            </label>
                            <label class="violation-option">
                                <input type="checkbox" value="harassment" name="violation_reasons[]">
                                <span class="checkmark"></span>
                                Harassment or bullying
                            </label>
                            <label class="violation-option">
                                <input type="checkbox" value="self_harm" name="violation_reasons[]">
                                <span class="checkmark"></span>
                                Self-harm or suicide promotion
                            </label>
                            <label class="violation-option">
                                <input type="checkbox" value="spam" name="violation_reasons[]">
                                <span class="checkmark"></span>
                                Spam or misleading information
                            </label>
                            <label class="violation-option">
                                <input type="checkbox" value="misinformation" name="violation_reasons[]">
                                <span class="checkmark"></span>
                                Misinformation or false claims
                            </label>
                            <label class="violation-option">
                                <input type="checkbox" value="abusive_language" name="violation_reasons[]">
                                <span class="checkmark"></span>
                                Foul or abusive language
                            </label>
                            <label class="violation-option">
                                <input type="checkbox" value="terrorism" name="violation_reasons[]">
                                <span class="checkmark"></span>
                                Terrorism or violent extremism
                            </label>
                            <label class="violation-option">
                                <input type="checkbox" value="illegal_activities" name="violation_reasons[]">
                                <span class="checkmark"></span>
                                Illegal activities (e.g. drug use, weapons)
                            </label>
                        </div>
                    </div>

                    <div class="category-section">
                        <h3><i class="fas fa-user-times"></i> User Behavior Violations</h3>
                        <div class="violation-options">
                            <label class="violation-option">
                                <input type="checkbox" value="impersonation" name="violation_reasons[]">
                                <span class="checkmark"></span>
                                Impersonation or fake account
                            </label>
                            <label class="violation-option">
                                <input type="checkbox" value="stalking" name="violation_reasons[]">
                                <span class="checkmark"></span>
                                Stalking or threatening behavior
                            </label>
                            <label class="violation-option">
                                <input type="checkbox" value="inappropriate_messages" name="violation_reasons[]">
                                <span class="checkmark"></span>
                                Inappropriate direct messages
                            </label>
                            <label class="violation-option">
                                <input type="checkbox" value="unwanted_contact" name="violation_reasons[]">
                                <span class="checkmark"></span>
                                Unwanted contact
                            </label>
                            <label class="violation-option">
                                <input type="checkbox" value="scam" name="violation_reasons[]">
                                <span class="checkmark"></span>
                                Scam or phishing
                            </label>
                        </div>
                    </div>

                    <div class="category-section">
                        <h3><i class="fas fa-shield-alt"></i> Platform/Community Violations</h3>
                        <div class="violation-options">
                            <label class="violation-option">
                                <input type="checkbox" value="community_guidelines" name="violation_reasons[]">
                                <span class="checkmark"></span>
                                Violation of community guidelines
                            </label>
                            <label class="violation-option">
                                <input type="checkbox" value="copyright" name="violation_reasons[]">
                                <span class="checkmark"></span>
                                Copyright or intellectual property infringement
                            </label>
                            <label class="violation-option">
                                <input type="checkbox" value="inappropriate_profile" name="violation_reasons[]">
                                <span class="checkmark"></span>
                                Inappropriate username or profile
                            </label>
                        </div>

                        <div class="other-section">
                            <h4><i class="fas fa-ellipsis-h"></i> Other</h4>
                            <div class="violation-options">
                                <label class="violation-option">
                                    <input type="checkbox" value="other_reason" name="violation_reasons[]">
                                    <span class="checkmark"></span>
                                    Other reason not listed above
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="cancel-btn" id="cancelReportBtn"
                        onclick="hideModal(document.getElementById('reportPostModal'))">
                        Cancel
                    </button>
                    <button type="submit" class="submit-btn" id="submitReportBtn" disabled>Submit Report</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Create Post Modal --}}
    <div id="createPostModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" id="closeModal" onclick="hideModal(document.getElementById('createPostModal'))">
                &times;
            </span>
            <h2>Create a New Post</h2>
            <form id="createPostForm" action="{{ route('posts.store') }}" method="POST">
                @csrf
                <div id="createPostErrors" class="alert alert-danger" style="display:none;"></div>
                <div class="form-group">
                    <label for="title">Post Title</label>
                    <input type="text" id="title" name="title" required class="uppercase-input"
                        oninput="this.value = this.value.toUpperCase();">
                </div>
                <div class="form-group description-group">
                    <label for="description">Post Description</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="found">Found</option>
                        <option value="not_found" selected>Not Found</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="breed">Pet Breed</label>
                        <input type="text" id="breed" name="breed" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Last Seen Location</label>
                        <input type="text" id="location" name="location" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="mobile_number">Contact Number</label>
                    <input type="text" id="mobile_number" name="mobile_number" required pattern="[0-9]+"
                        title="Contact number must be numeric">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
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
                    <button type="button" class="cancel-btn" id="cancelBtn"
                        onclick="hideModal(document.getElementById('createPostModal'))">
                        Cancel
                    </button>
                    <button type="button" class="submit-btn" id="submitCreatePostBtn"
                        onclick="validateAndShowConfirmModal()">
                        Create Post
                    </button>
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
                <button type="button" class="cancel-btn" id="cancelCreatePostBtn"
                    onclick="hideModal(document.getElementById('createPostConfirmModal'))">
                    Cancel
                </button>
                <button type="button" class="submit-btn" id="confirmCreatePostBtn" onclick="submitCreatePostForm()">
                    Create Post
                </button>
            </div>
        </div>
    </div>

    {{-- Share Post Confirm Modal --}}
    <div id="sharePostConfirmModal" class="modal">
        <div class="modal-content modal-content-small">
            <h3>Confirm Share</h3>
            <p>Do you want to share this post to your feed?</p>
            <div class="form-actions">
                <button type="button" class="cancel-btn" id="cancelSharePostBtn"
                    onclick="hideModal(document.getElementById('sharePostConfirmModal'))">
                    Cancel
                </button>
                <button type="button" class="submit-btn" id="confirmSharePostBtn" onclick="confirmSharePost()">
                    Share
                </button>
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
        function showNotification(message, type = 'success') {
            const container = document.getElementById('notificationContainer');
            if (!container) {
                console.error('Notification container not found');
                return;
            }

            const notification = document.createElement('div');
            notification.className = `notification ${type}`;

            const messageSpan = document.createElement('span');
            messageSpan.textContent = message;

            const closeBtn = document.createElement('button');
            closeBtn.className = 'close-btn';
            closeBtn.innerHTML = '&times;';
            closeBtn.onclick = () => {
                notification.classList.add('slide-out');
                setTimeout(() => notification.remove(), 300);
            };

            notification.appendChild(messageSpan);
            notification.appendChild(closeBtn);
            container.appendChild(notification);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.classList.add('slide-out');
                    setTimeout(() => notification.remove(), 300);
                }
            }, 5000);
        }

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
            const form = document.getElementById('createPostForm');
            if (form) {
                // If the form is invalid, trigger browser validation and do not show the confirm modal
                if (!form.checkValidity()) {
                    form.reportValidity(); // This will show the browser's validation UI
                    return;
                }
            }
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

            const body = isLiked ? null : JSON.stringify({
                reaction_type: 'like'
            });

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
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof cloudinary === 'undefined') {
                console.error('Cloudinary is not loaded!');
                return;
            }
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

                            removeBtn.onclick = function () {
                                photoUrls.splice(index, 1);
                                photoUrlsInput.value = JSON.stringify(photoUrls);
                                container.remove();
                            };

                            container.appendChild(img);
                            container.appendChild(removeBtn);
                            preview.appendChild(container);
                        });
                    }

                    catch (e) {
                        console.error('Error handling upload result:', e);
                        alert('Error processing uploaded photo. Please try again.');
                    }
                }
            });

            // Open post photos upload widget when the button is clicked
            const uploadButton = document.getElementById('upload_widget');

            if (uploadButton) {
                uploadButton.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Upload button clicked');
                    postPhotosWidget.open();
                });
            } else {
                console.error('Upload button not found in DOM');
            }
        });

        // --- Create Post Modal AJAX Submission ---
        document.addEventListener('DOMContentLoaded', function () {
            const createPostForm = document.getElementById('createPostForm');
            const createPostErrors = document.getElementById('createPostErrors');
            const confirmCreatePostBtn = document.getElementById('confirmCreatePostBtn');
            const createPostConfirmModal = document.getElementById('createPostConfirmModal');
            const createPostModal = document.getElementById('createPostModal');

            if (createPostForm) {
                createPostForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    if (createPostErrors) {
                        createPostErrors.style.display = 'none';
                        createPostErrors.innerHTML = '';
                    }
                    const formData = new FormData(createPostForm);
                    fetch(createPostForm.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                        .then(async response => {
                            if (response.ok) {
                                // Show success notification before reloading
                                showNotification('Post created successfully!', 'success');
                                // Add a small delay to allow the notification to be seen
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                let data = {};
                                try {
                                    data = await response.json();
                                } catch {
                                    data.message = 'Something went wrong. Please try again.';
                                }
                                if (data.errors && createPostErrors) {
                                    createPostErrors.style.display = 'block';
                                    createPostErrors.innerHTML = Object.values(data.errors)
                                        .map(errArr => `<div>${errArr.join('<br>')}</div>`)
                                        .join('');
                                } else if (data.message && createPostErrors) {
                                    createPostErrors.style.display = 'block';
                                    createPostErrors.innerHTML = `<div>${data.message}</div>`;
                                }
                                // Keep modal open
                                if (createPostConfirmModal) hideModal(createPostConfirmModal);
                                if (createPostModal) showModal(createPostModal);
                            }
                        })
                        .catch(error => {
                            if (createPostErrors) {
                                createPostErrors.style.display = 'block';
                                createPostErrors.innerHTML = '<div>Something went wrong. Please try again.</div>';
                            }
                            if (createPostConfirmModal) hideModal(createPostConfirmModal);
                            if (createPostModal) showModal(createPostModal);
                        });
                });
            }
            if (confirmCreatePostBtn) {
                confirmCreatePostBtn.onclick = function () {
                    if (createPostForm) createPostForm.requestSubmit();
                };
            }
        });

        // Handle post options button clicks
        document.addEventListener('DOMContentLoaded', function () {
            // Close any open menus when clicking outside
            document.addEventListener('click', function (event) {
                const optionsMenus = document.querySelectorAll('.post-options-menu');
                optionsMenus.forEach(menu => {
                    if (!menu.contains(event.target) && !event.target.closest('.post-options-btn')) {
                        menu.classList.remove('show');
                    }
                });
            });

            // Handle options button clicks
            const optionsButtons = document.querySelectorAll('.post-options-btn');
            optionsButtons.forEach(button => {
                button.addEventListener('click', function (event) {
                    event.stopPropagation();
                    const menuId = `post-options-menu-${this.dataset.postId}`;
                    const menu = document.getElementById(menuId);

                    // Close all other menus
                    document.querySelectorAll('.post-options-menu').forEach(m => {
                        if (m.id !== menuId) {
                            m.classList.remove('show');
                        }
                    });

                    // Toggle current menu
                    menu.classList.toggle('show');
                });
            });
        });

        // Lightbox functionality
        document.addEventListener('DOMContentLoaded', function () {
            const lightboxModal = document.getElementById('lightbox-modal');
            const lightboxImage = lightboxModal.querySelector('.lightbox-image');
            const lightboxClose = lightboxModal.querySelector('.lightbox-close');
            const lightboxPrev = lightboxModal.querySelector('.lightbox-prev');
            const lightboxNext = lightboxModal.querySelector('.lightbox-next');
            const lightboxCounter = lightboxModal.querySelector('.lightbox-counter');

            let currentPostImages = [];
            let currentImageIndex = 0;
            let lightboxPostId = null;

            // Handle image clicks
            document.querySelectorAll('.grid-item').forEach(item => {
                item.addEventListener('click', function () {
                    const postId = this.getAttribute('data-post-id');
                    const index = parseInt(this.getAttribute('data-index'));

                    lightboxPostId = postId;

                    // Get all images for this post
                    const postImages = Array.from(document.querySelectorAll(`.grid-item[data-post-id="${postId}"] img`))
                        .map(item => item.src);

                    // If there are more than 4 images, we need to fetch all image URLs
                    if (document.querySelector(`.grid-item[data-post-id="${postId}"] .more-indicator`)) {
                        // Fetch all image URLs from the server
                        fetch(`/posts/${postId}/photos`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    currentPostImages = data.photo_urls;
                                    openLightbox(postId, index);
                                }
                            })
                            .catch(() => {
                                // Fallback to the images we have
                                currentPostImages = postImages;
                                openLightbox(postId, index);
                            });
                    } else {
                        currentPostImages = postImages;
                        openLightbox(postId, index);
                    }
                });
            });

            function openLightbox(postId, index) {
                lightboxPostId = postId;
                currentImageIndex = index;

                updateLightboxImage();
                lightboxModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }

            function updateLightboxImage() {
                lightboxImage.src = currentPostImages[currentImageIndex];
                lightboxCounter.textContent = `${currentImageIndex + 1} / ${currentPostImages.length}`;

                // Show/hide prev/next buttons based on the number of images
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
                if (lightboxModal.style.display === 'flex') {
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
        });

        // Real-time comment count update
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

        // Report Modal Checkbox Handler
        document.addEventListener('DOMContentLoaded', function () {
            const reportModal = document.getElementById('reportPostModal');
            const submitReportBtn = document.getElementById('submitReportBtn');
            const checkboxes = reportModal.querySelectorAll('input[type="checkbox"]');

            // Function to update submit button state
            function updateSubmitButton() {
                const checkedBoxes = reportModal.querySelectorAll('input[type="checkbox"]:checked');
                submitReportBtn.disabled = checkedBoxes.length === 0;
            }

            // Add event listeners to all checkboxes
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSubmitButton);
            });

            // Reset form when modal is opened
            window.showReportModal = function (postId) {
                console.log('showReportModal called for post:', postId);
                document.getElementById('reportPostForm').action = `/posts/${postId}/report`;

                // Reset all checkboxes
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });

                // Reset submit button state
                updateSubmitButton();

                showModal(document.getElementById('reportPostModal'));
            };
        });

        // Report Post Form Submission Handler
        document.addEventListener('DOMContentLoaded', function () {
            const reportPostForm = document.getElementById('reportPostForm');
            if (reportPostForm) {
                reportPostForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const form = this;

                    // Collect selected violation reasons
                    const checkedBoxes = form.querySelectorAll('input[name="violation_reasons[]"]:checked');
                    const selectedReasons = Array.from(checkedBoxes).map(cb => cb.value);

                    if (selectedReasons.length === 0) {
                        showNotification('Please select at least one reason for reporting.', 'error');
                        return;
                    }

                    // Update the hidden input with selected reasons
                    document.getElementById('selected_reasons').value = JSON.stringify(selectedReasons);

                    const formData = new FormData(form);
                    const action = form.action;

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
                                showNotification(data.message, 'success');
                            } else {
                                showNotification(data.message || 'Failed to report post.', 'error');
                            }
                            hideModal(document.getElementById('reportPostModal'));
                        })
                        .catch(() => {
                            showNotification('Failed to report post. Please try again.', 'error');
                            hideModal(document.getElementById('reportPostModal'));
                        });
                });
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
                if (elapsedSeconds >= 86400) { // Check for 24 hours (86400 seconds)
                    post.style.display = 'none';
                }
            });
        }

        // Run the check every second
        setInterval(checkTakenDownPosts, 1000);
        // Initial check
        checkTakenDownPosts();
    </script>

    <style>
        #notificationContainer {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 2000;
            width: auto;
            max-width: 90%;
        }

        .notification {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 24px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 500;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            background: #fff;
            color: #222;
            border-left: 6px solid #3F7D58;
            animation: fadeIn 0.3s;
            position: relative;
            min-width: 280px;
            max-width: 400px;
            margin-bottom: 8px;
        }

        .notification.success {
            border-left-color: #16a34a;
            background: #f0fdf4;
            color: #166534;
        }

        .notification.error {
            border-left-color: #dc2626;
            background: #fef2f2;
            color: #991b1b;
        }

        .notification .close-btn {
            background: none;
            border: none;
            color: inherit;
            font-size: 1.5rem;
            cursor: pointer;
            margin-left: 16px;
            transition: color 0.2s;
        }

        .notification .close-btn:hover {
            color: #222;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-out {
            animation: slideOut 0.3s forwards;
        }

        @keyframes slideOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }

            to {
                opacity: 0;
                transform: translateY(-10px);
            }
        }

        /* Report Modal Styles */
        .report-modal-content {
            background: white;
            border-radius: 12px;
            padding: 24px;
            max-width: 1200px;
            width: 95%;
            max-height: 90vh;
            overflow: hidden;
            position: relative;
        }

        .report-categories {
            margin: 20px 0;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 24px;
            height: auto;
        }

        .category-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 16px;
            border: 1px solid #e9ecef;
        }

        .category-section h3 {
            color: #333;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-align: center;
            justify-content: center;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 8px;
        }

        .category-section h3 i {
            color: #e74c3c;
            font-size: 14px;
        }

        .violation-options {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-top: 12px;
        }

        .other-section {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #dee2e6;
        }

        .other-section h4 {
            color: #333;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .other-section h4 i {
            color: #e74c3c;
            font-size: 13px;
        }

        .violation-option {
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 6px 8px;
            border-radius: 4px;
            transition: background-color 0.2s;
            position: relative;
            font-size: 13px;
            line-height: 1.3;
        }

        .violation-option:hover {
            background-color: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .violation-option input[type="checkbox"] {
            margin-right: 8px;
            width: 14px;
            height: 14px;
            cursor: pointer;
            flex-shrink: 0;
        }

        .violation-option .checkmark {
            display: none;
        }

        .submit-btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .submit-btn:disabled:hover {
            background-color: #ccc;
        }

        /* Dark Mode Styles for Report Modal Categories */
        body.dark-mode .report-categories {
            background: transparent;
        }

        body.dark-mode .category-section {
            background: #2d3135;
            border: 1px solid #404448;
            color: #e0e0e0;
        }

        body.dark-mode .category-section h3 {
            color: #8fd19e;
            border-bottom-color: #404448;
        }

        body.dark-mode .category-section h3 i {
            color: #8fd19e;
        }

        body.dark-mode .violation-option {
            color: #e0e0e0;
        }

        body.dark-mode .other-section {
            border-top-color: #404448;
        }

        body.dark-mode .other-section h4 {
            color: #8fd19e;
        }

        body.dark-mode .other-section h4 i {
            color: #8fd19e;
        }

        body.dark-mode .violation-option:hover {
            background-color: #404448;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        body.dark-mode .violation-option input[type="checkbox"] {
            accent-color: #8fd19e;
            background-color: #1a1d20;
            border-color: #404448;
        }

        body.dark-mode .submit-btn:disabled {
            background-color: #404448;
            color: #666;
        }

        /* Dark Mode Styles for Report Modal Buttons */
        body.dark-mode #reportPostModal .cancel-btn {
            background-color: #23272b !important;
            color: #8fd19e !important;
            border: 1px solid #8fd19e !important;
        }

        body.dark-mode #reportPostModal .cancel-btn:hover {
            background-color: #181a1b !important;
            color: #fff !important;
        }

        body.dark-mode #reportPostModal .submit-btn {
            background-color: #8fd19e !important;
            color: #23272b !important;
            border: 1px solid #8fd19e !important;
        }

        body.dark-mode #reportPostModal .submit-btn:hover {
            background-color: #6fcf97 !important;
            color: #23272b !important;
        }

        body.dark-mode #reportPostModal .submit-btn:disabled {
            background-color: #404448 !important;
            color: #666 !important;
            border: 1px solid #404448 !important;
        }

        body.dark-mode #reportPostModal .submit-btn:disabled:hover {
            background-color: #404448 !important;
            color: #666 !important;
        }

        body.dark-mode #reportPostModal .form-actions {
            border-top: 1px solid #404448 !important;
        }

        /* Responsive design for report modal */
        @media (max-width: 1024px) {
            .report-modal-content {
                max-width: 900px;
            }

            .report-categories {
                gap: 16px;
            }

            .violation-option {
                font-size: 12px;
                padding: 5px 6px;
            }
        }

        @media (max-width: 768px) {
            .report-modal-content {
                width: 98%;
                padding: 16px;
                max-height: 95vh;
            }

            .report-categories {
                grid-template-columns: 1fr;
                gap: 16px;
                max-height: 60vh;
                overflow-y: auto;
            }

            .category-section {
                padding: 12px;
            }

            .violation-option {
                padding: 8px 6px;
                font-size: 13px;
            }

            .category-section h3 {
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .report-modal-content {
                padding: 12px;
            }

            .violation-option {
                font-size: 12px;
                padding: 6px 4px;
            }
        }

        /* Shared Post Takedown Styling */
        .original-post-content .violation-banner {
            margin: 15px 0;
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 12px 16px;
            color: #856404;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }

        .original-post-content .violation-banner i {
            color: #f39c12;
            font-size: 16px;
        }

        .original-post-header {
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .original-post-header .post-user-info {
            opacity: 0.8;
        }

        /* Enhanced takedown banner for shared content */
        .shared-post-main-container .original-post-content {
            position: relative;
        }

        .shared-post-main-container .original-post-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 243, 205, 0.1);
            border-radius: 8px;
            pointer-events: none;
        }
    </style>
@endsection