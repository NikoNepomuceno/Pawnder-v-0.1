@extends('layouts.app')

@push('head')
    <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
    <style>
        .notifications-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .clear-read-btn {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 8px 16px;
            background-color: #dc3545;
            border: 1px solid #dc3545;
            border-radius: 4px;
            color: white;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            outline: none;
        }
        .clear-read-btn:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
        .clear-read-btn:focus, .clear-read-btn:active {
            outline: none;
            box-shadow: none;
            border: 1px solid #bd2130;
        }
        .clear-read-btn i {
            font-size: 16px;
        }
        .clear-read-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Clear All Confirmation Modal */
        #clearAllModal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        #clearAllModal[style*="display: flex"] {
            opacity: 1;
            visibility: visible;
        }

        #clearAllModal .modal-content {
            margin: 0;
            position: relative;
            padding: 32px 32px 24px 32px;
            max-width: 400px;
            width: 90vw;
            box-sizing: border-box;
            background: #ffffff;
            border: 1px solid rgba(220, 53, 69, 0.2);
            box-shadow: 0 8px 32px rgba(220, 53, 69, 0.18);
            border-radius: 18px;
            transform: scale(0.95);
            opacity: 0;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        #clearAllModal[style*="display: flex"] .modal-content {
            transform: scale(1);
            opacity: 1;
        }

        #clearAllModal .modal-content h3 {
            color: #dc3545;
            font-weight: 700;
            margin-bottom: 8px;
        }

        #clearAllModal .modal-content p {
            color: #666666;
            margin-bottom: 24px;
        }

        #clearAllModal .modal-content .cancel-btn {
            background-color: #f0f2f5;
            color: #1a3d2b;
        }

        #clearAllModal .modal-content .cancel-btn:hover {
            background-color: #e4e6e9;
        }

        /* Only for the modal confirm button */
        #clearAllModal .confirm-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 10px 28px;
            background-color: #dc3545;
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
            box-shadow: 0 2px 8px rgba(220,53,69,0.08);
            outline: none;
        }
        #clearAllModal .confirm-btn:hover {
            background-color: #b91c1c;
            transform: translateY(-2px) scale(1.03);
        }
        #clearAllModal .confirm-btn:focus {
            outline: none;
            box-shadow: 0 0 0 2px #f8d7da;
        }
        #clearAllModal .confirm-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* Dark mode for modal and confirm button */
        body.dark-mode #clearAllModal .modal-content {
            background: #23272b;
            border: 1px solid rgba(220, 53, 69, 0.3);
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }
        body.dark-mode #clearAllModal .modal-content h3,
        body.dark-mode #clearAllModal .modal-content p {
            color: #fff;
        }
        body.dark-mode #clearAllModal .confirm-btn {
            background-color: #dc3545;
            color: #fff;
            border: none;
        }
        body.dark-mode #clearAllModal .confirm-btn:hover {
            background-color: #b91c1c;
        }
        body.dark-mode #clearAllModal .confirm-btn:focus {
            box-shadow: 0 0 0 2px #7f1d1d;
        }
        body.dark-mode #clearAllModal .cancel-btn {
            background-color: #2d333b;
            color: #e0e0e0;
        }
        body.dark-mode #clearAllModal .cancel-btn:hover {
            background-color: #373e47;
            color: #fff;
        }
    </style>
@endpush

@section('content')
<div class="notifications-container">
    <div class="notifications-header">
        <h2>Notifications</h2>
        <div class="notifications-actions">
            <button type="button" class="clear-read-btn" id="clearReadBtn" title="Clear all notifications">
                <i class='bx bx-trash'></i> Clear All
            </button>
            {{-- <button class="mark-all-read-btn" id="markAllReadBtn">
                <i class='bx bx-check-double'></i> Mark all as read
            </button> --}}
        </div>
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

<!-- Clear All Confirmation Modal -->
<div id="clearAllModal" class="modal">
    <div class="modal-content">
        <h3>Clear All Notifications</h3>
        <p>Are you sure you want to clear all notifications? This action cannot be undone.</p>
        <div class="form-actions" style="display: flex; gap: 24px; justify-content: center;">
            <button type="button" class="cancel-btn" id="cancelClearBtn">Cancel</button>
            <button type="button" class="confirm-btn" id="confirmClearBtn">Clear All</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    // Clear all notifications
    const clearReadBtn = document.getElementById('clearReadBtn');
    const clearAllModal = document.getElementById('clearAllModal');
    const cancelClearBtn = document.getElementById('cancelClearBtn');
    const confirmClearBtn = document.getElementById('confirmClearBtn');
    const notificationsList = document.getElementById('notificationsList');

    function hasNotifications() {
        return notificationsList && notificationsList.querySelectorAll('.notification-item').length > 0;
    }

    if (clearReadBtn) {
        clearReadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (!hasNotifications()) {
                // Use global alert system
                if (typeof showNotification === 'function') {
                    showNotification('No pending notifications to clear.', 'error');
                }
                return;
            }
            clearAllModal.style.display = 'flex';
        });

        if (cancelClearBtn) {
            cancelClearBtn.addEventListener('click', function() {
                clearAllModal.style.display = 'none';
            });
        }

        if (confirmClearBtn) {
            confirmClearBtn.addEventListener('click', async function() {
                try {
                    clearReadBtn.disabled = true;
                    confirmClearBtn.disabled = true;
                    const token = document.querySelector('meta[name="csrf-token"]');
                    const response = await fetch('/notifications/clear-read', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token ? token.content : '',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        credentials: 'same-origin'
                    });
                    const data = await response.json();
                    if (data.success) {
                        notificationsList.innerHTML = `
                            <div class="no-notifications">
                                <i class='bx bx-bell-off'></i>
                                <p>No notifications yet</p>
                            </div>
                        `;
                        if (typeof showNotification === 'function') {
                            showNotification('All notifications cleared successfully', 'success');
                        }
                    }
                } catch (error) {
                    if (typeof showNotification === 'function') {
                        showNotification('Failed to clear notifications: ' + error.message, 'error');
                    }
                } finally {
                    clearReadBtn.disabled = false;
                    confirmClearBtn.disabled = false;
                    clearAllModal.style.display = 'none';
                }
            });
        }

        window.addEventListener('click', function(event) {
            if (event.target === clearAllModal) {
                clearAllModal.style.display = 'none';
            }
        });
    }
})();
</script>
@endpush
@endsection 