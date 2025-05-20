@extends('layouts.app')

@section('title', 'Settings')

@push('head')
    <link rel="stylesheet" href="{{ asset('css/settings.css') }}">
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const themeForm = document.getElementById('themeForm');
            if (themeForm) {
                const radios = themeForm.elements['theme'];
                // Set initial theme from localStorage
                const savedTheme = localStorage.getItem('theme');
                if (savedTheme === 'dark') {
                    document.body.classList.add('dark-mode');
                    radios[1].checked = true;
                } else {
                    document.body.classList.remove('dark-mode');
                    radios[0].checked = true;
                }
                // Listen for changes
                themeForm.addEventListener('change', function (e) {
                    if (e.target.value === 'dark') {
                        document.body.classList.add('dark-mode');
                        localStorage.setItem('theme', 'dark');
                    } else {
                        document.body.classList.remove('dark-mode');
                        localStorage.setItem('theme', 'light');
                    }
                });
            }
        });
    </script>
@endpush

@section('content')
    <div class="settings-container">
        <h1 class="settings-title">Settings</h1>
        <div class="settings-section">
            <p>Welcome to your settings page. Customize your preferences here.</p>
            <!-- Add your settings form or options here -->
            <div class="theme-option">
                <h2 class="theme-title">Theme</h2>
                <form id="themeForm">
                    <label class="theme-label">
                        <input type="radio" name="theme" value="light" checked>
                        Light Mode
                    </label>
                    <label class="theme-label">
                        <input type="radio" name="theme" value="dark">
                        Dark Mode
                    </label>
                </form>
            </div>
        </div>
    </div>
@endsection