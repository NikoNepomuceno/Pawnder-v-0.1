@extends('layouts.app')
@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/profile.css') }}">
    <!-- Cloudinary Upload Widget -->
    <script src="https://upload-widget.cloudinary.com/global/all.js" type="text/javascript"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
@section('content')
<div class="profile-container">
    <div class="profile-banner">
        @if($user->banner_image)
            <img src="{{ $user->banner_image }}" alt="Banner">
        @else
            <img src="{{ asset('images/default_banner.png') }}" alt="Default Banner">
        @endif
        <button class="profile-edit-btn" onclick="const modal = document.getElementById('editProfileModal'); if(modal) showModal(modal);"><i class="fas fa-pencil-alt"></i></button>
        <div class="profile-header-row">
            <div class="profile-picture">
                @if($user->profile_picture)
                    <img src="{{ $user->profile_picture }}" alt="Profile Picture">
                @else
                    <img src="{{ asset('images/default-profile.png') }}" alt="Default Profile Picture">
                @endif
            </div>
            <div class="profile-info-card">
                <h1>{{ $user->username }}</h1>
                <p>{{ $user->email }}</p>
            </div>
        </div>
    </div>
    <div class="profile-content" style="margin-top:0;">
        <div class="posts-container">
            <h2>Your Posts</h2>
            @if($posts->isEmpty())
                <div class="no-posts-message">
                    <i class="fas fa-box-open"></i>
                    <p>You have made no reports/posts yet.</p>
                </div>
            @else
                @foreach($posts as $post)
                    <div class="post-card" data-post-id="{{ $post->id }}">
                        @if($post->is_flagged)
                            <div class="flag-notification"><i class="fas fa-exclamation-triangle"></i> {{ $post->flag_reason }}</div>
                        @endif
                        <!-- Shared Post Layout -->
                        @if($post->isShared())
                            <!-- Sharer's Post Header -->
                            <div class="post-header">
                                <div class="post-user-info">
                                    <img src="{{ $post->sharedBy->profile_picture ?? asset('images/default-profile.png') }}" alt="Profile" class="post-avatar">
                                    <div>
                                        <h4 class="post-author">{{ $post->sharedBy->name }} <span style="font-weight:normal; color:#888;"><i class="fas fa-share-alt" style="margin-right: 4px;"></i>shared</span></h4>
                                        <span class="post-date">{{ $post->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <div class="post-actions profile-post-actions">
                                    <button class="delete-btn">Delete</button>
                                </div>
                            </div>
                            @php $original = $post->originalPost; @endphp
                            <!-- Original Post Content -->
                            <div class="original-post-content">
                                @if($original && !$original->trashed())
                                    <!-- Original Post Creator's Post Header -->
                                    <div class="post-header original-post-header">
                                        <div class="post-user-info">
                                            <img src="{{ $original->user->profile_picture ?? asset('images/default-profile.png') }}" alt="Profile" class="post-avatar">
                                            <div>
                                                <h4 class="post-author">{{ $original->user->name }}</h4>
                                                <span class="post-date">{{ $original->created_at->diffForHumans() }}</span>
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
                                            <div class="image-grid {{ count($original->photo_urls) === 1 ? 'single-image' : '' }} {{ count($original->photo_urls) === 2 ? 'two-images' : '' }} {{ count($original->photo_urls) === 3 ? 'three-images' : '' }}">
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
                                @else
                                    <!-- Deleted Post Content -->
                                    <div class="post-content deleted-post">
                                        <h3>This post has been deleted</h3>
                                        <p class="post-description">The original post has been deleted by the author.</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="post-header">
                                <div class="post-user-info">
                                    <img src="{{ $post->user->profile_picture ?? asset('images/default-profile.png') }}" alt="Profile" class="post-avatar">
                                    <div>
                                        <h4 class="post-author">{{ $post->user->name }}</h4>
                                        <span class="post-date">{{ $post->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <div class="post-actions profile-post-actions">
                                    <button class="edit-btn" onclick="editPost({{ $post->id }})">Edit</button>
                                    <button class="delete-btn">Delete</button>
                                </div>
                            </div>
                            <div class="post-details">
                                <span class="post-status {{ $post->status }}">{{ ucfirst($post->status) }}</span>
                                <span class="post-breed">Breed: {{ $post->breed }}</span>
                                <span class="post-location">Location: {{ $post->location }}</span>
                                <span class="post-contact">Contact: {{ $post->contact }}</span>
                            </div>
                            <div class="post-images">
                                @if(count($post->photo_urls ?? []) > 0)
                                    <div class="image-grid {{ count($post->photo_urls) === 1 ? 'single-image' : '' }} {{ count($post->photo_urls) === 2 ? 'two-images' : '' }} {{ count($post->photo_urls) === 3 ? 'three-images' : '' }}">
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
                            <div class="post-content">
                                <h3>{{ $post->title }}</h3>
                                <p class="post-description">{{ $post->description }}</p>
                            </div>
                        @endif
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
                                data-liked="{{ $post->current_user_reaction === 'like' ? '1' : '0' }}">
                                <i class="fas fa-heart"></i>
                                <span>{{ $post->current_user_reaction === 'like' ? 'Liked' : 'Like' }}</span>
                            </button>
                            <button class="post-action-btn comment-btn" data-post-id="{{ $post->id }}">
                                <i class="far fa-comment"></i> Comment
                            </button>
                            <button class="post-action-btn share-btn" data-post-id="{{ $post->id }}">
                                <i class="far fa-share-square"></i> Share
                            </button>
                        </div>
                        <div class="post-reaction-panel" id="reaction-panel-{{ $post->id }}">
                            <button class="reaction-icon" data-reaction="like" data-post-id="{{ $post->id }}"><i class="fas fa-heart"></i></button>
                            <button class="reaction-icon" data-reaction="love" data-post-id="{{ $post->id }}"><i class="fas fa-heart"></i></button>
                            <button class="reaction-icon" data-reaction="care" data-post-id="{{ $post->id }}"><i class="fas fa-paw"></i></button>
                            <button class="reaction-icon" data-reaction="wow" data-post-id="{{ $post->id }}"><i class="fas fa-surprise"></i></button>
                        </div>
                        <!-- Comments Modal -->
                        <div class="modal comments-modal" id="comments-modal-{{ $post->id }}">
                            <div class="modal-content comments-modal-content">
                                <span class="close-modal">&times;</span>
                                <div class="comments-container">
                                    <div class="comments-header"><h3>Comments</h3></div>
                                    <div class="comments-list" id="comments-list-{{ $post->id }}"><!-- Comments will be loaded here --></div>
                                    <form class="comment-form" data-post-id="{{ $post->id }}">
                                        @csrf
                                        <img src="{{ Auth::user()->profile_picture ?? asset('images/default-profile.png') }}" alt="Profile" class="comment-avatar">
                                        <input type="text" class="comment-input" placeholder="Write a comment..." required>
                                        <button type="submit" class="comment-submit"><i class="fas fa-paper-plane"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
<!-- Edit Profile Modal -->
<div id="editProfileModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="const modal = document.getElementById('editProfileModal'); if(modal) hideModal(modal);">&times;</span>
        <h2>Edit Profile</h2>
        <form id="editProfileForm" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="edit-profile-sections">
                <div class="edit-profile-section">
                    <h3 class="edit-profile-section-title">Basic Info</h3>
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" value="{{ $user->username }}" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="{{ $user->email }}" required>
                    </div>
                </div>
                <div class="edit-profile-section">
                    <h3 class="edit-profile-section-title">Profile Images</h3>
                    <div class="form-group">
                        <label for="profile_picture">Profile Picture:</label>
                        <button type="button" class="file-input-button" id="upload_profile_widget">Upload Profile Picture</button>
                        <div id="profile-preview" class="photo-preview">
                            @if($user->profile_picture)
                                <div class="preview-image-container">
                                    <img src="{{ $user->profile_picture }}" class="preview-image">
                                </div>
                            @endif
                        </div>
                        <input type="hidden" id="profile_picture" name="profile_picture" value="{{ $user->profile_picture }}">
                    </div>
                    <div class="form-group">
                        <label for="banner_image">Banner Image:</label>
                        <button type="button" class="file-input-button" id="upload_banner_widget">Upload Banner Image</button>
                        <div id="banner-preview" class="photo-preview">
                            @if($user->banner_image)
                                <div class="preview-image-container">
                                    <img src="{{ $user->banner_image }}" class="preview-image">
                                </div>
                            @endif
                        </div>
                        <input type="hidden" id="banner_image" name="banner_image" value="{{ $user->banner_image }}">
                    </div>
                </div>
            </div>
            <button type="button" id="saveProfileChangesBtn">Save Changes</button>
        </form>
    </div>
</div>
<!-- Edit Post Modal -->
<div id="editPostModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="const modal = document.getElementById('editPostModal'); if(modal) hideModal(modal);">&times;</span>
        <h2>Edit Post</h2>
        <form id="editPostForm" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label for="post_title"><i class="fas fa-heading" style="color: #3F7D58;"></i>Title</label>
                <input type="text" id="post_title" name="title" required placeholder="Enter post title">
            </div>
            <div class="form-group description-group">
                <label for="post_description"><i class="fas fa-align-left" style="color: #3F7D58;"></i>Description</label>
                <textarea id="post_description" name="description" required placeholder="Provide details about your pet..."></textarea>
            </div>
            <div class="form-group">
                <label for="post_status"><i class="fas fa-tag" style="color: #3F7D58;"></i>Status</label>
                <select id="post_status" name="status" required>
                    <option value="not_found">Not Found</option>
                    <option value="found">Found</option>
                </select>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="post_breed"><i class="fas fa-paw" style="color: #3F7D58;"></i>Breed</label>
                    <input type="text" id="post_breed" name="breed" required placeholder="Pet breed">
                </div>
                <div class="form-group">
                    <label for="post_location"><i class="fas fa-map-marker-alt" style="color: #3F7D58;"></i>Location</label>
                    <input type="text" id="post_location" name="location" required placeholder="Where was the pet found/lost">
                </div>
            </div>
            <div class="form-group">
                <label for="post_contact"><i class="fas fa-phone" style="color: #3F7D58;"></i>Contact Information</label>
                <input type="text" id="post_contact" name="contact" required placeholder="Your contact details">
            </div>
            <div class="form-group">
                <label for="post_photos"><i class="fas fa-images" style="color: #3F7D58;"></i>Photos</label>
                <button type="button" class="file-input-button" id="upload_post_photos_widget">Upload Photos</button>
                <div class="photo-hint">Upload up to 5 photos (optional)</div>
                <div id="post-photos-preview" class="photo-preview"></div>
                <input type="hidden" id="post_photo_urls" name="photo_urls" value="">
            </div>
            <button type="button" id="savePostChangesBtn">Save Changes</button>
        </form>
    </div>
</div>
<!-- Save Changes Confirmation Modal -->
<div id="saveChangesConfirmModal" class="modal">
    <div class="modal-content">
        <h3>Confirm Save Changes</h3>
        <p>Are you sure you want to save these changes?</p>
        <div class="form-actions">
            <button type="button" class="cancel-btn" id="cancelSaveBtn">Cancel</button>
            <button type="button" class="submit-btn" id="confirmSaveBtn">Save Changes</button>
        </div>
    </div>
</div>
<!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="modal">
    <div class="modal-content">
        <h3>Confirm Deletion</h3>
        <p>Are you sure you want to delete this post? This action cannot be undone.</p>
        <div class="form-actions">
            <button type="button" class="cancel-btn" id="cancelDeleteBtn">Cancel</button>
            <button type="button" class="submit-btn" id="confirmDeleteBtn">Delete</button>
        </div>
    </div>
</div>
<!-- Profile Save Changes Confirmation Modal -->
<div id="saveProfileConfirmModal" class="modal">
    <div class="modal-content">
        <h3>Confirm Profile Changes</h3>
        <p>Are you sure you want to save these profile changes?</p>
        <div class="form-actions">
            <button type="button" class="cancel-btn" id="cancelProfileSaveBtn">Cancel</button>
            <button type="button" class="submit-btn" id="confirmProfileSaveBtn">Save Changes</button>
        </div>
    </div>
</div>
<!-- Share Post Confirmation Modal -->
<div id="sharePostConfirmModal" class="modal">
    <div class="modal-content" style="max-width: 400px;">
        <h3>Confirm Share</h3>
        <p>Do you want to share this post to your feed?</p>
        <div class="form-actions">
            <button type="button" class="cancel-btn" id="cancelSharePostBtn">Cancel</button>
            <button type="button" class="submit-btn" id="confirmSharePostBtn">Share</button>
        </div>
    </div>
</div>
<!-- Add lightbox modal at the end of the page -->
<div class="lightbox-modal" id="lightbox-modal">
    <span class="lightbox-close">&times;</span>
    <div class="lightbox-content">
        <div class="lightbox-prev"><i class="fas fa-chevron-left"></i></div>
        <img class="lightbox-image" src="" alt="Full size image">
        <div class="lightbox-next"><i class="fas fa-chevron-right"></i></div>
    </div>
    <div class="lightbox-counter"></div>
</div>
<style>
/* Fade-out animation for post card removal */
.post-card.fade-out {
    opacity: 0;
    transition: opacity 0.4s ease;
}

/* Heart icon color styles */
.post-stats .reaction-count i,
.post-actions .reaction-btn i,
.post-reaction-panel .reaction-icon i {
    color: #ff4d4d;
}

.post-stats .reaction-count i:hover,
.post-actions .reaction-btn i:hover,
.post-reaction-panel .reaction-icon i:hover {
    color: #ff0000;
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

/* Deleted post styling */
.deleted-post {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin: 10px 0;
}

.deleted-post h3 {
    color: #6c757d;
    font-size: 1.1em;
    margin-bottom: 8px;
}

.deleted-post .post-description {
    color: #6c757d;
    font-style: italic;
}
</style>
<script>
// Create a namespace for edit post functionality
const EditPostManager = {
    editingPostId: null,
    modals: {
        editPost: null,
        saveChanges: null,
        delete: null
    },
    elements: {
        form: null,
        saveBtn: null,
        cancelBtn: null,
        confirmBtn: null,
        uploadBtn: null,
        photoPreview: null,
        photoUrlsInput: null
    },
    cloudinaryWidget: null,

    init() {
        // Initialize modal references
        this.modals.editPost = document.getElementById('editPostModal');
        this.modals.saveChanges = document.getElementById('saveChangesConfirmModal');
        this.modals.delete = document.getElementById('deleteConfirmModal');

        // Initialize element references
        this.elements.form = document.getElementById('editPostForm');
        this.elements.saveBtn = document.getElementById('savePostChangesBtn');
        this.elements.cancelBtn = document.getElementById('cancelSaveBtn');
        this.elements.confirmBtn = document.getElementById('confirmSaveBtn');
        this.elements.uploadBtn = document.getElementById('upload_post_photos_widget');
        this.elements.photoPreview = document.getElementById('post-photos-preview');
        this.elements.photoUrlsInput = document.getElementById('post_photo_urls');

        // Initialize Cloudinary widget
        this.initCloudinaryWidget();

        // Bind event listeners
        this.bindEventListeners();
    },

    initCloudinaryWidget() {
        const cloudName = '{{ config('cloudinary.cloud_name') }}';
        const uploadPreset = '{{ config('cloudinary.upload_preset') }}';

        this.cloudinaryWidget = cloudinary.createUploadWidget({
            cloudName: cloudName,
            uploadPreset: uploadPreset,
            sources: ['local'],
            multiple: true,
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
        }, this.handleCloudinaryUpload.bind(this));
    },

    handleCloudinaryUpload(error, result) {
        if (error) {
            console.error('Upload error:', error);
            this.showNotification('Error uploading photo. Please try again.', 'error');
            return;
        }

        if (result && result.event === "success") {
            const imageUrl = result.info.secure_url;
            this.addPhotoToPreview(imageUrl);
        }
    },

    addPhotoToPreview(imageUrl) {
        try {
            let photoUrls = [];
            if (this.elements.photoUrlsInput.value) {
                photoUrls = JSON.parse(this.elements.photoUrlsInput.value);
            }

            if (photoUrls.length >= 5) {
                this.showNotification('Maximum of 5 photos allowed.', 'error');
                return;
            }

            photoUrls.push(imageUrl);
            this.elements.photoUrlsInput.value = JSON.stringify(photoUrls);
            this.updatePhotoPreview(photoUrls);
        } catch (e) {
            console.error('Error handling upload result:', e);
            this.showNotification('Error processing uploaded photo. Please try again.', 'error');
        }
    },

    updatePhotoPreview(photoUrls) {
        this.elements.photoPreview.innerHTML = '';
        
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
            removeBtn.onclick = () => this.removePhoto(index);

            container.appendChild(img);
            container.appendChild(removeBtn);
            this.elements.photoPreview.appendChild(container);
        });
    },

    removePhoto(index) {
        const photoUrls = JSON.parse(this.elements.photoUrlsInput.value);
        photoUrls.splice(index, 1);
        this.elements.photoUrlsInput.value = JSON.stringify(photoUrls);
        this.updatePhotoPreview(photoUrls);
    },

    bindEventListeners() {
        // Edit button click handlers
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const postId = button.closest('.post-card').dataset.postId;
                this.editPost(postId);
            });
        });

        // Save changes button
        this.elements.saveBtn?.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.validateAndShowSaveConfirmation();
        });

        // Cancel button
        this.elements.cancelBtn?.addEventListener('click', () => {
            this.hideModal(this.modals.saveChanges);
        });

        // Confirm save button
        this.elements.confirmBtn?.addEventListener('click', () => {
            this.submitForm();
        });

        // Upload button
        this.elements.uploadBtn?.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.cloudinaryWidget.open();
        });

        // Close modals when clicking outside
        window.addEventListener('click', (event) => {
            Object.values(this.modals).forEach(modal => {
                if (event.target === modal) {
                    this.hideModal(modal);
                }
            });
        });

        // Delete button click handlers
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const postId = button.closest('.post-card').dataset.postId;
                console.log('Delete button clicked for postId:', postId);
                this.deletePost(postId);
            });
        });

        // Confirm delete button handler
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        if (confirmDeleteBtn) {
            confirmDeleteBtn.onclick = null; // Remove any previous handler
            confirmDeleteBtn.addEventListener('click', () => {
                console.log('Confirm delete button clicked');
                const postId = this.editingPostId;
                console.log('Attempting to delete postId:', postId, 'URL:', `/posts/${postId}`);
                if (!postId) return;
                fetch(`/posts/${postId}`, {
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
                        // Remove the post card from the DOM with fade-out
                        const postCard = document.querySelector(`.post-card[data-post-id="${postId}"]`);
                        if (postCard) {
                            postCard.classList.add('fade-out');
                            setTimeout(() => postCard.remove(), 400);
                        }
                        // Show global notification
                        this.showNotification(data.message || 'Post deleted successfully!', 'success');
                    } else {
                        this.showNotification(data.message || 'Failed to delete post.', 'error');
                    }
                })
                .catch(error => {
                    this.showNotification('Failed to delete post. Please try again.', 'error');
                    console.error('Error deleting post:', error);
                })
                .finally(() => {
                    if (this.modals.delete) this.hideModal(this.modals.delete);
                    this.editingPostId = null;
                });
            });
        }

        // Cancel button for Delete Confirmation Modal
        const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
        if (cancelDeleteBtn) {
            cancelDeleteBtn.addEventListener('click', () => {
                this.hideModal(this.modals.delete);
            });
        }
    },

    editPost(postId) {
        this.editingPostId = postId;
        const postCard = document.querySelector(`.post-card[data-post-id="${postId}"]`);
        
        if (!postCard) {
            this.showNotification('Post not found', 'error');
            return;
        }

        try {
            // Set form action
            this.elements.form.action = `/posts/${postId}`;
            
            // Populate form fields
            document.getElementById('post_title').value = postCard.querySelector('h3').textContent.trim();
            document.getElementById('post_description').value = postCard.querySelector('.post-description').textContent.trim();
            document.getElementById('post_status').value = postCard.querySelector('.post-status').textContent.toLowerCase().trim();
            document.getElementById('post_breed').value = postCard.querySelector('.post-breed').textContent.replace('Breed:', '').trim();
            document.getElementById('post_location').value = postCard.querySelector('.post-location').textContent.replace('Location:', '').trim();
            document.getElementById('post_contact').value = postCard.querySelector('.post-contact').textContent.replace('Contact:', '').trim();
            
            // Handle photos
            const photoUrls = [];
            postCard.querySelectorAll('.grid-item img').forEach(img => {
                photoUrls.push(img.src);
            });
            if (photoUrls.length > 0) {
                this.elements.photoUrlsInput.value = JSON.stringify(photoUrls);
                this.updatePhotoPreview(photoUrls);
            }
            
            this.showModal(this.modals.editPost);
        } catch (error) {
            console.error('Error in editPost:', error);
            this.showNotification('Error loading post data. Please try again.', 'error');
        }
    },

    validateAndShowSaveConfirmation() {
        const requiredFields = {
            title: document.getElementById('post_title').value,
            description: document.getElementById('post_description').value,
            status: document.getElementById('post_status').value,
            breed: document.getElementById('post_breed').value,
            location: document.getElementById('post_location').value,
            contact: document.getElementById('post_contact').value
        };

        const missingFields = Object.entries(requiredFields)
            .filter(([_, value]) => !value)
            .map(([key]) => key);

        if (missingFields.length > 0) {
            this.showNotification(`Please fill in all required fields: ${missingFields.join(', ')}`, 'error');
            return;
        }

        this.showModal(this.modals.saveChanges);
    },

    submitForm() {
        if (!this.elements.form) return;

        // Gather form data
        const formData = new FormData(this.elements.form);
        formData.append('_method', 'PUT');

        fetch(this.elements.form.action, {
            method: 'POST', // Laravel expects POST with _method=PUT
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification(data.message || 'Post updated successfully!', 'success');
                this.hideModal(this.modals.editPost);
                this.hideModal(this.modals.saveChanges);
                // Reload the page after a short delay
                setTimeout(() => { window.location.reload(); }, 1200);
            } else {
                this.showNotification(data.message || 'Failed to update post.', 'error');
            }
        })
        .catch(error => {
            this.showNotification('An error occurred. Please try again.', 'error');
            console.error(error);
        });
    },

    showModal(modal) {
        console.log('showModal called with:', modal);
        if (!modal) return;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        // Force reflow to ensure transition works
        modal.offsetHeight;
    },

    hideModal(modal) {
        if (!modal) return;
        modal.style.display = 'none';
        document.body.style.overflow = '';
        modal.classList.remove('modal-open');
    },

    showNotification(message, type = 'success') {
        // Use the global notification system
        if (typeof window.showNotification === 'function') {
            window.showNotification(message, type);
        } else {
            // fallback: alert
            alert(message);
        }
    },

    deletePost(postId) {
        this.editingPostId = postId;
        console.log('deletePost called, this.modals.delete:', this.modals.delete);
        if (this.modals.delete) this.showModal(this.modals.delete);
    }
};

// Initialize the EditPostManager when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    EditPostManager.init();
});

let editingPostId = null;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all modals
    const editPostModal = document.getElementById('editPostModal');
    const saveChangesConfirmModal = document.getElementById('saveChangesConfirmModal');
    const deleteConfirmModal = document.getElementById('deleteConfirmModal');
    
    // Initialize edit buttons
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const postId = this.closest('.post-card').dataset.postId;
            editPost(postId);
        });
    });
    
    // Initialize delete buttons
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const postId = this.closest('.post-card').dataset.postId;
            deletePost(postId);
        });
    });

    // Initialize save changes button
    const savePostChangesBtn = document.getElementById('savePostChangesBtn');
    if (savePostChangesBtn) {
        savePostChangesBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            console.log('Save Changes button clicked');
            
            // Basic form validation
            const title = document.getElementById('post_title').value;
            const description = document.getElementById('post_description').value;
            const status = document.getElementById('post_status').value;
            const breed = document.getElementById('post_breed').value;
            const location = document.getElementById('post_location').value;
            const contact = document.getElementById('post_contact').value;

            if (!title || !description || !status || !breed || !location || !contact) {
                alert('Please fill in all required fields');
                return;
            }

            if (saveChangesConfirmModal) {
                showModal(saveChangesConfirmModal);
            }
        });
    }
});

function showModal(modal) {
    console.log('showModal called with:', modal);
    if (!modal) {
        console.error('Modal not found');
        return;
    }
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    // Force reflow to ensure transition works
    modal.offsetHeight;
}

function hideModal(modal) {
    console.log('hideModal called with:', modal);
    if (!modal) {
        console.error('Modal not found');
        return;
    }
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

function editPost(postId) {
    console.log('Edit post called with ID:', postId);
    editingPostId = postId;
    const editPostModal = document.getElementById('editPostModal');
    const postCard = document.querySelector(`.post-card[data-post-id="${postId}"]`);
    
    if (!editPostModal) {
        console.error('Edit post modal not found');
        return;
    }
    
    if (!postCard) {
        console.error('Post card not found for ID:', postId);
        return;
    }
    
    try {
        // Set form action
        const form = document.getElementById('editPostForm');
        form.action = `/posts/${postId}`;
        
        // Populate form fields
        document.getElementById('post_title').value = postCard.querySelector('h3').textContent.trim();
        document.getElementById('post_description').value = postCard.querySelector('.post-description').textContent.trim();
        document.getElementById('post_status').value = postCard.querySelector('.post-status').textContent.toLowerCase().trim();
        document.getElementById('post_breed').value = postCard.querySelector('.post-breed').textContent.replace('Breed:', '').trim();
        document.getElementById('post_location').value = postCard.querySelector('.post-location').textContent.replace('Location:', '').trim();
        document.getElementById('post_contact').value = postCard.querySelector('.post-contact').textContent.replace('Contact:', '').trim();
        
        // Handle photos
        const photoUrls = [];
        postCard.querySelectorAll('.grid-item img').forEach(img => {
            photoUrls.push(img.src);
        });
        if (photoUrls.length > 0) {
            populatePhotosPreview(photoUrls);
        }
        
        showModal(editPostModal);
    } catch (error) {
        console.error('Error in editPost:', error);
        alert('Error loading post data. Please try again.');
    }
}

function deletePost(postId) {
    EditPostManager.editingPostId = postId;
    const modal = document.getElementById('deleteConfirmModal');
    if (modal) EditPostManager.showModal(modal);
}

// Function to show modal
function showModal(modal) {
    console.log('showModal called with:', modal);
    if (!modal) {
        console.error('Modal not found');
        return;
    }
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    // Force reflow to ensure transition works
    modal.offsetHeight;
}

// Function to hide modal
function hideModal(modal) {
    console.log('hideModal called with:', modal);
    if (!modal) {
        console.error('Modal not found');
        return;
    }
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

// Close modals when clicking outside
window.addEventListener('click', (event) => {
    const editPostModal = document.getElementById('editPostModal');
    const saveChangesConfirmModal = document.getElementById('saveChangesConfirmModal');
    const deleteConfirmModal = document.getElementById('deleteConfirmModal');
    const saveProfileConfirmModal = document.getElementById('saveProfileConfirmModal');
    const sharePostConfirmModal = document.getElementById('sharePostConfirmModal');

    if (event.target === editPostModal) {
        hideModal(editPostModal);
    }
    if (event.target === saveChangesConfirmModal) {
        hideModal(saveChangesConfirmModal);
    }
    if (event.target === deleteConfirmModal) {
        hideModal(deleteConfirmModal);
    }
    if (event.target === saveProfileConfirmModal) {
        hideModal(saveProfileConfirmModal);
    }
    if (event.target === sharePostConfirmModal) {
        hideModal(sharePostConfirmModal);
    }
});

// Cloudinary Upload Widget for Profile Picture
const cloudName='{{ config('cloudinary.cloud_name') }}';
const uploadPreset='{{ config('cloudinary.upload_preset') }}';

console.log('Cloudinary Config:', {
        cloudName, uploadPreset
    }

);

const profileWidget=cloudinary.createUploadWidget( {

        cloudName: cloudName,
        uploadPreset: uploadPreset,
        sources: ['local'],
        multiple: false,
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
        if (error) {
            console.error('Upload error:', error);
            return;
        }

        if (result && result.event==="success") {
            const imageUrl=result.info.secure_url;
            console.log('Uploaded profile image:', imageUrl);

            document.getElementById('profile_picture').value=imageUrl;
            const preview=document.getElementById('profile-preview');
            preview.innerHTML=` <div class="preview-image-container"> <img src="${imageUrl}"class="preview-image"> </div> `;
        }
    }

);

// Cloudinary Upload Widget for Banner Image
const bannerWidget=cloudinary.createUploadWidget( {

        cloudName: cloudName,
        uploadPreset: uploadPreset,
        sources: ['local'],
        multiple: false,
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
        if (error) {
            console.error('Upload error:', error);
            return;
        }

        if (result && result.event==="success") {
            const imageUrl=result.info.secure_url;
            console.log('Uploaded banner image:', imageUrl);

            document.getElementById('banner_image').value=imageUrl;
            const preview=document.getElementById('banner-preview');
            preview.innerHTML=` <div class="preview-image-container"> <img src="${imageUrl}"class="preview-image"> </div> `;
        }
    }

);

// Open Cloudinary widgets when the upload buttons are clicked
document.getElementById('upload_profile_widget').addEventListener('click', function (e) {
        e.preventDefault();
        console.log('Opening profile widget');
        profileWidget.open();
    }

    , false);

document.getElementById('upload_banner_widget').addEventListener('click', function (e) {
        e.preventDefault();
        console.log('Opening banner widget');
        bannerWidget.open();
    }

    , false);

// Profile Save Changes Confirmation
const saveProfileChangesBtn=document.getElementById('saveProfileChangesBtn');
const saveProfileConfirmModal=document.getElementById('saveProfileConfirmModal');
const cancelProfileSaveBtn=document.getElementById('cancelProfileSaveBtn');
const confirmProfileSaveBtn=document.getElementById('confirmProfileSaveBtn');

saveProfileChangesBtn.addEventListener('click', () => {
    const modal = document.getElementById('saveProfileConfirmModal');
    if (modal) showModal(modal);
});

cancelProfileSaveBtn.addEventListener('click', () => {
    const modal = document.getElementById('saveProfileConfirmModal');
    if (modal) hideModal(modal);
});

confirmProfileSaveBtn.addEventListener('click', () => {
    document.getElementById('editProfileForm').submit();
    const modal = document.getElementById('saveProfileConfirmModal');
    if (modal) hideModal(modal);
});

// Add hover effects for buttons
saveProfileChangesBtn.addEventListener('mouseenter', ()=> {
        saveProfileChangesBtn.style.backgroundColor='#4a8d65';
        saveProfileChangesBtn.style.transform='';
        saveProfileChangesBtn.style.boxShadow='';
    }

);

saveProfileChangesBtn.addEventListener('mouseleave', ()=> {
        saveProfileChangesBtn.style.backgroundColor='#3F7D58';
        saveProfileChangesBtn.style.transform='';
        saveProfileChangesBtn.style.boxShadow='';
    }

);

savePostChangesBtn.addEventListener('mouseenter', ()=> {
        savePostChangesBtn.style.backgroundColor='#4a8d65';
        savePostChangesBtn.style.transform='';
        savePostChangesBtn.style.boxShadow='';
    }

);

savePostChangesBtn.addEventListener('mouseleave', ()=> {
        savePostChangesBtn.style.backgroundColor='#3F7D58';
        savePostChangesBtn.style.transform='';
        savePostChangesBtn.style.boxShadow='';
    }

);

// Image grid and lightbox functionality
const lightboxModal=document.getElementById('lightbox-modal');
const lightboxImage=document.querySelector('.lightbox-image');
const lightboxClose=document.querySelector('.lightbox-close');
const lightboxPrev=document.querySelector('.lightbox-prev');
const lightboxNext=document.querySelector('.lightbox-next');
const lightboxCounter=document.querySelector('.lightbox-counter');

let currentImageIndex=0;
let currentPostImages=[];

// Open lightbox when clicking on an image
document.querySelectorAll('.grid-item').forEach(item=> {
        item.addEventListener('click', function () {
                const postId=this.getAttribute('data-post-id');
                const index=parseInt(this.getAttribute('data-index'));

                lightboxPostId=postId;

                // Get all images for this post
                const postImages=Array.from(document.querySelectorAll(`.grid-item[data-post-id="${postId}"] img`)) .map(item=> item.src);

                // If there are more than 4 images, we need to fetch all image URLs
                if (document.querySelector(`.grid-item[data-post-id="${postId}"] .more-indicator`)) {

                    // This would ideally be an AJAX call to get all image URLs
                    // For now, we'll use what we have
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
                            // Fallback to the images we have
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

        );
    }

);

function openLightbox(postId, index) {
    lightboxPostId=postId;
    currentImageIndex=index;

    updateLightboxImage();
    lightboxModal.style.display='block';

    // Prevent scrolling on the body
    document.body.style.overflow='hidden';
}

function updateLightboxImage() {
    lightboxImage.src=currentPostImages[currentImageIndex];

    lightboxCounter.textContent=`$ {
        currentImageIndex+1
    }

    / $ {
        currentPostImages.length
    }

    `;

    // Show/hide prev/next buttons based on the number of images
    lightboxPrev.style.display=currentPostImages.length>1 ? 'flex' : 'none';
    lightboxNext.style.display=currentPostImages.length>1 ? 'flex' : 'none';
}

// Close lightbox
lightboxClose.addEventListener('click', function () {
        lightboxModal.style.display='none';
        document.body.style.overflow='';
    }

);

// Navigate to previous image
lightboxPrev.addEventListener('click', function () {
        currentImageIndex=(currentImageIndex - 1 + currentPostImages.length) % currentPostImages.length;
        updateLightboxImage();
    }

);

// Navigate to next image
lightboxNext.addEventListener('click', function () {
        currentImageIndex=(currentImageIndex + 1) % currentPostImages.length;
        updateLightboxImage();
    }

);

// Close lightbox when clicking outside the image
lightboxModal.addEventListener('click', function (event) {
        if (event.target===lightboxModal) {
            lightboxModal.style.display='none';
            document.body.style.overflow='';
        }
    }

);

// Keyboard navigation
document.addEventListener('keydown', function (event) {
        if (lightboxModal.style.display==='block') {
            if (event.key==='Escape') {
                lightboxModal.style.display='none';
                document.body.style.overflow='';
            }

            else if (event.key==='ArrowLeft') {
                currentImageIndex=(currentImageIndex - 1 + currentPostImages.length) % currentPostImages.length;
                updateLightboxImage();
            }

            else if (event.key==='ArrowRight') {
                currentImageIndex=(currentImageIndex + 1) % currentPostImages.length;
                updateLightboxImage();
            }
        }
    }

);

// Cloudinary Upload Widget for Post Photos
const postPhotosWidget = cloudinary.createUploadWidget({
    cloudName: cloudName,
    uploadPreset: uploadPreset,
    sources: ['local'],
    multiple: true,
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
            const photoUrlsInput = document.getElementById('post_photo_urls');

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
            const preview = document.getElementById('post-photos-preview');
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
    const uploadButton = document.getElementById('upload_post_photos_widget');
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

// Function to populate the photos preview when editing
function populatePhotosPreview(photoUrls) {
    const preview=document.getElementById('post-photos-preview');
    const photoUrlsInput=document.getElementById('post_photo_urls');

    preview.innerHTML='';
    photoUrlsInput.value=JSON.stringify(photoUrls);

    if ( !photoUrls || photoUrls.length===0) {
        return;
    }

    photoUrls.forEach((url, index)=> {
            const container=document.createElement('div');
            container.className='preview-image-container';

            const img=document.createElement('img');
            img.src=url;
            img.className='preview-image';

            const removeBtn=document.createElement('button');
            removeBtn.type='button';
            removeBtn.className='remove-image-btn';
            removeBtn.textContent='×';

            removeBtn.onclick=function () {
                const currentUrls=JSON.parse(photoUrlsInput.value);
                currentUrls.splice(index, 1);
                photoUrlsInput.value=JSON.stringify(currentUrls);
                container.remove();
            }

            ;

            container.appendChild(img);
            container.appendChild(removeBtn);
            preview.appendChild(container);
        }

    );
}

// Save Changes Confirmation for Edit Post
const savePostChangesBtn = document.getElementById('savePostChangesBtn');
const saveChangesConfirmModal = document.getElementById('saveChangesConfirmModal');
const cancelSaveBtn = document.getElementById('cancelSaveBtn');
const confirmSaveBtn = document.getElementById('confirmSaveBtn');

if (savePostChangesBtn) {
    savePostChangesBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        console.log('Save Changes button clicked');
        
        // Basic form validation
        const title = document.getElementById('post_title').value;
        const description = document.getElementById('post_description').value;
        const status = document.getElementById('post_status').value;
        const breed = document.getElementById('post_breed').value;
        const location = document.getElementById('post_location').value;
        const contact = document.getElementById('post_contact').value;

        if (!title || !description || !status || !breed || !location || !contact) {
            alert('Please fill in all required fields');
            return;
        }

        if (saveChangesConfirmModal) {
            showModal(saveChangesConfirmModal);
        }
    });
}

if (cancelSaveBtn) {
    cancelSaveBtn.addEventListener('click', () => {
        if (saveChangesConfirmModal) hideModal(saveChangesConfirmModal);
    });
}

if (confirmSaveBtn) {
    confirmSaveBtn.addEventListener('click', () => {
        const form = document.getElementById('editPostForm');
        if (form) {
            // Add CSRF token if not already present
            if (!form.querySelector('input[name="_token"]')) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);
            }

            // Add method override for PUT request
            if (!form.querySelector('input[name="_method"]')) {
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                form.appendChild(methodInput);
            }

            // Submit the form
            form.submit();
        }
        
        // Hide modals
        const editModal = document.getElementById('editPostModal');
        if (editModal) hideModal(editModal);
        if (saveChangesConfirmModal) hideModal(saveChangesConfirmModal);
    });
}

// In-app share logic (copied from home.blade.php)
let postIdToShare = null;
const sharePostConfirmModal = document.getElementById('sharePostConfirmModal');
const cancelSharePostBtn = document.getElementById('cancelSharePostBtn');
const confirmSharePostBtn = document.getElementById('confirmSharePostBtn');

document.querySelectorAll('.share-btn').forEach(button => {
    button.addEventListener('click', function () {
        const postId = this.getAttribute('data-post-id');
        postIdToShare = postId;
        showModal(sharePostConfirmModal);
    });
});

if (confirmSharePostBtn) {
    confirmSharePostBtn.addEventListener('click', function () {
        if (!postIdToShare) return;
        fetch(`/posts/${postIdToShare}/share-in-app`, {
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
                // Optionally show notification
                // Update share count
                if (data.share_count !== undefined) {
                    const shareCountElement = document.getElementById(`share-count-${postIdToShare}`);
                    if (shareCountElement) {
                        shareCountElement.textContent = data.share_count;
                    }
                    window.dispatchEvent(new CustomEvent('shareCountUpdated', {
                        detail: { postId: postIdToShare, count: data.share_count }
                    }));
                }
            }
        })
        .finally(() => {
            hideModal(sharePostConfirmModal);
            postIdToShare = null;
        });
    });
}
if (cancelSharePostBtn) {
    cancelSharePostBtn.addEventListener('click', function () {
        hideModal(sharePostConfirmModal);
        postIdToShare = null;
    });
}

// Add these event listeners for reaction, comment, and share counts
document.addEventListener('DOMContentLoaded', function() {
    // Comment count update listener
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

    // Share count update listener
    window.addEventListener('shareCountUpdated', event => {
        const postId = event.detail ? event.detail.postId : undefined;
        const count = event.detail ? event.detail.count : undefined;
        
        const shareCountElement = document.getElementById(`share-count-${postId}`);
        
        if (shareCountElement) {
            shareCountElement.textContent = count;
        }
    });

    // Like button click handlers
    document.querySelectorAll('.like-btn').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const liked = this.dataset.liked === '1';
            const url = `/posts/${postId}/reactions`;
            const method = liked ? 'DELETE' : 'POST';
            const body = liked ? null : JSON.stringify({ reaction_type: 'like' });

            fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: body
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const countSpan = document.getElementById(`like-count-${postId}`);
                    if (countSpan) {
                        countSpan.textContent = data.total_reactions;
                    }
                    if (liked) {
                        this.classList.remove('reacted');
                        this.dataset.liked = '0';
                        this.querySelector('span').textContent = 'Like';
                    } else {
                        this.classList.add('reacted');
                        this.dataset.liked = '1';
                        this.querySelector('span').textContent = 'Liked';
                    }
                }
            });
        });
    });

    // Share button click handlers
    document.querySelectorAll('.share-btn').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            postIdToShare = postId;
            showModal(sharePostConfirmModal);
        });
    });

    // Confirm share button handler
    const confirmSharePostBtn = document.getElementById('confirmSharePostBtn');
    if (confirmSharePostBtn) {
        confirmSharePostBtn.addEventListener('click', function() {
            if (!postIdToShare) return;

            fetch(`/posts/${postIdToShare}/share-in-app`, {
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
                    if (data.share_count !== undefined) {
                        const shareCountElement = document.getElementById(`share-count-${postIdToShare}`);
                        if (shareCountElement) {
                            shareCountElement.textContent = data.share_count;
                        }
                        window.dispatchEvent(new CustomEvent('shareCountUpdated', {
                            detail: { postId: postIdToShare, count: data.share_count }
                        }));
                    }
                }
            })
            .finally(() => {
                hideModal(sharePostConfirmModal);
                postIdToShare = null;
            });
        });
    }
});

</script>
@endsection