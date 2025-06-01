<!-- Mobile Overlay -->
<div id="sidebarOverlay"
    class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden transition-opacity duration-300"></div>

<!-- Mobile Hamburger Button -->
<button id="sidebarToggle"
    class="fixed top-4 left-4 z-50 lg:hidden bg-white rounded-lg p-2 shadow-lg border border-gray-200 hover:bg-gray-50 transition-colors"
    aria-label="Toggle sidebar">
    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
</button>

<!-- Sidebar -->
<aside id="adminSidebar"
    class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-gray-200 flex flex-col shadow-xl transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out h-screen">
    <!-- Sidebar Header - Fixed -->
    <div class="flex items-center justify-between p-6 border-b border-gray-200 flex-shrink-0">
        <span class="text-2xl font-extrabold text-green-900 tracking-wide">Admin</span>
        <!-- Close button for mobile -->
        <button id="sidebarClose" class="lg:hidden p-1 rounded-lg hover:bg-gray-100 transition-colors"
            aria-label="Close sidebar">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Navigation - Scrollable -->
    <nav class="flex-1 overflow-y-auto px-6 py-4 min-h-0">
        <div class="space-y-2">
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-green-50 transition-colors group {{ request()->routeIs('admin.dashboard') ? 'bg-green-100 text-green-800 font-semibold' : '' }}">
                <i
                    class="fas fa-tachometer-alt w-5 text-center {{ request()->routeIs('admin.dashboard') ? 'text-green-600' : 'text-gray-500 group-hover:text-green-600' }}"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.reports.approved') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-green-50 transition-colors group {{ request()->routeIs('admin.reports.approved') ? 'bg-green-100 text-green-800 font-semibold' : '' }}">
                <i
                    class="fas fa-check-circle w-5 text-center {{ request()->routeIs('admin.reports.approved') ? 'text-green-600' : 'text-gray-500 group-hover:text-green-600' }}"></i>
                <span>Approved Reports</span>
            </a>
            <a href="{{ route('admin.reports.archived') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-green-50 transition-colors group {{ request()->routeIs('admin.reports.archived') ? 'bg-green-100 text-green-800 font-semibold' : '' }}">
                <i
                    class="fas fa-archive w-5 text-center {{ request()->routeIs('admin.reports.archived') ? 'text-green-600' : 'text-gray-500 group-hover:text-green-600' }}"></i>
                <span>Archived Reports</span>
            </a>
        </div>
    </nav>
    <!-- Logout Section - Fixed at Bottom -->
    <div class="border-t border-gray-200 p-6 flex-shrink-0">
        <form id="adminLogoutForm" method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="button" id="adminOpenLogoutModal"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-red-600 hover:bg-red-50 transition-colors group">
                <i class="fas fa-sign-out-alt w-5 text-center text-red-500 group-hover:text-red-600"></i>
                <span class="font-medium">Logout</span>
            </button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Sidebar toggle functionality
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarClose = document.getElementById('sidebarClose');
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('sidebarOverlay');

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                overlay.classList.remove('hidden');
                overlay.classList.add('block');
                // Prevent body scroll when sidebar is open on mobile
                document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
            }

            function closeSidebar() {
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('-translate-x-full');
                overlay.classList.remove('block');
                overlay.classList.add('hidden');
                // Restore body scroll
                document.body.classList.remove('overflow-hidden');
            }

            // Toggle sidebar on mobile
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', openSidebar);
            }

            // Close sidebar when close button is clicked
            if (sidebarClose) {
                sidebarClose.addEventListener('click', closeSidebar);
            }

            // Close sidebar when overlay is clicked
            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }

            // Close sidebar on escape key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !sidebar.classList.contains('-translate-x-full')) {
                    closeSidebar();
                }
            });

            // Handle window resize - close sidebar on large screens
            window.addEventListener('resize', function () {
                if (window.innerWidth >= 1024) { // lg breakpoint
                    closeSidebar();
                }
            });

            // Note: Logout modal functionality is handled in the main dashboard page
        });
    </script>
</aside>