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
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">

    @stack('head')

    @livewireStyles

    <style>
        :root {
            /* Color System */
            --primary-color: #3F7D58;
            --primary-dark: #2d5a41;
            --primary-light: #4a8d65;
            --primary-lighter: #b7e4c7;

            /* Neutral Colors */
            --white: #ffffff;
            --gray-50: #f8f9fa;
            --gray-100: #f1f3f4;
            --gray-200: #e8eaed;
            --gray-300: #dadce0;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;

            /* Status Colors */
            --success-color: #16a34a;
            --success-bg: #f0fdf4;
            --error-color: #dc2626;
            --error-bg: #fef2f2;

            /* Spacing System */
            --space-xs: 0.25rem;
            --space-sm: 0.5rem;
            --space-md: 1rem;
            --space-lg: 1.5rem;
            --space-xl: 2rem;
            --space-2xl: 3rem;

            /* Border Radius */
            --radius-sm: 4px;
            --radius-md: 6px;
            --radius-lg: 8px;
            --radius-xl: 12px;
            --radius-full: 50px;

            /* Shadows */
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
            --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px rgba(0, 0, 0, 0.1);

            /* Typography */
            --font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            --font-size-xs: 0.75rem;
            --font-size-sm: 0.875rem;
            --font-size-base: 1rem;
            --font-size-lg: 1.125rem;
            --font-size-xl: 1.25rem;
            --font-size-2xl: 1.5rem;

            /* Transitions */
            --transition-fast: 0.15s ease;
            --transition-base: 0.2s ease;
            --transition-slow: 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--gray-50);
            color: var(--gray-800);
            line-height: 1.6;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
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
            background: var(--primary-color);
            padding: var(--space-md) var(--space-xl);
            color: var(--white);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow-md);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
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
            border-radius: var(--radius-full);
            border: 1px solid rgba(255, 255, 255, 0.2);
            outline: none;
            padding: var(--space-sm) var(--space-lg);
            font-size: var(--font-size-sm);
            background: rgba(255, 255, 255, 0.15);
            color: var(--white);
            width: 240px;
            height: 42px;
            transition: var(--transition-base);
            backdrop-filter: blur(10px);
        }

        .nav-search-input:focus {
            background: var(--white);
            color: var(--primary-dark);
            border-color: var(--white);
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
        }

        .nav-search-input::placeholder {
            color: rgba(255, 255, 255, 0.7);
            opacity: 1;
        }

        .nav-search-filter-btn {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            outline: none;
            cursor: pointer;
            border-radius: 50%;
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 20px;
            padding: 0;
            margin: 0;
            transition: var(--transition-base);
            backdrop-filter: blur(10px);
        }

        .nav-search-filter-btn:hover {
            background: var(--white);
            color: var(--primary-color);
            border-color: var(--white);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .nav-search-filter-btn:active {
            transform: translateY(0);
        }

        .nav-search-dropdown {
            display: none;
            position: absolute;
            top: 110%;
            right: 0;
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
            width: 260px;
            z-index: 100;
            padding: var(--space-lg);
            border: 1px solid var(--gray-200);
            backdrop-filter: blur(20px);
            animation: slideDown 0.2s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .filter-row {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 8px;
        }

        .filter-row:last-child {
            margin-bottom: 12px;
        }

        .filter-group {
            display: flex;
            gap: 6px;
        }

        .filter-item {
            flex: 1;
        }

        .filter-item.breed-filter {
            display: none;
            animation: slideIn 0.15s ease-out;
        }

        .filter-item.breed-filter.show {
            display: block;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-4px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .nav-search-dropdown select {
            width: 100%;
            height: 36px;
            padding: 0 var(--space-sm);
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-md);
            background: var(--white);
            color: var(--gray-700);
            font-size: var(--font-size-sm);
            font-weight: 500;
            appearance: none;
            cursor: pointer;
            transition: var(--transition-base);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right var(--space-sm) center;
            background-repeat: no-repeat;
            background-size: 14px;
            padding-right: 28px;
        }

        .nav-search-dropdown select:hover {
            border-color: var(--gray-400);
            box-shadow: var(--shadow-sm);
        }

        .nav-search-dropdown select:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(63, 125, 88, 0.1);
        }

        .filter-actions {
            display: flex;
            gap: 6px;
        }

        .filter-apply {
            flex: 1;
            height: 32px;
            background: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: var(--radius-md);
            font-size: var(--font-size-xs);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-base);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-apply:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .filter-apply:active {
            transform: translateY(0);
        }

        .filter-clear {
            flex: 1;
            height: 32px;
            background: var(--gray-100);
            color: var(--gray-600);
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-md);
            font-size: var(--font-size-xs);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-base);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-clear:hover {
            background: var(--gray-200);
            color: var(--gray-700);
            border-color: var(--gray-400);
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        .filter-clear:active {
            transform: translateY(0);
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
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            background: #fff;
            color: #222;
            border-left: 6px solid #3F7D58;
            animation: fadeIn 0.3s;
            position: relative;
            min-width: 280px;
            max-width: 400px;
            margin-bottom: 8px;
        }

        .notification.success,
        .notification.info {
            border-left-color: #16a34a;
            background: #f0fdf4;
            color: #166534;
        }

        .notification.error {
            border-left-color: #dc2626;
            background: #fef2f2;
            color: #991b1b;
        }

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
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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
                    <form id="navSearchForm" method="GET" action="{{ route('home') }}" class="nav-search-form">
                        <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}"
                            class="nav-search-input">
                        <button type="button" id="filterToggleBtn" class="nav-search-filter-btn">
                            <i class='bx bx-filter filter-icon'></i>
                        </button>
                        <div id="filterDropdown" class="nav-search-dropdown">
                            <div class="filter-row">
                                <div class="filter-item">
                                    <select name="status" id="status">
                                        <option value="" {{ request('status') == '' ? 'selected' : '' }}>All Status
                                        </option>
                                        <option value="found" {{ request('status') == 'found' ? 'selected' : '' }}>Found
                                        </option>
                                        <option value="not_found" {{ request('status') == 'not_found' ? 'selected' : '' }}>Not Found</option>
                                    </select>
                                </div>
                            </div>

                            <div class="filter-row">
                                <div class="filter-group">
                                    <div class="filter-item">
                                        <select name="pet_type" id="pet_type">
                                            <option value="" {{ request('pet_type') == '' ? 'selected' : '' }}>All Animals
                                            </option>
                                            <option value="dog" {{ request('pet_type') == 'dog' ? 'selected' : '' }}>Dog
                                            </option>
                                            <option value="cat" {{ request('pet_type') == 'cat' ? 'selected' : '' }}>Cat
                                            </option>
                                            <option value="bird" {{ request('pet_type') == 'bird' ? 'selected' : '' }}>
                                                Bird</option>
                                            <option value="rabbit" {{ request('pet_type') == 'rabbit' ? 'selected' : '' }}>Rabbit</option>
                                            <option value="hamster" {{ request('pet_type') == 'hamster' ? 'selected' : '' }}>Hamster</option>
                                            <option value="guinea pig" {{ request('pet_type') == 'guinea pig' ? 'selected' : '' }}>Guinea Pig</option>
                                            <option value="fish" {{ request('pet_type') == 'fish' ? 'selected' : '' }}>
                                                Fish</option>
                                            <option value="reptile" {{ request('pet_type') == 'reptile' ? 'selected' : '' }}>Reptile</option>
                                            <option value="other" {{ request('pet_type') == 'other' ? 'selected' : '' }}>
                                                Other</option>
                                        </select>
                                    </div>
                                    <div class="filter-item breed-filter" id="breedSection">
                                        <select name="breed_filter" id="breed_filter">
                                            <option value="">Breed</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="filter-actions">
                                <button type="button" class="filter-clear" id="clearFilters">Clear</button>
                                <button type="submit" class="filter-apply">Apply</button>
                            </div>
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

            // Dynamic breed filtering
            const petTypeSelect = document.getElementById('pet_type');
            const breedSection = document.getElementById('breedSection');
            const breedSelect = document.getElementById('breed_filter');

            // Breed data for different animal types
            const breedData = {
                dog: [
                    { value: 'golden retriever', label: 'Golden Retriever' },
                    { value: 'labrador', label: 'Labrador' },
                    { value: 'german shepherd', label: 'German Shepherd' },
                    { value: 'bulldog', label: 'Bulldog' },
                    { value: 'poodle', label: 'Poodle' },
                    { value: 'beagle', label: 'Beagle' },
                    { value: 'rottweiler', label: 'Rottweiler' },
                    { value: 'yorkshire terrier', label: 'Yorkshire Terrier' },
                    { value: 'dachshund', label: 'Dachshund' },
                    { value: 'siberian husky', label: 'Siberian Husky' },
                    { value: 'shih tzu', label: 'Shih Tzu' },
                    { value: 'chihuahua', label: 'Chihuahua' },
                    { value: 'border collie', label: 'Border Collie' },
                    { value: 'boxer', label: 'Boxer' },
                    { value: 'cocker spaniel', label: 'Cocker Spaniel' }
                ],
                cat: [
                    { value: 'persian', label: 'Persian' },
                    { value: 'siamese', label: 'Siamese' },
                    { value: 'maine coon', label: 'Maine Coon' },
                    { value: 'british shorthair', label: 'British Shorthair' },
                    { value: 'ragdoll', label: 'Ragdoll' },
                    { value: 'bengal', label: 'Bengal' },
                    { value: 'russian blue', label: 'Russian Blue' },
                    { value: 'scottish fold', label: 'Scottish Fold' },
                    { value: 'sphynx', label: 'Sphynx' },
                    { value: 'abyssinian', label: 'Abyssinian' }
                ],
                bird: [
                    { value: 'budgerigar', label: 'Budgerigar' },
                    { value: 'canary', label: 'Canary' },
                    { value: 'cockatiel', label: 'Cockatiel' },
                    { value: 'lovebird', label: 'Lovebird' },
                    { value: 'parrot', label: 'Parrot' },
                    { value: 'finch', label: 'Finch' },
                    { value: 'macaw', label: 'Macaw' },
                    { value: 'conure', label: 'Conure' }
                ],
                rabbit: [
                    { value: 'holland lop', label: 'Holland Lop' },
                    { value: 'netherland dwarf', label: 'Netherland Dwarf' },
                    { value: 'mini rex', label: 'Mini Rex' },
                    { value: 'lionhead', label: 'Lionhead' },
                    { value: 'flemish giant', label: 'Flemish Giant' },
                    { value: 'angora', label: 'Angora' }
                ]
            };

            function updateBreedOptions(animalType) {
                // Clear existing options
                breedSelect.innerHTML = '<option value="">Breed</option>';

                if (animalType && breedData[animalType]) {
                    // Add "All Breed" option for the selected animal type
                    const allBreedOption = document.createElement('option');
                    allBreedOption.value = `all_${animalType}_breeds`;
                    allBreedOption.textContent = `All ${animalType.charAt(0).toUpperCase() + animalType.slice(1)} Breeds`;
                    // Check if "All Breed" option is selected
                    if (`all_${animalType}_breeds` === '{{ request("breed_filter") }}') {
                        allBreedOption.selected = true;
                    }
                    breedSelect.appendChild(allBreedOption);

                    // Add breeds for selected animal type
                    breedData[animalType].forEach(breed => {
                        const option = document.createElement('option');
                        option.value = breed.value;
                        option.textContent = breed.label;
                        // Preserve selection if it matches
                        if (breed.value === '{{ request("breed_filter") }}') {
                            option.selected = true;
                        }
                        breedSelect.appendChild(option);
                    });

                    // Show breed section with animation
                    breedSection.classList.add('show');
                } else {
                    // Hide breed section
                    breedSection.classList.remove('show');
                }

                // Add common options for all types
                if (animalType) {
                    const commonBreeds = [
                        { value: 'mixed breed', label: 'Mixed Breed' },
                        { value: 'unknown', label: 'Unknown' }
                    ];

                    commonBreeds.forEach(breed => {
                        const option = document.createElement('option');
                        option.value = breed.value;
                        option.textContent = breed.label;
                        if (breed.value === '{{ request("breed_filter") }}') {
                            option.selected = true;
                        }
                        breedSelect.appendChild(option);
                    });
                }
            }

            // Handle pet type change
            if (petTypeSelect) {
                petTypeSelect.addEventListener('change', function () {
                    updateBreedOptions(this.value);
                });

                // Initialize on page load
                updateBreedOptions(petTypeSelect.value);
            }

            // Clear filters functionality
            const clearFiltersBtn = document.getElementById('clearFilters');
            if (clearFiltersBtn) {
                clearFiltersBtn.addEventListener('click', function () {
                    // Reset all filter selects to their default values
                    const statusSelect = document.getElementById('status');
                    const petTypeSelect = document.getElementById('pet_type');
                    const breedSelect = document.getElementById('breed_filter');

                    if (statusSelect) statusSelect.value = '';
                    if (petTypeSelect) petTypeSelect.value = '';
                    if (breedSelect) breedSelect.value = '';

                    // Hide breed section
                    if (breedSection) {
                        breedSection.classList.remove('show');
                    }

                    // Reset breed options
                    if (breedSelect) {
                        breedSelect.innerHTML = '<option value="">Breed</option>';
                    }

                    // Submit the form to apply cleared filters
                    const form = document.getElementById('navSearchForm');
                    if (form) {
                        form.submit();
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