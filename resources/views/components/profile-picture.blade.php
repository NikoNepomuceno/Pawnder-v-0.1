@props([
    'user',
    'size' => 'md',
    'class' => '',
    'showFallback' => true,
    'lazy' => true
])

@php
    $sizeClasses = [
        'xs' => 'w-6 h-6',
        'sm' => 'w-8 h-8',
        'md' => 'w-10 h-10',
        'lg' => 'w-16 h-16',
        'xl' => 'w-24 h-24',
        '2xl' => 'w-32 h-32'
    ];
    
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    
    $profilePictureUrl = $user?->profile_picture_url ?? asset('images/default-profile.png');
    $userInitials = $user?->initials ?? '?';
    $userName = $user?->name ?? 'Unknown User';
@endphp

<div class="profile-picture-container {{ $sizeClass }} {{ $class }}" 
     data-user-id="{{ $user?->id }}"
     title="{{ $userName }}">
    
    @if($user && $user->profile_picture)
        <img 
            src="{{ $profilePictureUrl }}" 
            alt="{{ $userName }}'s profile picture"
            class="profile-picture-img {{ $sizeClass }} rounded-full object-cover border border-gray-200"
            @if($lazy) loading="lazy" @endif
            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
        >
    @endif
    
    @if($showFallback)
        <div class="profile-picture-fallback {{ $sizeClass }} rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold {{ $user && $user->profile_picture ? 'hidden' : 'flex' }}"
             style="font-size: {{ $size === 'xs' ? '0.5rem' : ($size === 'sm' ? '0.625rem' : ($size === 'md' ? '0.75rem' : ($size === 'lg' ? '1rem' : ($size === 'xl' ? '1.25rem' : '1.5rem')))) }};">
            {{ $userInitials }}
        </div>
    @endif
</div>

<style>
.profile-picture-container {
    position: relative;
    display: inline-block;
    flex-shrink: 0;
}

.profile-picture-img {
    transition: opacity 0.2s ease-in-out;
}

.profile-picture-img:hover {
    opacity: 0.9;
}

.profile-picture-fallback {
    transition: all 0.2s ease-in-out;
}

.profile-picture-fallback:hover {
    transform: scale(1.05);
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .profile-picture-container.w-24 {
        width: 1.5rem;
        height: 1.5rem;
    }
    
    .profile-picture-container.w-32 {
        width: 2rem;
        height: 2rem;
    }
}
</style>
