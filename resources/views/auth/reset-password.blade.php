<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Pawnder</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth/reset-password.css') }}">
</head>

<body>
    <div class="container">
        <img src="{{ asset('images/logo.png') }}" alt="Pawnder Logo" class="logo-img">
        <h1>Reset Paws-word</h1>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            {{-- <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="alert alert-error">
                        {{ $message }}
                    </div>
                @enderror
            </div> --}}

            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" required>
                @error('password')
                    <div class="alert alert-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
                @error('password_confirmation')
                    <div class="alert alert-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit">
                Reset Password
            </button>
        </form>
    </div>
</body>

</html>