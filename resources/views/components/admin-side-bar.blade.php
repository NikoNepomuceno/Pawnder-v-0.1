<aside class="h-screen bg-white border-r border-gray-200 flex flex-col py-8 px-5 w-64 min-w-max shadow-xl">
    <div class="mb-10 flex items-center gap-2">
        <span class="text-2xl font-extrabold text-green-900 tracking-wide">Admin</span>
    </div>
    <nav class="flex-1 flex flex-col gap-2">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-700 hover:bg-green-50 transition {{ request()->routeIs('admin.dashboard') ? 'bg-green-100 font-semibold' : '' }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="{{ route('admin.reports.approved') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-700 hover:bg-green-50 transition {{ request()->routeIs('admin.reports.approved') ? 'bg-green-100 font-semibold' : '' }}">
            <i class="fas fa-check-circle"></i> Approved Reports
        </a>
        <a href="{{ route('admin.reports.archived') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-700 hover:bg-green-50 transition {{ request()->routeIs('admin.reports.archived') ? 'bg-green-100 font-semibold' : '' }}">
            <i class="fas fa-archive"></i> Archived Reports
        </a>
    </nav>
    <form id="adminLogoutForm" method="POST" action="{{ route('admin.logout') }}" class="mt-auto">
        @csrf
        <button type="button" id="openLogoutModal" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-red-600 hover:bg-red-100 font-semibold transition cursor-pointer">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </form>

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="fixed inset-0 z-50 flex items-center justify-center bg-white/60 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300">
        <div id="logoutModalCard" class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-xs text-center relative border-2" style="border-color: #2d5a41;">
            <h3 class="text-xl font-bold mb-2" style="color: #2d5a41;">Confirm Logout</h3>
            <p class="mb-7 text-gray-600">Are you sure you want to log out?</p>
            <div class="flex gap-4 justify-center">
                <button id="cancelLogout" class="px-5 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold shadow-sm transition">Cancel</button>
                <button id="confirmLogout" class="px-5 py-2 rounded-lg font-semibold shadow-sm transition text-white" style="background-color: #2d5a41;">Logout</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const openBtn = document.getElementById('openLogoutModal');
            const modal = document.getElementById('logoutModal');
            const modalCard = document.getElementById('logoutModalCard');
            const cancelBtn = document.getElementById('cancelLogout');
            const confirmBtn = document.getElementById('confirmLogout');
            const form = document.getElementById('adminLogoutForm');

            function showModal() {
                modal.classList.remove('pointer-events-none');
                modal.classList.remove('opacity-0');
                modal.classList.add('opacity-100');
                setTimeout(() => {
                    modalCard.classList.remove('scale-95', 'opacity-0');
                    modalCard.classList.add('scale-100', 'opacity-100');
                }, 10);
            }
            function hideModal() {
                modalCard.classList.remove('scale-100', 'opacity-100');
                modalCard.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.remove('opacity-100');
                    modal.classList.add('opacity-0', 'pointer-events-none');
                }, 300);
            }
            if (openBtn && modal) {
                openBtn.onclick = showModal;
            }
            if (cancelBtn && modal) {
                cancelBtn.onclick = hideModal;
            }
            if (confirmBtn && form) {
                confirmBtn.onclick = function () {
                    form.submit();
                };
            }
            window.addEventListener('click', function (event) {
                if (event.target === modal) {
                    hideModal();
                }
            });
        });
    </script>
</aside> 