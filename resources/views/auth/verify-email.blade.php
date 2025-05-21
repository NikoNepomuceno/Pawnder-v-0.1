<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css'])
    {{-- <link rel="stylesheet" href="{{ asset('css/auth/auth.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/verify-email.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <title>Pawnder - Email Verification</title>
</head>
<body>
    <!-- Notification Container -->
    <div class="notification-container" id="notificationContainer"></div>
    
    <div class="verification-container">
        <div class="verification-content">
            <img src="{{ asset('images/logo.png') }}" alt="Pawnder Logo" class="verification-logo">
            <h2>Verify Your Email</h2>
            <p>We've sent a verification code to <strong>{{ session('email') }}</strong></p>
            <p>Please check your email and enter the code below to complete your registration.</p>
            
            <form id="verificationForm" method="POST" action="{{ route('verify.email') }}" class="verification-form">
                @csrf
                <div class="verification-code-inputs">
                    <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                    <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                    <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                    <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                    <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                    <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                </div>
                
                <div class="verification-actions">
                    <button type="submit" class="btn verify-btn">VERIFY EMAIL</button>
                </div>
            </form>
            
            <div class="verification-footer">
                <p>Didn't receive the code? <a href="#" onclick="resendCode()">Resend</a></p>
                <p>Wrong email? <a href="{{ route('login') }}">Go back</a></p>
            </div>
        </div>
    </div>

    <script>
// Handle verification code input
const inputs=document.querySelectorAll('.verification-code-inputs input');

inputs.forEach((input, index)=> {

        // Handle input event
        input.addEventListener('input', (e)=> {
                // Only allow numbers
                e.target.value=e.target.value.replace(/[^0-9]/g, '');

                // If we have a value and there's a next input, move to it
                if (e.target.value.length===1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            }

        );

        // Handle keydown event for backspace
        input.addEventListener('keydown', (e)=> {
                if (e.key==='Backspace'&& !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }
            }

        );

        // Handle paste event
        input.addEventListener('paste', (e)=> {
                e.preventDefault();
                const pastedData=e.clipboardData.getData('text').replace(/[^0-9]/g, '');
                const digits=pastedData.split('').slice(0, 6);

                digits.forEach((digit, i)=> {
                        if (inputs[i]) {
                            inputs[i].value=digit;
                        }
                    }

                );

                // Focus the next empty input or the last input
                const nextEmptyIndex=digits.length;

                if (nextEmptyIndex < inputs.length) {
                    inputs[nextEmptyIndex].focus();
                }

                else {
                    inputs[inputs.length - 1].focus();
                }
            }

        );
    }

);

// Handle form submission
document.getElementById('verificationForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const code=Array.from(inputs).map(input=> input.value).join('');

        // Show loading state
        const verifyBtn=document.querySelector('.verify-btn');
        verifyBtn.disabled=true;
        verifyBtn.textContent='Verifying...';

        // Submit the verification code
        fetch('{{ route("verify.email") }}', {

                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }

                ,
                body: JSON.stringify( {
                        code: code
                    }

                )
            }

        ) .then(response=> response.json()) .then(data=> {
                if (data.success) {
                    showNotification('Email verified successfully!', 'success');
                    window.location.href=data.redirect || '/dashboard';
                }

                else {
                    showNotification(data.message || 'Invalid verification code', 'error');
                }
            }

        ) .catch(error=> {
                showNotification('An error occurred. Please try again.', 'error');
            }

        ) .finally(()=> {
                verifyBtn.disabled=false;
                verifyBtn.textContent='Verify Email';
            }

        );
    }

);

// Handle resend code
function resendCode() {
    const resendBtn=document.querySelector('.resend-btn');
    resendBtn.disabled=true;
    resendBtn.textContent='Sending...';

    fetch('{{ route("resend.verification") }}', {

            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }

    ) .then(response=> response.json()) .then(data=> {
            if (data.success) {
                showNotification('New verification code sent!', 'success');
            }

            else {
                showNotification(data.message || 'Failed to send new code', 'error');
            }
        }

    ) .catch(error=> {
            showNotification('An error occurred. Please try again.', 'error');
        }

    ) .finally(()=> {
            resendBtn.disabled=false;
            resendBtn.textContent='Resend Code';
        }

    );
}

// Notification function
function showNotification(message, type='success') {
    const container=document.getElementById('notificationContainer');
    const notification=document.createElement('div');

    notification.className=`notification $ {
        type
    }

    `;

    const messageSpan=document.createElement('span');
    messageSpan.textContent=message;

    const closeBtn=document.createElement('button');
    closeBtn.className='close-btn';
    closeBtn.innerHTML='&times;';

    closeBtn.onclick=()=> {
        notification.classList.add('slide-out');
        setTimeout(()=> notification.remove(), 300);
    }

    ;

    notification.appendChild(messageSpan);
    notification.appendChild(closeBtn);
    container.appendChild(notification);

    setTimeout(()=> {
            if (notification.parentNode) {
                notification.classList.add('slide-out');
                setTimeout(()=> notification.remove(), 300);
            }
        }

        , 5000);
}
    </script>
</body>
</html>