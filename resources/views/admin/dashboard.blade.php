@extends('layouts.admin')

@section('content')
<div id="pageFade" class="flex min-h-screen opacity-0 transition-opacity duration-500">
    <x-admin-side-bar />
    <div class="flex-1 p-8 bg-gray-50">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-500">Welcome back, Admin</span>
            </div>
        </div>

        <div class="mb-8">
            <livewire:admin.dashboard-stats />
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-clock text-blue-500 text-xl"></i>
                    <h2 class="text-xl font-semibold text-gray-800">Pending Reports</h2>
                </div>
            </div>
            <div class="p-6">
                <livewire:admin.reports-table status="pending" />
            </div>
        </div>
    </div>
</div>

<!-- Archive Report Confirmation Modal -->
<div id="archiveReportConfirmModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/25 backdrop-blur-sm">
    <div class="relative bg-white rounded-2xl p-8 shadow-lg min-w-[320px] max-w-[90vw] text-center flex flex-col items-center border border-gray-200">
        <button class="absolute right-4 top-4 text-2xl text-gray-500 hover:text-gray-700 transition-colors" id="closeArchiveModal" tabindex="0" aria-label="Close">&times;</button>
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Confirm Archive</h3>
        <p class="mb-6 text-gray-600">Are you sure you want to archive this report?</p>
        <div class="flex justify-center gap-4">
            <button class="px-6 py-2.5 rounded-lg bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition-colors" id="cancelArchive">Cancel</button>
            <button class="px-6 py-2.5 rounded-lg bg-blue-500 text-white font-semibold hover:bg-blue-600 transition-colors" id="confirmArchive">Archive</button>
        </div>
    </div>
</div>

<!-- Approve Report Confirmation Modal -->
<div id="approveReportConfirmModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/25 backdrop-blur-sm">
    <div class="relative bg-white rounded-2xl p-8 shadow-lg min-w-[320px] max-w-[90vw] text-center flex flex-col items-center border border-gray-200">
        <button class="absolute right-4 top-4 text-2xl text-gray-500 hover:text-gray-700 transition-colors" id="closeApproveModal" tabindex="0" aria-label="Close">&times;</button>
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Confirm Approval</h3>
        <p class="mb-6 text-gray-600">Are you sure you want to approve this report and take down the post?</p>
        <div class="flex justify-center gap-4">
            <button class="px-6 py-2.5 rounded-lg bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition-colors" id="cancelApprove">Cancel</button>
            <button class="px-6 py-2.5 rounded-lg bg-green-500 text-white font-semibold hover:bg-green-600 transition-colors" id="confirmApprove">Approve</button>
        </div>
    </div>
</div>

<!-- Logout Confirmation Modal -->
<div id="adminLogoutConfirmModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/25 backdrop-blur-sm">
    <div class="relative bg-white rounded-2xl p-8 shadow-lg min-w-[320px] max-w-[90vw] text-center flex flex-col items-center border border-gray-200">
        <button class="absolute right-4 top-4 text-2xl text-gray-500 hover:text-gray-700 transition-colors" id="adminCloseLogoutModal" tabindex="0" aria-label="Close">&times;</button>
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Confirm Logout</h3>
        <p class="mb-6 text-gray-600">Are you sure you want to log out?</p>
        <div class="flex justify-center gap-4">
            <button class="px-6 py-2.5 rounded-lg bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition-colors" id="adminCancelLogout">Cancel</button>
            <button class="px-6 py-2.5 rounded-lg bg-red-500 text-white font-semibold hover:bg-red-600 transition-colors" id="adminConfirmLogout">Logout</button>
        </div>
    </div>
</div>

<!-- Notification Container -->
<div id="notificationContainer" class="fixed top-4 right-4 z-50"></div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hamburger menu logic
    var hamburger = document.getElementById('adminHamburgerMenu');
    var dropdown = document.getElementById('adminDropdownMenu');
    var openBtn = document.getElementById('adminOpenLogoutModal');
    var modal = document.getElementById('adminLogoutConfirmModal');
    var closeBtn = document.getElementById('adminCloseLogoutModal');
    var cancelBtn = document.getElementById('adminCancelLogout');
    var confirmBtn = document.getElementById('adminConfirmLogout');
    var form = document.getElementById('adminLogoutForm');

    if (hamburger && dropdown) {
        hamburger.onclick = function(e) {
            e.stopPropagation();
            var expanded = hamburger.getAttribute('aria-expanded') === 'true';
            hamburger.setAttribute('aria-expanded', !expanded);
            dropdown.classList.toggle('show');
            if (!expanded) dropdown.focus();
        };

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target) && !hamburger.contains(e.target)) {
                dropdown.classList.remove('show');
                hamburger.setAttribute('aria-expanded', 'false');
            }
        });

        // Keyboard accessibility
        dropdown.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                dropdown.classList.remove('show');
                hamburger.setAttribute('aria-expanded', 'false');
                hamburger.focus();
            }
        });
    }

    // Logout modal logic
    if (openBtn && modal) {
        openBtn.onclick = function() {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        };
    }

    if (closeBtn && modal) {
        closeBtn.onclick = function() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        };
    }

    if (cancelBtn && modal) {
        cancelBtn.onclick = function() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        };
    }

    if (confirmBtn && form) {
        confirmBtn.onclick = function() {
            form.submit();
        };
    }

    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    });

    // Archive Report Modal Logic
    const archiveModal = document.getElementById('archiveReportConfirmModal');
    const closeArchiveModal = document.getElementById('closeArchiveModal');
    const cancelArchive = document.getElementById('cancelArchive');
    const confirmArchive = document.getElementById('confirmArchive');
    
    function showArchiveModal() {
        archiveModal.classList.remove('hidden');
        archiveModal.classList.add('flex');
    }
    
    function hideArchiveModal() {
        archiveModal.classList.add('hidden');
        archiveModal.classList.remove('flex');
    }
    
    if (closeArchiveModal) {
        closeArchiveModal.onclick = hideArchiveModal;
    }
    
    if (cancelArchive) {
        cancelArchive.onclick = hideArchiveModal;
    }
    
    // Approve Report Modal Logic
    const approveModal = document.getElementById('approveReportConfirmModal');
    const closeApproveModal = document.getElementById('closeApproveModal');
    const cancelApprove = document.getElementById('cancelApprove');
    const confirmApprove = document.getElementById('confirmApprove');
    
    function showApproveModal() {
        approveModal.classList.remove('hidden');
        approveModal.classList.add('flex');
    }
    
    function hideApproveModal() {
        approveModal.classList.add('hidden');
        approveModal.classList.remove('flex');
    }
    
    if (closeApproveModal) {
        closeApproveModal.onclick = hideApproveModal;
    }
    
    if (cancelApprove) {
        cancelApprove.onclick = hideApproveModal;
    }
    
    // Notification System
    window.showNotification = function(message, type = 'success') {
        const container = document.getElementById('notificationContainer');
        const notification = document.createElement('div');
        notification.className = `p-4 mb-2 rounded-lg flex items-center justify-between min-w-[300px] max-w-[400px] shadow-lg animate-slideIn ${
            type === 'success' 
                ? 'bg-green-50 text-green-800 border-l-4 border-green-500' 
                : 'bg-red-50 text-red-800 border-l-4 border-red-500'
        }`;
        
        const messageSpan = document.createElement('span');
        messageSpan.textContent = message;
        
        const closeBtn = document.createElement('button');
        closeBtn.className = 'text-current opacity-70 hover:opacity-100 px-2 text-xl';
        closeBtn.innerHTML = '&times;';
        closeBtn.onclick = function() {
            notification.classList.add('animate-slideOut');
            setTimeout(() => notification.remove(), 300);
        };
        
        notification.appendChild(messageSpan);
        notification.appendChild(closeBtn);
        container.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.classList.add('animate-slideOut');
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
    
    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === archiveModal) {
            hideArchiveModal();
        }
        if (event.target === approveModal) {
            hideApproveModal();
        }
        if (event.target === modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    setTimeout(function () {
        document.getElementById('pageFade').classList.add('opacity-100');
    }, 10);
});
</script>

<style>
@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.animate-slideIn {
    animation: slideIn 0.3s ease-out;
}

.animate-slideOut {
    animation: slideOut 0.3s ease-out forwards;
}
</style>
@endpush