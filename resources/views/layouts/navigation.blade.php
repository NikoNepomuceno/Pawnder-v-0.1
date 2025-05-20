<!-- Navigation Bar -->
<nav class="navbar">
    <a href="{{ route('home') }}" class="brand">Pawnder</a>

    <div class="hamburger" id="hamburger">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <div class="nav-center" id="nav-center">
        <div class="nav-links">
            <a href="{{ route('home') }}" title="Home"><i class='bx bx-home-alt'></i></a>
            <a href="{{ route('notifications') }}" title="Notifications" class="notification-link">
                <i class='bx bx-bell'></i>
                @if(auth()->user() && auth()->user()->unreadNotifications->count() > 0)
                    <span
                        class="notification-badge {{ auth()->user()->unreadNotifications->count() > 99 ? 'overflow' : '' }} {{ auth()->user()->unreadNotifications->count() > 0 ? 'has-notifications' : '' }}">
                        {{ auth()->user()->unreadNotifications->count() > 99 ? '99+' : auth()->user()->unreadNotifications->count() }}
                    </span>
                @endif
            </a>
        </div>
        <div class="search-bar">
            <form id="navSearchForm" method="GET" action="" class="nav-search-form">
                <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}"
                    class="nav-search-input">
                <button type="button" id="filterToggleBtn" class="nav-search-filter-btn">
                    <i class='bx bx-filter filter-icon'></i>
                </button>
                <div id="filterDropdown" class="nav-search-dropdown">
                    <label for="status">Category</label>
                    <select name="status" id="status">
                        <option value="" {{ request('status') == '' ? 'selected' : '' }}>All</option>
                        <option value="found" {{ request('status') == 'found' ? 'selected' : '' }}>Found</option>
                        <option value="not_found" {{ request('status') == 'not_found' ? 'selected' : '' }}>Not Found
                        </option>
                    </select>
                    <button type="submit" class="submit-btn">Apply Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="nav-right">
        <div class="profile-wrapper">
            <div class="profile-icon" id="profile-icon">
                @if(Auth::user() && Auth::user()->profile_picture)
                    <img src="{{ Auth::user()->profile_picture }}" alt="Profile">
                @else
                    <img src="{{ asset('images/default-profile.png') }}" alt="Default Profile Picture">
                @endif
            </div>
            <span class="dropdown-indicator"><i class='bx bx-chevron-down'></i></span>
        </div>

        <div class="dropdown-content" id="dropdown-content">
            <a href="{{ route('view-profile') }}" class="dropdown-item"><i class='bx bx-user mr-2'></i> View
                Profile</a>
            <a href="{{ route('settings') }}" class="dropdown-item"><i class='bx bx-cog mr-2'></i> Settings</a>
            <button class="logoutBtn" type="button"><i class='bx bx-log-out-circle mr-2'></i> Logout</button>
        </div>
    </div>
</nav>