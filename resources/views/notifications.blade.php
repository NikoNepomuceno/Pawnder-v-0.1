@extends('layouts.app')

@push('head')
    <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
@endpush

@section('content')
<div class="notifications-container">
    <div class="notifications-header">
        <h2>Notifications</h2>
        {{-- <div class="notifications-actions">
            <button class="mark-all-read-btn" id="markAllReadBtn">
                <i class='bx bx-check-double'></i> Mark all as read
            </button>
        </div> --}}
    </div>

    <div class="notifications-list" id="notificationsList">
        @forelse($notifications as $notification)
            <div class="notification-item {{ $notification->read_at ? 'read' : 'unread' }}" 
                 data-notification-id="{{ $notification->id }}">
                <div class="notification-icon">
                    @switch($notification->type)
                        @case('App\Notifications\PostLiked')
                            <i class='bx bx-heart'></i>
                            @break
                        @case('App\Notifications\NewComment')
                            <i class='bx bx-comment'></i>
                            @break
                        @case('App\Notifications\PostShared')
                            <i class='bx bx-share'></i>
                            @break
                        @default
                            <i class='bx bx-bell'></i>
                    @endswitch
                </div>
                <div class="notification-content">
                    <p class="notification-text">{!! $notification->data['message'] !!}</p>
                    <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                </div>
                <button class="mark-read-btn" title="Mark as read">
                    <i class='bx bx-check'></i>
                </button>
            </div>
        @empty
            <div class="no-notifications">
                <i class='bx bx-bell-off'></i>
                <p>No notifications yet</p>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mark single notification as read
    document.querySelectorAll('.mark-read-btn').forEach(button => {
        button.addEventListener('click', function() {
            const notificationItem = this.closest('.notification-item');
            const notificationId = notificationItem.dataset.notificationId;
            
            fetch(`/notifications/${notificationId}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    notificationItem.classList.remove('unread');
                    notificationItem.classList.add('read');
                    updateNotificationCount();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Failed to mark notification as read', 'error');
            });
        });
    });

    // Mark all notifications as read
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function() {
            fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelectorAll('.notification-item.unread').forEach(item => {
                        item.classList.remove('unread');
                        item.classList.add('read');
                    });
                    updateNotificationCount();
                    showNotification('All notifications marked as read', 'success');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Failed to mark all notifications as read', 'error');
            });
        });
    }

    // Update notification count in the navbar
    function updateNotificationCount() {
        const unreadCount = document.querySelectorAll('.notification-item.unread').length;
        const notificationBadge = document.querySelector('.notification-badge');
        
        if (notificationBadge) {
            if (unreadCount > 0) {
                notificationBadge.textContent = unreadCount;
                notificationBadge.style.display = 'flex';
            } else {
                notificationBadge.style.display = 'none';
            }
        }
    }
});
</script>
@endpush
@endsection 