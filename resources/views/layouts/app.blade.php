<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/logo.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/logo.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo.png') }}" type="image/x-icon">
    
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <title>@yield('title', 'Pawnder')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- jQuery for AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">

    @stack('head')

    @livewireStyles

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        /* Page Transition Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        main {
            animation: fadeInUp 0.5s ease-out;
        }

        /* Navigation Bar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #3F7D58;
            padding: 14px 32px;
            color: #fff;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .brand {
            font-size: 1.7rem;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-logo {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }

        .nav-center {
            display: flex;
            align-items: center;
            gap: 32px;
            flex: 1;
            justify-content: center;
        }

        .nav-links {
            display: flex;
            gap: 24px;
        }

        .nav-links a {
            color: #fff;
            text-decoration: none;
            font-size: 25px;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: #b7e4c7;
        }

        .search-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
        }

        .nav-search-form {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-search-input {
            border-radius: 50px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            outline: none;
            padding: 10px 18px;
            font-size: 15px;
            background: rgba(255, 255, 255, 0.18);
            color: #fff;
            width: 200px;
            height: 40px;
            box-shadow: none;
            transition: all 0.3s;
        }

        .nav-search-input:focus {
            background: #fff;
            color: #2d5a41;
            border-color: #3F7D58;
            box-shadow: 0 0 0 2px rgba(63, 125, 88, 0.1);
        }

        .nav-search-input::placeholder {
            color: rgba(255, 255, 255, 0.8);
            opacity: 1;
        }

        .nav-search-filter-btn {
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.4);
            outline: none;
            cursor: pointer;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 22px;
            padding: 0;
            margin: 0;
            transition: all 0.3s;
        }

        .nav-search-filter-btn:hover {
            background: #fff;
            color: #3F7D58;
            border: 1.5px solid #3F7D58;
        }

        .nav-search-dropdown {
            display: none;
            position: absolute;
            top: 110%;
            right: 0;
            background: #fff;
            color: #333;
            border-radius: 10px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.16);
            min-width: 180px;
            z-index: 100;
            padding: 16px 18px 14px 18px;
            border: 1px solid rgba(63, 125, 88, 0.1);
        }

        .nav-search-dropdown label {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            display: block;
            color: #2d5a41;
        }

        .nav-search-dropdown select {
            width: 100%;
            padding: 8px 10px;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
            margin-bottom: 10px;
            background-color: #f8f9fa;
            color: #333;
            transition: all 0.3s;
        }

        .nav-search-dropdown select:focus {
            border-color: #3F7D58;
            box-shadow: 0 0 0 2px rgba(63, 125, 88, 0.1);
            outline: none;
        }

        .nav-search-dropdown .submit-btn {
            width: 100%;
            padding: 8px 0;
            border-radius: 6px;
            background: #3F7D58;
            color: #fff;
            border: none;
            font-weight: 500;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .nav-search-dropdown .submit-btn:hover {
            background: #2d5a41;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-left: 24px;
        }

        .profile-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .profile-icon {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid #fff;
            transition: all 0.3s;
            background: #3F7D58;
        }

        .profile-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            display: block;
        }

        .dropdown-indicator {
            position: absolute;
            right: 0;
            bottom: -2px;
            background: #fff;
            border-radius: 50%;
            width: 15px;
            height: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.10);
            border: 1.5px solid #e0e0e0;
            z-index: 10;
        }

        .dropdown-indicator i {
            color: #222;
            font-size: 15px;
            line-height: 1;
        }

        .dropdown-content {
            position: absolute;
            right: 0;
            top: 55px;
            background-color: #fff;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            z-index: 1000;
            min-width: 180px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease;
        }

        .dropdown-content.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-content i {
            padding-right: 10px;
        }

        .dropdown-content a,
        .dropdown-content button.logoutBtn {
            display: flex;
            align-items: center;
            padding: 12px 18px;
            text-decoration: none;
            color: #333;
            background-color: #fff;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
            transform: translateX(-20px);
            opacity: 0;
        }

        .dropdown-content.active a,
        .dropdown-content.active button.logoutBtn {
            transform: translateX(0);
            opacity: 1;
        }

        .dropdown-content a:nth-child(1) {
            transition-delay: 0.1s;
        }

        .dropdown-content a:nth-child(2) {
            transition-delay: 0.2s;
        }

        .dropdown-content button.logoutBtn {
            transition-delay: 0.3s;
        }

        .dropdown-content a:hover,
        .dropdown-content button.logoutBtn:hover {
            background-color: #f8f9fa;
            padding-left: 22px;
            color: #3F7D58;
        }

        .hamburger {
            display: none;
            flex-direction: column;
            gap: 6px;
            cursor: pointer;
            padding: 5px;
            z-index: 101;
        }

        .hamburger span {
            display: block;
            width: 28px;
            height: 3px;
            background-color: #fff;
            transition: all 0.3s;
            border-radius: 2px;
        }

        .hamburger.active span:nth-child(1) {
            transform: translateY(9px) rotate(45deg);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: translateY(-9px) rotate(-45deg);
        }

        @media screen and (max-width: 900px) {
            .navbar {
                padding: 10px 10px;
            }

            .nav-center {
                gap: 10px;
            }
        }

        @media screen and (max-width: 768px) {
            .hamburger {
                display: flex;
            }

            .brand {
                display: none;
            }

            .nav-center {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: #3F7D58;
                padding: 80px 20px 20px;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                z-index: 100;
            }

            .nav-center.active {
                display: flex;
                animation: fadeIn 0.3s;
            }

            .nav-links {
                flex-direction: column;
                align-items: center;
                gap: 25px;
                margin-bottom: 30px;
            }

            .nav-links a {
                font-size: 28px;
            }

            .search-bar input[type="text"] {
                width: 100%;
                max-width: 300px;
                padding: 12px 18px;
            }

            .nav-right {
                margin-left: auto;
            }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .modal[style*="display: flex"] {
            opacity: 1;
            visibility: visible;
        }

        #logoutConfirmModal {
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

        #logoutConfirmModal[style*="display: flex"] {
            opacity: 1;
            visibility: visible;
        }

        #logoutConfirmModal .modal-content {
            margin: 0;
            position: relative;
            padding: 32px 32px 24px 32px;
            max-width: 400px;
            width: 90vw;
            box-sizing: border-box;
            background: #ffffff !important;
            border: 1px solid rgba(63, 125, 88, 0.2) !important;
            box-shadow: 0 8px 32px rgba(63, 125, 88, 0.18) !important;
            border-radius: 18px;
            transform: scale(0.95);
            opacity: 0;
            transition: transform 0.3s ease, opacity 0.3s ease;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
        }

        #logoutConfirmModal[style*="display: flex"] .modal-content {
            transform: scale(1);
            opacity: 1;
        }

        #logoutConfirmModal .modal-content h3 {
            color: #3F7D58 !important;
            font-weight: 700;
            margin-bottom: 8px;
        }

        #logoutConfirmModal .modal-content p {
            color: #666666 !important;
            margin-bottom: 24px;
        }

        body.dark-mode #logoutConfirmModal .modal-content {
            background: #23272b !important;
            border: 1px solid rgba(63, 125, 88, 0.3) !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3) !important;
        }

        body.dark-mode #logoutConfirmModal .modal-content h3 {
            color: #3F7D58 !important;
        }

        body.dark-mode #logoutConfirmModal .modal-content p {
            color: #fff !important;
        }

        body.dark-mode #logoutConfirmModal .cancel-btn {
            background-color: #2d333b;
            color: #e0e0e0;
        }

        body.dark-mode #logoutConfirmModal .cancel-btn:hover {
            background-color: #373e47;
            color: #fff;
        }

        body.dark-mode #logoutConfirmModal .submit-btn {
            background-color: #3F7D58;
            color: #fff;
        }

        body.dark-mode #logoutConfirmModal .submit-btn:hover {
            background-color: #4a8d65;
        }

        @media (max-width: 500px) {
            #logoutConfirmModal .modal-content {
                padding: 18px 8px 16px 8px;
                max-width: 98vw;
            }
        }

        /* Custom scrollbar styles */
        .modal-content::-webkit-scrollbar {
            width: 6px;
        }

        .modal-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .modal-content::-webkit-scrollbar-thumb {
            background: #3F7D58;
            border-radius: 3px;
        }

        .modal-content::-webkit-scrollbar-thumb:hover {
            background: #2d5a41;
        }

        /* Modal Animation Keyframes */
        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes modalFadeOut {
            from {
                opacity: 1;
                transform: scale(1);
            }

            to {
                opacity: 0;
                transform: scale(0.95);
            }
        }

        /* Close Modal Animation */
        .close-modal {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 24px;
            font-weight: bold;
            color: #3F7D58;
            cursor: pointer;
            transition: all 0.2s ease;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transform: rotate(0deg);
        }

        .close-modal:hover {
            background-color: #f0f2f5;
            color: #2d5a41;
            transform: rotate(90deg);
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2d5a41;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="email"]:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3F7D58;
            box-shadow: 0 0 0 2px rgba(63, 125, 88, 0.1);
            background-color: #fff;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .cancel-btn,
        .submit-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .cancel-btn {
            background-color: #f0f2f5 !important;
            color: #1a3d2b !important;
        }

        .submit-btn {
            background-color: #3F7D58;
            color: white;
        }

        .cancel-btn:hover {
            background-color: #e4e6e9 !important;
            color: #1a3d2b !important;
        }

        .submit-btn:hover {
            background-color: #2d5a41;
            transform: translateY(-1px);
        }

        body.dark-mode .cancel-btn {
            background-color: #2d333b !important;
            color: #e0e0e0 !important;
        }

        body.dark-mode .cancel-btn:hover {
            background-color: #373e47 !important;
            color: #fff !important;
        }

        /* Alert Styles */
        .alert {
            padding: 12px 16px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-error {
            background-color: #fef2f2;
            color: #dc2626;
            border: 1px solid #fee2e2;
        }

        .alert-success {
            background-color: #f0fdf4;
            color: #16a34a;
            border: 1px solid #dcfce7;
        }

        /* Modal Header Styles */
        .modal-content h2,
        .modal-content h3 {
            color: #2d5a41;
            margin-bottom: 20px;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .modal-content p {
            color: #4b5563;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        #logoutConfirmModal .modal-content .cancel-btn {
            background-color: #f0f2f5 !important;
            color: #1a3d2b !important;
        }

        #logoutConfirmModal .modal-content .submit-btn {
            background-color: #3F7D58;
            color: white !important;
        }

        #logoutConfirmModal .modal-content .cancel-btn:hover {
            background-color: #e4e6e9 !important;
            color: #1a3d2b !important;
        }

        #logoutConfirmModal .modal-content .submit-btn:hover {
            background-color: #2d5a41;
            color: white !important;
        }

        body.dark-mode #logoutConfirmModal .modal-content .cancel-btn {
            background-color: #2d333b !important;
            color: #e0e0e0 !important;
        }

        body.dark-mode #logoutConfirmModal .modal-content .cancel-btn:hover {
            background-color: #373e47 !important;
            color: #fff !important;
        }

        body.dark-mode #logoutConfirmModal .modal-content .submit-btn {
            background-color: #3F7D58;
            color: #fff !important;
        }

        body.dark-mode #logoutConfirmModal .modal-content .submit-btn:hover {
            background-color: #4a8d65;
            color: #fff !important;
        }

        /* Notification Container (class for styling, id for JS) */
        .notification-container {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 3000;
            display: flex;
            flex-direction: column;
            gap: 16px;
            min-width: 320px;
            max-width: 90vw;
        }
        /* Notification Alert Base */
        .notification {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 24px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 500;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            background: #fff;
            color: #222;
            border-left: 6px solid #3F7D58;
            animation: fadeIn 0.3s;
            position: relative;
            min-width: 280px;
            max-width: 400px;
            margin-bottom: 8px;
        }
        .notification.success, .notification.info {
            border-left-color: #16a34a;
            background: #f0fdf4;
            color: #166534;
        }
        .notification.error   { border-left-color: #dc2626; background: #fef2f2; color: #991b1b; }
        .notification .close-btn {
            background: none;
            border: none;
            color: inherit;
            font-size: 1.5rem;
            cursor: pointer;
            margin-left: 16px;
            transition: color 0.2s;
        }
        .notification .close-btn:hover {
            color: #222;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px);}
            to   { opacity: 1; transform: translateY(0);}
        }
    </style>
    <script>
        // Apply dark mode on every page load if set in localStorage
        (function () {
            try {
                var theme = localStorage.getItem('theme');
                if (theme === 'dark') {
                    document.addEventListener('DOMContentLoaded', function () {
                        document.body.classList.add('dark-mode');
                    });
                }
            } catch (e) { }
        })();
    </script>
</head>

<body>
    <!-- Notification Container -->
    <div class="notification-container" id="notificationContainer"></div>

    <div id="app">
        <!-- Navigation Bar -->
        <nav class="navbar">
            <a href="{{ route('home') }}" class="brand">
                <img src="{{ asset('images/white_logo.png') }}" alt="Pawnder Logo" class="brand-logo">
                Pawnder
            </a>

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
                                <option value="not_found" {{ request('status') == 'not_found' ? 'selected' : '' }}>Not
                                    Found
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

        <main>
            @yield('content')
        </main>
    </div>

    <!-- Logout Confirmation Modal (always at end of body for global access) -->
    <div id="logoutConfirmModal" class="modal">
        <div class="modal-content" style="max-width: 400px;">
            <h3 style="color: #3F7D58; font-weight: 700; margin-bottom: 8px;">Confirm Logout</h3>
            <p style="color: #666666; margin-bottom: 24px;">Are you sure you want to logout?</p>
            <div class="form-actions" style="display: flex; gap: 24px; justify-content: center;">
                <button type="button" class="cancel-btn" id="cancelLogoutBtn">Cancel</button>
                <button type="button" class="submit-btn" id="confirmLogoutBtn">Logout</button>
            </div>
        </div>
    </div>
    <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Navigation elements
            const hamburger = document.getElementById('hamburger');
            const navCenter = document.getElementById('nav-center');
            const profileIcon = document.getElementById('profile-icon');
            const dropdownContent = document.getElementById('dropdown-content');
            const logoutBtns = document.querySelectorAll('.logoutBtn');
            const filterBtn = document.getElementById('filterToggleBtn');
            const filterDropdown = document.getElementById('filterDropdown');
            const logoutConfirmModal = document.getElementById('logoutConfirmModal');
            const cancelLogoutBtn = document.getElementById('cancelLogoutBtn');
            const confirmLogoutBtn = document.getElementById('confirmLogoutBtn');
            const logoutForm = document.getElementById('logoutForm');

            // Mobile menu toggle
            if (hamburger && navCenter) {
                hamburger.addEventListener('click', () => {
                    navCenter.classList.toggle('active');
                    hamburger.classList.toggle('active');
                });
            }

            // Profile dropdown toggle
            if (profileIcon && dropdownContent) {
                profileIcon.addEventListener('click', (e) => {
                    e.stopPropagation();
                    dropdownContent.classList.toggle('active');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', (e) => {
                    if (!profileIcon.contains(e.target) && !dropdownContent.contains(e.target)) {
                        dropdownContent.classList.remove('active');
                    }
                });
            }

            // Search filter dropdown toggle
            if (filterBtn && filterDropdown) {
                document.addEventListener('click', function (e) {
                    if (filterBtn.contains(e.target)) {
                        filterDropdown.style.display = filterDropdown.style.display === 'block' ? 'none' : 'block';
                    } else if (!filterDropdown.contains(e.target)) {
                        filterDropdown.style.display = 'none';
                    }
                });
            }

            // Logout button hover effects
            if (logoutBtns.length > 0) {
                logoutBtns.forEach(logoutBtn => {
                    logoutBtn.addEventListener('mouseenter', () => {
                        logoutBtn.style.backgroundColor = '#f8f9fa';
                        logoutBtn.style.paddingLeft = '22px';
                        logoutBtn.style.color = '#3F7D58';
                    });
                    logoutBtn.addEventListener('mouseleave', () => {
                        logoutBtn.style.backgroundColor = '#fff';
                        logoutBtn.style.paddingLeft = '18px';
                        logoutBtn.style.color = '#333';
                    });
                    logoutBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        logoutConfirmModal.style.display = 'flex';
                    });
                });
            }

            // Logout confirmation modal
            if (cancelLogoutBtn) {
                cancelLogoutBtn.addEventListener('click', () => {
                    logoutConfirmModal.style.display = 'none';
                });
            }
            if (confirmLogoutBtn && logoutForm) {
                confirmLogoutBtn.addEventListener('click', () => {
                    logoutForm.submit();
                });
            }

            // Close logout modal when clicking outside
            window.addEventListener('click', (event) => {
                if (event.target === logoutConfirmModal) {
                    logoutConfirmModal.style.display = 'none';
                }
            });
        });

        function showNotification(message, type = 'success') {
            const container = document.getElementById('notificationContainer');
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;

            const messageSpan = document.createElement('span');
            messageSpan.textContent = message;

            const closeBtn = document.createElement('button');
            closeBtn.className = 'close-btn';
            closeBtn.innerHTML = '&times;';
            closeBtn.onclick = () => {
                notification.classList.add('slide-out');
                setTimeout(() => notification.remove(), 300);
            };

            notification.appendChild(messageSpan);
            notification.appendChild(closeBtn);
            container.appendChild(notification);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.classList.add('slide-out');
                    setTimeout(() => notification.remove(), 300);
                }
            }, 5000);
        }

        // Handle session flash messages
        @if(session('success'))
            showNotification('{{ session('success') }}', 'success');
        @endif

        @if(session('error'))
            showNotification('{{ session('error') }}', 'error');
        @elseif($errors->any())
            // Show only the first error to avoid duplicates
            showNotification('{{ $errors->first() }}', 'error');
        @endif
    </script>

    @livewireScripts
    @stack('scripts')
</body>

</html>