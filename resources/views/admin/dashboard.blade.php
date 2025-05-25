@extends('layouts.admin')

@section('content')
<div id="pageFade" class="flex min-h-screen opacity-0 transition-opacity duration-500">
    <x-admin-side-bar />
    <div class="flex-1 p-8">
        <div class="dashboard-header-row mb-6">
            <h1>Admin Dashboard</h1>
        </div>
        <div class="dashboard-stats-row">
            <livewire:admin.dashboard-stats />
        </div>
        <div class="reports-container">
            <div class="reports-section">
                <div class="section-header">
                    <h2><i class="fas fa-clock"></i> Pending Reports</h2>
                    {{-- <span class="section-count">0</span> --}}
                </div>
                <livewire:admin.reports-table status="pending" />
            </div>
        </div>
    </div>
</div>

    <!-- Logout Confirmation Modal -->
    <div id="adminLogoutConfirmModal" class="logout-modal" tabindex="-1" aria-modal="true" role="dialog">
        <div class="logout-modal-content">
            <span class="close-modal" id="adminCloseLogoutModal" tabindex="0" aria-label="Close">&times;</span>
            <h3>Confirm Logout</h3>
            <p>Are you sure you want to log out?</p>
            <div class="modal-actions">
                <button class="cancel-btn" id="adminCancelLogout">Cancel</button>
                <button class="submit-btn" id="adminConfirmLogout">Logout</button>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    <style>
        .dashboard-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.2rem;
        }
        .dashboard-header-row h1 {
            color: var(--admin-primary);
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0;
        }
        .dashboard-stats-row {
            margin-bottom: 2.2rem;
        }
        .admin-menu-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            margin-left: 0;
        }
        .admin-hamburger {
            background: none;
            border: none;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 44px;
            height: 44px;
            padding: 0;
        }
        .hamburger-bar {
            width: 28px;
            height: 3px;
            background: var(--admin-primary);
            margin: 3px 0;
            border-radius: 2px;
            transition: all 0.2s;
        }
        .admin-dropdown-menu {
            display: none;
            position: absolute;
            top: 48px;
            right: 0;
            background: #fff;
            border-radius: 0.7rem;
            box-shadow: 0 4px 16px rgba(27,67,50,0.13);
            min-width: 170px;
            z-index: 100;
            flex-direction: column;
            padding: 0.5rem 0;
        }
        .admin-dropdown-menu.show {
            display: flex;
        }
        .admin-dropdown-item {
            background: none;
            border: none;
            color: var(--admin-primary);
            text-align: left;
            width: 100%;
            padding: 0.7rem 1.2rem;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.7em;
            text-decoration: none;
            transition: background 0.13s;
        }
        .admin-dropdown-item:hover, .admin-dropdown-item:focus {
            background: var(--admin-bg-alt);
            outline: none;
        }
        .logout-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.25);
            justify-content: center;
            align-items: center;
        }
        .logout-modal.show {
            display: flex;
        }
        .logout-modal-content {
            background: #fff;
            border-radius: 1rem;
            padding: 2rem 2.5rem;
            box-shadow: 0 8px 32px rgba(27,67,50,0.15);
            min-width: 320px;
            max-width: 90vw;
            text-align: center;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .close-modal {
            position: absolute;
            right: 18px;
            top: 14px;
            font-size: 1.5rem;
            color: var(--admin-primary);
            cursor: pointer;
            background: none;
            border: none;
            transition: color 0.2s;
        }
        .close-modal:hover, .close-modal:focus {
            color: var(--admin-primary-dark);
            outline: none;
        }
        .modal-actions {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 1rem;
        }
        .cancel-btn, .submit-btn {
            padding: 0.6rem 1.5rem;
            border-radius: 0.5rem;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
        }
        .cancel-btn {
            background: var(--admin-bg-alt);
            color: var(--admin-primary);
        }
        .cancel-btn:hover {
            background: #e0e0e0;
        }
        .submit-btn {
            background: var(--admin-primary);
            color: #fff;
        }
        .submit-btn:hover {
            background: var(--admin-primary-dark);
        }
        @media (max-width: 600px) {
            .logout-modal-content {
                padding: 1.2rem 0.7rem;
                min-width: 0;
            }
        }
    </style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hamburger menu logic
    var hamburger=document.getElementById('adminHamburgerMenu');
    var dropdown=document.getElementById('adminDropdownMenu');
    var openBtn=document.getElementById('adminOpenLogoutModal');
    var modal=document.getElementById('adminLogoutConfirmModal');
    var closeBtn=document.getElementById('adminCloseLogoutModal');
    var cancelBtn=document.getElementById('adminCancelLogout');
    var confirmBtn=document.getElementById('adminConfirmLogout');
    var form=document.getElementById('adminLogoutForm');

    if (hamburger && dropdown) {
        hamburger.onclick=function(e) {
            e.stopPropagation();
            var expanded=hamburger.getAttribute('aria-expanded')==='true';
            hamburger.setAttribute('aria-expanded',  !expanded);
            dropdown.classList.toggle('show');
            if ( !expanded) dropdown.focus();
        }

        ;

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
                if ( !dropdown.contains(e.target) && !hamburger.contains(e.target)) {
                    dropdown.classList.remove('show');
                    hamburger.setAttribute('aria-expanded', 'false');
                }
            }

        );

        // Keyboard accessibility
        dropdown.addEventListener('keydown', function(e) {
                if (e.key==='Escape') {
                    dropdown.classList.remove('show');
                    hamburger.setAttribute('aria-expanded', 'false');
                    hamburger.focus();
                }
            }

        );
    }

    // Logout modal logic (unchanged)
    if (openBtn && modal) {
        openBtn.onclick=function() {
            modal.classList.add('show');
        }

        ;
    }

    if (closeBtn && modal) {
        closeBtn.onclick=function() {
            modal.classList.remove('show');
        }

        ;
    }

    if (cancelBtn && modal) {
        cancelBtn.onclick=function() {
            modal.classList.remove('show');
        }

        ;
    }

    if (confirmBtn && form) {
        confirmBtn.onclick=function() {
            form.submit();
        }

        ;
    }

    window.addEventListener('click', function(event) {
            if (event.target===modal) {
                modal.classList.remove('show');
            }
        }

    );
}

);

document.addEventListener('DOMContentLoaded', function () {
    setTimeout(function () {
        document.getElementById('pageFade').classList.add('opacity-100');
    }, 10);
});
</script>
@endpush