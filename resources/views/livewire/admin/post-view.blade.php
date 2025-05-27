<div>
    @if($post)
        <div class="post-card" data-post-id="{{ $post->id }}">
            <div class="post-header">
                <div class="post-user-info">
                    <img src="{{ $post->user->profile_picture ?? asset('images/default-profile.png') }}" alt="Profile" class="post-avatar">
                    <div>
                        <h4 class="post-author">
                            {{ $post->user->username }}
                            <span class="post-name" style="font-weight:normal; color:#888;">{{ $post->user->name }}</span>
                        </h4>
                        <span class="post-date">{{ $post->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
            <div class="post-details" style="padding: 10px 15px;">
                <span class="post-status {{ $post->status }}">{{ ucfirst($post->status) }}</span>
                <span class="post-breed">Breed: {{ $post->breed }}</span>
                <span class="post-location">Location: {{ $post->location }}</span>
                <span class="post-contact">Contact Number: {{ $post->mobile_number }} | Email: {{ $post->email }}</span>
            </div>
            <div class="post-content">
                <h3>{{ $post->title }}</h3>
                <p class="post-description">{{ $post->description }}</p>
            </div>
            @if(count($post->photo_urls) > 0)
                <div class="post-images">
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
                </div>
            @endif
        </div>
        <!-- Lightbox Modal (scoped to this component) -->
        <div class="lightbox-modal" id="lightboxModal-{{ $post->id }}" tabindex="-1" aria-modal="true" role="dialog" style="display:none;">
            <div class="lightbox-content">
                <span class="lightbox-close" tabindex="0" aria-label="Close">&times;</span>
                <img class="lightbox-image" src="" alt="Full size image">
                <div class="lightbox-prev" tabindex="0" aria-label="Previous image">&lt;</div>
                <div class="lightbox-next" tabindex="0" aria-label="Next image">&gt;</div>
                <div class="lightbox-counter"></div>
            </div>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const postId = @json($post->id);
            const lightbox = document.getElementById('lightboxModal-' + postId);
            const lightboxImg = lightbox.querySelector('.lightbox-image');
            const lightboxClose = lightbox.querySelector('.lightbox-close');
            const lightboxPrev = lightbox.querySelector('.lightbox-prev');
            const lightboxNext = lightbox.querySelector('.lightbox-next');
            const lightboxCounter = lightbox.querySelector('.lightbox-counter');
            let currentImageIndex = 0;
            let currentImages = [];

            // Open lightbox when clicking on an image
            document.querySelectorAll(`[data-post-id='${postId}'] .grid-item`).forEach(function(gridItem) {
                gridItem.addEventListener('click', function(e) {
                    const images = Array.from(document.querySelectorAll(`[data-post-id='${postId}'] img`));
                    const index = images.indexOf(gridItem.querySelector('img'));
                    currentImages = images;
                    currentImageIndex = index >= 0 ? index : parseInt(gridItem.dataset.index);
                    showLightbox();
                });
            });

            function showLightbox() {
                if (!currentImages.length) return;
                lightboxImg.src = currentImages[currentImageIndex].src;
                lightboxCounter.textContent = `${currentImageIndex + 1} / ${currentImages.length}`;
                lightbox.style.display = 'flex';
                lightbox.classList.add('show');
                lightbox.focus();
            }

            function hideLightbox() {
                lightbox.style.display = 'none';
                lightbox.classList.remove('show');
            }

            function showNextImage() {
                if (!currentImages.length) return;
                currentImageIndex = (currentImageIndex + 1) % currentImages.length;
                showLightbox();
            }

            function showPrevImage() {
                if (!currentImages.length) return;
                currentImageIndex = (currentImageIndex - 1 + currentImages.length) % currentImages.length;
                showLightbox();
            }

            lightboxClose.addEventListener('click', hideLightbox);
            lightboxNext.addEventListener('click', showNextImage);
            lightboxPrev.addEventListener('click', showPrevImage);

            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (!lightbox.classList.contains('show')) return;
                switch(e.key) {
                    case 'Escape':
                        hideLightbox();
                        break;
                    case 'ArrowRight':
                        showNextImage();
                        break;
                    case 'ArrowLeft':
                        showPrevImage();
                        break;
                }
            });

            // Accessibility: allow Enter/Space on controls
            [lightboxClose, lightboxPrev, lightboxNext].forEach(el => {
                el.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        el.click();
                    }
                });
            });

            // Close lightbox when clicking outside the image
            lightbox.addEventListener('click', function(e) {
                if (e.target === lightbox) {
                    hideLightbox();
                }
            });
        });
        </script>
        <style>
        .post-card {
            background: white;
            border-radius: 0.8rem;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        .post-header {
            padding: 1.25rem;
            border-bottom: 1px solid #e5e7eb;
        }
        .post-user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .post-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
        }
        .post-author {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: #1b4332;
        }
        .post-name {
            font-weight: normal;
            color: #6b7280;
            margin-left: 0.5rem;
        }
        .post-date {
            font-size: 0.875rem;
            color: #6b7280;
        }
        .post-details {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            padding: 1rem 1.25rem;
            background-color: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }
        .post-status {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .post-status.pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .post-status.approved {
            background-color: #dcfce7;
            color: #166534;
        }
        .post-status.archived {
            background-color: #f3f4f6;
            color: #374151;
        }
        .post-breed,
        .post-location,
        .post-contact {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            background-color: #f3f4f6;
            color: #374151;
        }
        .post-breed i,
        .post-location i,
        .post-contact i {
            margin-right: 0.375rem;
            color: #6b7280;
        }
        .post-content {
            padding: 1.25rem;
        }
        .post-content h3 {
            margin: 0 0 0.75rem;
            font-size: 1.25rem;
            font-weight: 600;
            color: #1b4332;
        }
        .post-description {
            margin: 0;
            color: #4b5563;
            line-height: 1.6;
        }
        .post-images {
            padding: 0 1.25rem 1.25rem;
        }
        .image-grid {
            display: grid;
            gap: 0.5rem;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .image-grid.single-image {
            grid-template-columns: 1fr;
        }
        .image-grid.two-images {
            grid-template-columns: repeat(2, 1fr);
        }
        .image-grid.three-images {
            grid-template-columns: repeat(3, 1fr);
        }
        .grid-item {
            position: relative;
            aspect-ratio: 1;
            cursor: pointer;
            overflow: hidden;
            border-radius: 0.5rem;
        }
        .grid-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.2s;
        }
        .grid-item:hover img {
            transform: scale(1.05);
        }
        .more-indicator {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 600;
        }
        .deleted-post-message {
            text-align: center;
            padding: 2rem;
            background: #f3f4f6;
            border-radius: 0.8rem;
            color: #6b7280;
        }
        .deleted-post-message i {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #9ca3af;
        }
        .deleted-post-message h3 {
            margin: 0 0 0.5rem;
            font-size: 1.25rem;
            font-weight: 600;
            color: #374151;
        }
        .deleted-post-message p {
            margin: 0;
            font-size: 0.875rem;
        }
        .lightbox-modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(30, 30, 30, 0.18);
            backdrop-filter: blur(6px);
            align-items: center;
            justify-content: center;
        }
        .lightbox-modal.show {
            display: flex;
        }
        .lightbox-content {
            position: relative;
            max-width: 90vw;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .lightbox-image {
            max-width: 90vw;
            max-height: 80vh;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }
        .lightbox-close {
            position: absolute;
            top: 10px;
            right: 20px;
            color: #222;
            font-size: 2rem;
            font-weight: bold;
            cursor: pointer;
            z-index: 10;
            background: rgba(255,255,255,0.7);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        .lightbox-close:hover {
            background: rgba(255,255,255,0.9);
        }
        .lightbox-prev,
        .lightbox-next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #222;
            font-size: 2rem;
            font-weight: bold;
            cursor: pointer;
            z-index: 10;
            background: rgba(255,255,255,0.7);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        .lightbox-prev:hover,
        .lightbox-next:hover {
            background: rgba(255,255,255,0.9);
        }
        .lightbox-prev {
            left: 10px;
        }
        .lightbox-next {
            right: 10px;
        }
        .lightbox-counter {
            margin-top: 12px;
            color: #222;
            font-size: 1rem;
            background: rgba(255,255,255,0.7);
            padding: 4px 12px;
            border-radius: 20px;
        }
        </style>
    @else
        <div class="deleted-post-message">
            <i class="fas fa-trash-alt"></i>
            <h3>This post has been deleted</h3>
            <p>The original post has been deleted by the author.</p>
        </div>
    @endif
</div>
