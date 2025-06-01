<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/logo.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/logo.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo.png') }}" type="image/x-icon">

    <!-- DNS Prefetch for faster navigation -->
    <link rel="dns-prefetch" href="{{ parse_url(route('register.page'), PHP_URL_HOST) }}">

    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="/css/auth/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Pawnder - Login</title>
</head>

<body>
    <div class="page-transition-overlay"></div>
    <div class="notification-container" id="notificationContainer"></div>
    <div class="register-split">
        <div class="register-left">
            <div class="register-form-container">
                <div class="register-title">Sign in</div>
                <form method="POST" action="{{ route('login.submit') }}" class="register-form" id="loginForm">
                    @csrf
                    <div class="register-input-wrapper">
                        <span class="register-input-icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" id="email" name="email" class="register-input" placeholder="Email"
                            required />
                    </div>
                    <div class="register-input-wrapper">
                        <span class="register-input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" id="password" name="password" class="register-input"
                            placeholder="Password" required />
                        <button type="button" class="password-toggle" onclick="togglePasswordVisibility('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="login-options">
                        <label>
                            <input type="checkbox" name="remember" id="remember" />
                            Remember Me
                        </label>
                        <a href="{{ route('password.request') }}" class="forgot-password">Forgot Password?</a>
                    </div>
                    <button type="submit" class="register-btn" id="loginBtn">
                        <span class="btn-text">Login</span>
                        <span class="btn-loading" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i> Logging in...
                        </span>
                    </button>
                </form>
                <div class="register-or">Or Continue with</div>
                <div class="register-socials">
                    <a href="{{ route('google.login') }}" class="register-social-btn">
                        <i class="fab fa-google"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="register-right">
            <div class="register-welcome">
                <h2>Welcome back, Fur-iend!</h2>
                <p>Be Paw-sitive!</p>
                <a href="{{ route('register.page') }}" class="preload-link"
                    data-preload-url="{{ route('register.page') }}">
                    <button class="register-signin-btn">Sign Up</button>
                </a>
            </div>
            <img src="{{ asset('images/logo.png') }}" alt="Pawnder Logo" class="register-illustration" />
        </div>
    </div>

    <script>
        // Hover-triggered preloading mechanism
        class HoverPreloader {
            constructor() {
                this.preloadedUrls = new Set();
                this.hoverTimer = null;
                this.hoverDelay = 65; // Delay in milliseconds before preloading
                this.init();
            }

            init() {
                // Add event listeners to all preload links
                document.querySelectorAll('.preload-link').forEach(link => {
                    link.addEventListener('mouseenter', (e) => this.handleHover(e));
                    link.addEventListener('mouseleave', () => this.clearHoverTimer());
                });
            }

            handleHover(event) {
                const url = event.currentTarget.dataset.preloadUrl;
                if (!url || this.preloadedUrls.has(url)) return;

                // Clear any existing timer
                this.clearHoverTimer();

                // Set a timer to preload after hover delay
                this.hoverTimer = setTimeout(() => {
                    this.preloadPage(url);
                }, this.hoverDelay);
            }

            clearHoverTimer() {
                if (this.hoverTimer) {
                    clearTimeout(this.hoverTimer);
                    this.hoverTimer = null;
                }
            }

            preloadPage(url) {
                if (this.preloadedUrls.has(url)) return;

                try {
                    // Create prefetch link element
                    const prefetchLink = document.createElement('link');
                    prefetchLink.rel = 'prefetch';
                    prefetchLink.href = url;
                    prefetchLink.as = 'document';

                    // Add to head
                    document.head.appendChild(prefetchLink);

                    // Mark as preloaded
                    this.preloadedUrls.add(url);

                    console.log(`Preloaded: ${url}`);
                } catch (error) {
                    console.warn('Preload failed:', error);
                }
            }
        }

        // Initialize preloader when DOM is ready
        document.addEventListener('DOMContentLoaded', () => {
            new HoverPreloader();
        });

        function togglePasswordVisibility(inputId) {
            const passwordInput = document.getElementById(inputId);
            const toggleButton = event.currentTarget;

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                passwordInput.type = 'password';
                toggleButton.innerHTML = '<i class="fas fa-eye"></i>';
            }
        }

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

            setTimeout(() => {
                if (notification.parentNode) {
                    notification.classList.add('slide-out');
                    setTimeout(() => notification.remove(), 300);
                }
            }, 5000);
        }

        @if(session('success'))
            showNotification('{{ session('success') }}', 'success');
        @endif

        @if(session('error'))
            showNotification('{{ session('error') }}', 'error');
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                showNotification('{{ $error }}', 'error');
            @endforeach
        @endif

        document.getElementById('loginForm').addEventListener('submit', function (e) {
            const requiredFields = this.querySelectorAll('[required]');
            const loginBtn = document.getElementById('loginBtn');
            const btnText = loginBtn.querySelector('.btn-text');
            const btnLoading = loginBtn.querySelector('.btn-loading');
            let hasError = false;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    hasError = true;
                    const fieldName = field.placeholder || field.name;
                    showNotification(`Please fill in the ${fieldName} field`, 'error');
                }
            });

            if (hasError) {
                e.preventDefault();
            } else {
                // Show loading state
                btnText.style.display = 'none';
                btnLoading.style.display = 'inline-block';
                loginBtn.disabled = true;
            }
        });

        // Add page transition handling
        document.addEventListener('DOMContentLoaded', function () {
            const overlay = document.querySelector('.page-transition-overlay');
            const links = document.querySelectorAll('a[href]');

            links.forEach(link => {
                link.addEventListener('click', function (e) {
                    if (this.getAttribute('href').startsWith('{{ route('register.page') }}')) {
                        e.preventDefault();
                        const targetUrl = this.getAttribute('href');

                        // Add exit animations
                        document.querySelector('.register-left').classList.add('slide-out-left');
                        document.querySelector('.register-right').classList.add('slide-out-right');
                        document.querySelector('.register-form-container').classList.add('fade-out');

                        // Activate overlay
                        document.body.classList.add('transitioning');
                        overlay.classList.add('active');

                        // Navigate after animation
                        setTimeout(() => {
                            window.location.href = targetUrl;
                        }, 800);
                    }
                });
            });

            // Handle page load
            if (window.performance && window.performance.navigation.type === window.performance.navigation.TYPE_BACK_FORWARD) {
                overlay.classList.add('active');
                setTimeout(() => {
                    overlay.classList.remove('active');
                    document.body.classList.remove('transitioning');
                }, 100);
            }
        });
    </script>
</body>

</html>