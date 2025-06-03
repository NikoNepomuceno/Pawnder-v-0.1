@extends('layouts.admin')

@section('content')
    <div id="pageFade" class="opacity-0 transition-opacity duration-500">
        <x-admin-side-bar />
        <!-- Main Content Area -->
        <div class="lg:ml-64 min-h-screen bg-gray-50">
            <div class="lg:p-8 p-4">
                <!-- Header with mobile spacing -->
                <div class="flex items-center justify-between mb-8 pt-16 lg:pt-0">
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-800">Archived Reports</h1>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-col space-y-3">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-archive text-gray-500 text-xl"></i>
                                <h2 class="text-xl font-semibold text-gray-800">Archived Reports</h2>
                            </div>
                            <span class="text-sm text-gray-500">Total: {{ $archivedReports->total() }}</span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Report ID
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Report Created
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Archived At
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Archived By
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($archivedReports as $report)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            #{{ $report->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $report->created_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $report->reviewed_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Admin
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button onclick="showUnarchiveModal({{ $report->id }})"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                                <i class="fas fa-undo mr-2"></i>
                                                Unarchive
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-16 text-center">
                                            <div class="flex flex-col items-center justify-center space-y-4">
                                                <div
                                                    class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-archive text-gray-500 text-2xl"></i>
                                                </div>
                                                <div class="space-y-2">
                                                    <h3 class="text-lg font-medium text-gray-900">No Archived Reports</h3>
                                                    <p class="text-sm text-gray-500 max-w-sm">
                                                        Archived reports will appear here when admins dismiss reports without
                                                        taking
                                                        action.
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($archivedReports->hasPages())
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                            {{ $archivedReports->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Unarchive Confirmation Modal -->
    <div id="unarchiveReportConfirmModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/25 backdrop-blur-sm">
        <div
            class="relative bg-white rounded-2xl shadow-lg min-w-[320px] max-w-[500px] border border-gray-200 overflow-hidden">
            <button class="absolute right-4 top-4 text-2xl text-gray-500 hover:text-gray-700 transition-colors z-10"
                id="closeUnarchiveModal" tabindex="0" aria-label="Close">&times;</button>

            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b border-gray-200">
                <div class="flex items-center gap-4">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-undo text-white text-xl"></i>
                    </div>
                    <div class="text-left">
                        <h3 class="text-xl font-bold text-gray-800">Confirm Unarchive</h3>
                        <p class="text-sm text-blue-600 font-medium">Restore report to pending status</p>
                    </div>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <div class="mb-6">
                    <p class="text-gray-700 text-base leading-relaxed mb-4">
                        Are you sure you want to unarchive this report? This action will restore the report to pending
                        status for further review.
                    </p>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-blue-800 mb-3 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        This will:
                    </h4>
                    <ul class="space-y-2 text-sm text-blue-700">
                        <li class="flex items-center">
                            <i class="fas fa-arrow-left text-blue-500 mr-3 w-4"></i>
                            Move the report back to pending reports list
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-clock text-blue-500 mr-3 w-4"></i>
                            Reset the review status and timestamps
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-eye text-blue-500 mr-3 w-4"></i>
                            Make the report available for admin review again
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-user-shield text-blue-500 mr-3 w-4"></i>
                            Clear previous admin decisions and notes
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                <button
                    class="px-5 py-2.5 rounded-lg bg-white border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 flex items-center gap-2"
                    id="cancelUnarchive">
                    <i class="fas fa-times text-sm"></i>
                    Cancel
                </button>
                <button
                    class="px-5 py-2.5 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold hover:from-blue-600 hover:to-indigo-700 transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2"
                    id="confirmUnarchive">
                    <i class="fas fa-undo text-sm"></i>
                    Unarchive Report
                </button>
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
        let selectedReportId = null;

        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                document.getElementById('pageFade').classList.add('opacity-100');
            }, 10);

            // Logout modal logic
            var openBtn = document.getElementById('adminOpenLogoutModal');
            var logoutModal = document.getElementById('adminLogoutConfirmModal');
            var cancelBtn = document.getElementById('adminCancelLogout');
            var confirmBtn = document.getElementById('adminConfirmLogout');
            var form = document.getElementById('adminLogoutForm');

            if (openBtn && logoutModal) {
                openBtn.onclick = function () {
                    logoutModal.classList.remove('hidden');
                    logoutModal.classList.add('flex');
                };
            }

            if (cancelBtn && logoutModal) {
                cancelBtn.onclick = function () {
                    logoutModal.classList.add('hidden');
                    logoutModal.classList.remove('flex');
                };
            }

            if (confirmBtn && form) {
                confirmBtn.onclick = function () {
                    form.submit();
                };
            }

            // Unarchive modal elements
            const unarchiveModal = document.getElementById('unarchiveReportConfirmModal');
            const closeUnarchiveModal = document.getElementById('closeUnarchiveModal');
            const cancelUnarchive = document.getElementById('cancelUnarchive');
            const confirmUnarchive = document.getElementById('confirmUnarchive');

            // Show unarchive modal
            window.showUnarchiveModal = function (reportId) {
                selectedReportId = reportId;
                unarchiveModal.classList.remove('hidden');
                unarchiveModal.classList.add('flex');
            };

            // Hide unarchive modal
            function hideUnarchiveModal() {
                unarchiveModal.classList.add('hidden');
                unarchiveModal.classList.remove('flex');
                selectedReportId = null;
            }

            // Modal event listeners
            if (closeUnarchiveModal) {
                closeUnarchiveModal.onclick = hideUnarchiveModal;
            }

            if (cancelUnarchive) {
                cancelUnarchive.onclick = hideUnarchiveModal;
            }

            if (confirmUnarchive) {
                confirmUnarchive.onclick = function () {
                    if (selectedReportId) {
                        unarchiveReport(selectedReportId);
                    }
                };
            }

            // Close modal when clicking outside
            window.addEventListener('click', function (event) {
                if (event.target === unarchiveModal) {
                    hideUnarchiveModal();
                }
                if (event.target === logoutModal) {
                    logoutModal.classList.add('hidden');
                    logoutModal.classList.remove('flex');
                }
            });

            // Unarchive report function
            function unarchiveReport(reportId) {
                fetch(`/admin/reports/${reportId}/unarchive`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        hideUnarchiveModal();
                        if (data.success) {
                            showNotification(data.message, 'success');
                            // Reload the page after a short delay to show the updated list
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            showNotification(data.message || 'Failed to unarchive report', 'error');
                        }
                    })
                    .catch(error => {
                        hideUnarchiveModal();
                        showNotification('An error occurred while unarchiving the report', 'error');
                        console.error('Error:', error);
                    });
            }

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
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    <style>
        @keyframes slideInFromTop {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideOutToTop {
            from {
                transform: translateY(0);
                opacity: 1;
            }

            to {
                transform: translateY(-100%);
                opacity: 0;
            }
        }

        .animate-slideInFromTop {
            animation: slideInFromTop 0.4s ease-out;
        }

        .animate-slideOutToTop {
            animation: slideOutToTop 0.3s ease-out forwards;
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
@endpush