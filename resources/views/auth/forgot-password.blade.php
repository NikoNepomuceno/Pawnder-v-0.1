<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Pawnder</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/auth/forgot-password.css') }}">
</head>

<body>
    <div class="container">
        <img src="{{ asset('images/logo.png') }}" alt="Pawnder Logo" class="logo-img">
        <h1>Forgot Paws-word?</h1>
        <p class="subtext">Please enter your email Address to receive the<br>Password Reset Link</p>
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="alert alert-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" id="resetBtn">
                <span class="btn-text">Send Password Reset Link</span>
                <span class="btn-loading" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i> Sending...
                </span>
            </button>
        </form>

        <div class="back-to-login">
            <a href="{{ route('login') }}">Back to Login</a>
        </div>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function (e) {
            const resetBtn = document.getElementById('resetBtn');
            const btnText = resetBtn.querySelector('.btn-text');
            const btnLoading = resetBtn.querySelector('.btn-loading');
            const emailInput = document.getElementById('email');

            // Basic email validation
            if (!emailInput.value.trim()) {
                e.preventDefault();
                return;
            }

            // Show loading state
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline-block';
            resetBtn.disabled = true;
        });
    </script>
</body>

</html>