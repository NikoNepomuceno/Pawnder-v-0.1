<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="{{ asset('css/auth/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <title>Pawnder - Email Verification</title>
    <style>
        * {
    margin: 0;
    padding: 0;
            box-sizing: border-box;
        }

        html, body {
            width: 100%;
            min-height: 100vh;
            background: #f0f2f5;
            overflow-x: hidden;
        }

        body {
            font-family: 'Poppins', sans-serif;
    background: #f0f2f5;
            padding: 1rem;
}

.verification-container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            overflow-x: hidden;
}

.verification-content {
    background: white;
    padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.verification-logo {
            width: 100px;
            margin-bottom: 1.5rem;
}

.verification-content h2 {
            color: #1b4332;
            font-size: 1.5rem;
    margin-bottom: 1rem;
}

.verification-content p {
    color: #666;
            font-size: 0.95rem;
    margin-bottom: 1rem;
            line-height: 1.5;
}

.verification-code-inputs {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
            margin: 1.5rem 0;
}

.verification-code-inputs input {
    width: 40px;
            height: 45px;
    text-align: center;
            font-size: 1.25rem;
    border: 2px solid #ddd;
    border-radius: 8px;
    background: #f8f9fa;
            font-family: 'Poppins', sans-serif;
}

.verification-code-inputs input:focus {
    border-color: #d8f3dc;
    outline: none;
}

.verification-actions {
            margin-top: 1.5rem;
}

.verify-btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
    background: #d8f3dc;
    color: #1b4332;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
}

        .verify-btn:active {
    background: #b7e4c7;
}

.verification-footer {
            margin-top: 1.5rem;
    color: #666;
            font-size: 0.9rem;
}

.verification-footer a {
    color: #1b4332;
    text-decoration: none;
            font-weight: 500;
}

        .verification-footer a:active {
            color: #40916c;
}

        /* Mobile Optimizations */
        @media (max-width: 480px) {
            html, body {
                width: 100vw;
                min-height: 100vh;
                max-width: 100vw;
                overflow-x: hidden;
            }
            body {
                padding: 0.75rem;
                /* Remove flex for mobile to prevent scroll issues */
                display: block;
            }
            .verification-container {
                max-width: 100vw;
                overflow-x: hidden;
            }
    .verification-content {
        padding: 1.5rem;
                border-radius: 8px;
    }
    .verification-logo {
                width: 80px;
        margin-bottom: 1rem;
    }
    .verification-content h2 {
                font-size: 1.25rem;
    }
    .verification-content p {
        font-size: 0.9rem;
    }
    .verification-code-inputs {
        gap: 0.35rem;
    }
    .verification-code-inputs input {
                width: 35px;
        height: 40px;
        font-size: 1.1rem;
    }
            .verify-btn {
                padding: 10px;
                font-size: 0.95rem;
    }
    .verification-footer {
                font-size: 0.85rem;
    }
}

        /* Small Mobile Devices */
        @media (max-width: 360px) {
    .verification-content {
                padding: 1.25rem;
    }
    .verification-code-inputs input {
                width: 30px;
                height: 35px;
        font-size: 1rem;
    }
        }
    </style>
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