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
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <title>Pawnder - Register</title>
</head>
<body>
    <div class="page-transition-overlay"></div>
    <div class="register-split">
        <div class="register-left">
            <div class="register-form-container">
                <div class="register-title">Sign up</div>
                <form method="POST" action="{{ route('register.submit') }}" class="register-form" id="signupForm" onsubmit="setUsername()">
                    @csrf
                    <input type="hidden" name="username" id="username">
                    <div class="register-input-wrapper">
                        <span class="register-input-icon"><i class="fas fa-user"></i></span>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" class="register-input" placeholder="First Name" required pattern="[A-Za-z\s]+" title="Only letters and spaces are allowed">
                    </div>
                    <div class="register-input-wrapper">
                        <span class="register-input-icon"><i class="fas fa-user"></i></span>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" class="register-input" placeholder="Last Name" required pattern="[A-Za-z\s]+" title="Only letters and spaces are allowed">
                    </div>
                    <div class="register-input-wrapper">
                        <span class="register-input-icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" id="reg_email" name="email" value="{{ old('email') }}" class="register-input" placeholder="Email" required>
                    </div>
                    <div class="register-input-wrapper">
                        <span class="register-input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" id="reg_password" name="password" class="register-input" placeholder="Password" required pattern="^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" title="Password must contain at least 8 characters, one uppercase letter, one number, and one special character">
                        <button type="button" class="password-toggle" onclick="togglePasswordVisibility('reg_password')"><i class="fas fa-eye"></i></button>
                    </div>
                    <div class="register-input-wrapper">
                        <span class="register-input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="register-input" placeholder="Confirm Password" required>
                        <button type="button" class="password-toggle" onclick="togglePasswordVisibility('password_confirmation')"><i class="fas fa-eye"></i></button>
                    </div>
                    <div class="privacy-policy">
                        <input type="checkbox" id="privacy_agreement" name="privacy_agreement" required>
                        <label for="privacy_agreement">I agree to the <a href="#" onclick="showPrivacyPolicy()">Privacy Policy</a></label>
                    </div>
                    <button type="submit" class="register-btn">Sign Up</button>
                </form>
                <div class="register-or">Or Sign up with Others</div>
                <div class="register-socials">
                    <a href="{{ route('google.login') }}" class="register-social-btn"><i class="fab fa-google"></i></a>
                </div>
            </div>
        </div>
        <div class="register-right">
            <div class="register-welcome">
                <h2>Welcome back, Fur-iend!</h2>
                <p>Be Paw-sitive!</p>
                <a href="{{ route('login') }}"><button class="register-signin-btn">Sign In</button></a>
            </div>
            <img src="{{ asset('images/logo.png') }}" alt="Pawnder Logo" class="register-illustration" />
        </div>
    </div>

    <!-- Privacy Policy Modal -->
    <div id="privacyModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-shield-alt"></i> Privacy Policy</h2>
            </div>
            <div class="modal-body">
                <p><i class="fas fa-handshake"></i> Welcome to Pawnder! Your privacy is important to us. By using our
                    platform, you agree to this Privacy Policy and to the Data Privacy Act of 2012 (RA 10173) of the
                    Philippines.</p>
                <div class="modal-columns">
                    <div class="modal-column">
                        <h3><i class="fas fa-database"></i> Information We Collect</h3>
                        <p>We may collect the following when you use Pawnder:</p>
                        <ul class="privacy-list">
                            <li><i class="fas fa-user"></i> Your name, email, and phone number (when you sign up or post a listing)</li>
                            <li><i class="fas fa-paw"></i> Details about lost or found pets (photos, descriptions, locations)</li>
                            <li><i class="fas fa-map-marker-alt"></i> Your location (if permission is granted)</li>
                            <li><i class="fas fa-laptop"></i> Technical info (IP address, browser type, etc.)</li>
                        </ul>
                        <h3><i class="fas fa-tasks"></i> How We Use Your Data</h3>
                        <p>We use your information to:</p>
                        <ul class="privacy-list">
                            <li><i class="fas fa-heart"></i> Help reunite lost pets with their owners</li>
                            <li><i class="fas fa-map"></i> Display pet listings based on your location</li>
                            <li><i class="fas fa-chart-line"></i> Improve our services and platform</li>
                            <li><i class="fas fa-bell"></i> Notify you of updates or potential pet matches</li>
                        </ul>
                        <h3><i class="fas fa-share-alt"></i> Data Sharing</h3>
                        <ul class="privacy-list">
                            <li><i class="fas fa-globe"></i> Public Listings: Pet-related info you share may be visible to others to assist in recovery.</li>
                            <li><i class="fas fa-handshake"></i> Third-Party Services: We may share data with trusted partners (e.g., for hosting or email delivery).</li>
                            <li><i class="fas fa-gavel"></i> Legal Compliance: We may disclose information if required by law or to protect our users.</li>
                        </ul>
                    </div>
                    <div class="modal-column">
                        <h3><i class="fas fa-user-shield"></i> Your Rights</h3>
                        <p>Under the Data Privacy Act, you may:</p>
                        <ul class="privacy-list">
                            <li><i class="fas fa-eye"></i> Access or update your data</li>
                            <li><i class="fas fa-trash-alt"></i> Request account deletion</li>
                            <li><i class="fas fa-undo"></i> Withdraw consent at any time</li>
                            <li><i class="fas fa-file-alt"></i> File a complaint with the National Privacy Commission</li>
                        </ul>
                        <p><i class="fas fa-envelope"></i> To make any request, email us at <a href="mailto:support@pawnder.com">support@pawnder.com</a></p>
                        <h3><i class="fas fa-lock"></i> Data Security</h3>
                        <p><i class="fas fa-shield-alt"></i> We take steps to protect your data, but no system is 100% secure. Please safeguard your own information as well.</p>
                        <h3><i class="fas fa-child"></i> Children's Privacy</h3>
                        <p><i class="fas fa-exclamation-circle"></i> Pawnder is not intended for users under 13 years old. We do not knowingly collect their data.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="closePrivacyModal()" class="modal-btn" id="privacyBtn"><i class="fas fa-check"></i> I Understand</button>
            </div>
        </div>
    </div>
    <script>
        // Notification function
        function showNotification(message, type = 'success') {
            const container = document.getElementById('notificationContainer');
            if (!container) {
                const newContainer = document.createElement('div');
                newContainer.id = 'notificationContainer';
                newContainer.className = 'notification-container';
                document.body.appendChild(newContainer);
            }
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
        // Password visibility toggle function
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
        // Set username before form submission
        function setUsername() {
            const firstName = document.getElementById('first_name').value.trim().toLowerCase();
            const lastName = document.getElementById('last_name').value.trim().toLowerCase();
            if (firstName && lastName) {
                const username = (firstName + lastName).replace(/\s+/g, '');
                document.getElementById('username').value = username;
            }
            return true;
        }
        // Privacy Policy Modal Functions
        function showPrivacyPolicy() {
            const modal = document.getElementById('privacyModal');
            if (!modal) {
                console.error('Privacy modal element not found');
                return;
            }
            modal.style.display = 'flex';
        }
        function closePrivacyModal() {
            const modal = document.getElementById('privacyModal');
            if (!modal) {
                console.error('Modal element not found when closing');
                return;
            }
            modal.style.display = 'none';
        }
        // Form validation
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            const privacyCheckbox = document.getElementById('privacy_agreement');
            if (!privacyCheckbox.checked) {
                e.preventDefault();
                showNotification('Please agree to the Privacy Policy to continue.', 'error');
            }
        });
        // Show session messages
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

        // Add page transition handling
        document.addEventListener('DOMContentLoaded', function() {
            const overlay = document.querySelector('.page-transition-overlay');
            const links = document.querySelectorAll('a[href]');
            
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (this.getAttribute('href').startsWith('{{ route('login') }}')) {
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