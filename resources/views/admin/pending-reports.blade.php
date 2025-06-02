@extends('layouts.admin')

@section('content')
    <div id="pageFade" class="opacity-0 transition-opacity duration-500">
        <x-admin-side-bar />
        <!-- Main Content Area -->
        <div class="lg:ml-64 min-h-screen bg-gray-50">
            <div class="lg:p-8 p-4">
                <!-- Header with mobile spacing -->
                <div class="flex items-center justify-between mb-8 pt-16 lg:pt-0">
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-800">Pending Reports</h1>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-500 hidden sm:block">Review and manage pending reports</span>
                    </div>
                </div>

                <!-- Pending Reports Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-clock text-amber-500 text-xl"></i>
                            <h2 class="text-xl font-semibold text-gray-800">Reports Awaiting Review</h2>
                        </div>
                        <p class="text-gray-600 mt-2">Review reported posts and take appropriate action</p>
                    </div>
                    <div class="p-6">
                        <livewire:admin.reports-table status="pending" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div id="adminLogoutConfirmModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/25 backdrop-blur-sm p-4">
        <div
            class="relative bg-white rounded-2xl p-8 shadow-lg w-full max-w-sm text-center flex flex-col items-center border border-gray-200 mx-auto">
            <h3 class="text-xl font-semibold mb-4 text-gray-800">Confirm Logout</h3>
            <p class="mb-6 text-gray-600">Are you sure you want to log out?</p>
            <div class="flex justify-center gap-4 w-full">
                <button
                    class="px-6 py-2.5 rounded-lg bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition-colors flex-1"
                    id="adminCancelLogout">Cancel</button>
                <button
                    class="px-6 py-2.5 rounded-lg bg-red-500 text-white font-semibold hover:bg-red-600 transition-colors flex-1"
                    id="adminConfirmLogout">Logout</button>
            </div>
        </div>
    </div>

    <!-- Notification Container -->
    <div id="notificationContainer" class="fixed top-0 left-0 right-0 z-50 flex flex-col items-center pt-2"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Page fade in effect
            setTimeout(() => {
                document.getElementById('pageFade').classList.remove('opacity-0');
                document.getElementById('pageFade').classList.add('opacity-100');
            }, 100);

            // Logout modal logic
            var openBtn = document.getElementById('adminOpenLogoutModal');
            var modal = document.getElementById('adminLogoutConfirmModal');
            var cancelBtn = document.getElementById('adminCancelLogout');
            var confirmBtn = document.getElementById('adminConfirmLogout');
            var form = document.getElementById('adminLogoutForm');

            if (openBtn && modal) {
                openBtn.onclick = function () {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                };
            }

            if (cancelBtn && modal) {
                cancelBtn.onclick = function () {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                };
            }

            if (confirmBtn && form) {
                confirmBtn.onclick = function () {
                    form.submit();
                };
            }

            // Close modal when clicking outside
            window.addEventListener('click', function (event) {
                if (event.target === modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            });

            // Notification System
            window.showNotification = function (message, type = 'success') {
                const container = document.getElementById('notificationContainer');
                const notification = document.createElement('div');
                notification.className = `p-4 mb-2 rounded-lg flex items-center justify-between min-w-[300px] max-w-[500px] shadow-lg animate-slideInFromTop ${type === 'success'
                    ? 'bg-green-50 text-green-800 border-l-4 border-green-500'
                    : 'bg-red-50 text-red-800 border-l-4 border-red-500'
                    }`;

                const messageSpan = document.createElement('span');
                messageSpan.textContent = message;

                const closeBtn = document.createElement('button');
                closeBtn.className = 'text-current opacity-70 hover:opacity-100 px-2 text-xl';
                closeBtn.innerHTML = '&times;';
                closeBtn.onclick = function () {
                    notification.classList.add('animate-slideOutToTop');
                    setTimeout(() => notification.remove(), 300);
                };

                notification.appendChild(messageSpan);
                notification.appendChild(closeBtn);
                container.appendChild(notification);

                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.classList.add('animate-slideOutToTop');
                        setTimeout(() => notification.remove(), 300);
                    }
                }, 5000);
            };

            // Listen for Livewire events
            Livewire.on('showNotification', (message, type) => {
                showNotification(message, type);
            });

            // Handle session flash messages
            @if(session('success'))
                window.showNotification('{{ session('success') }}', 'success');
            @endif

            @if(session('error'))
                window.showNotification('{{ session('error') }}', 'error');
            @elseif($errors->any())
                window.showNotification('{{ $errors->first() }}', 'error');
            @endif
                        });
    </script>

    <style>
        @keyframes slideInFromTop {
            from {
                opacity: 0;
                transform: translateY(-100%);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideOutToTop {
            from {
                opacity: 1;
                transform: translateY(0);
            }

            to {
                opacity: 0;
                transform: translateY(-100%);
            }
        }

        .animate-slideInFromTop {
            animation: slideInFromTop 0.5s ease-out forwards;
        }

        .animate-slideOutToTop {
            animation: slideOutToTop 0.3s ease-in forwards;
        }

        /* Ensure perfect modal centering */
        #adminLogoutConfirmModal {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        #adminLogoutConfirmModal.hidden {
            display: none !important;
        }
    </style>
@endsection