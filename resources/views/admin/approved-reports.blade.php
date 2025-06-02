@extends('layouts.admin')

@section('content')
    <div id="pageFade" class="opacity-0 transition-opacity duration-500">
        <x-admin-side-bar />
        <!-- Main Content Area -->
        <div class="lg:ml-64 min-h-screen bg-gray-50">
            <div class="lg:p-8 p-4">
                <!-- Header with mobile spacing -->
                <div class="flex items-center justify-between mb-8 pt-16 lg:pt-0">
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-800">Approved Reports</h1>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-check-circle text-green-500 text-xl"></i>
                                <h2 class="text-xl font-semibold text-gray-800">Approved Reports</h2>
                            </div>
                            <span class="text-sm text-gray-500">Total: {{ $approvedReports->total() }}</span>
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
                                        Approved At
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Approved By
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($approvedReports as $report)
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
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Admin
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-16 text-center">
                                            <div class="flex flex-col items-center justify-center space-y-4">
                                                <div
                                                    class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                                                </div>
                                                <div class="space-y-2">
                                                    <h3 class="text-lg font-medium text-gray-900">No Approved Reports</h3>
                                                    <p class="text-sm text-gray-500 max-w-sm">
                                                        Approved reports will appear here when admins resolve violations and
                                                        take
                                                        action on posts.
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($approvedReports->hasPages())
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                            {{ $approvedReports->links() }}
                        </div>
                    @endif
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                document.getElementById('pageFade').classList.add('opacity-100');
            }, 10);

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
        });
    </script>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    <style>
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