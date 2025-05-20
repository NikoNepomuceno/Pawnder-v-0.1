<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthService
{
    public function register(array $data)
    {
        $registrationData = array_intersect_key($data, array_flip([
            'first_name',
            'last_name',
            'email',
            'password'
        ]));

        // Generate verification code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Cache::put('verification_code_' . $data['email'], $code, now()->addMinutes(60));

        // Send verification email
        (new User(['email' => $data['email']]))->notify(new CustomVerifyEmail($code));

        // Clear rate limiter
        RateLimiter::clear('register.' . request()->ip());

        return $registrationData;
    }

    public function login(array $credentials, bool $remember = false)
    {
        $key = Str::lower($credentials['email']) . '|' . request()->ip();

        if (!Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($key, 60);
            Log::warning('Failed login attempt', [
                'email' => $credentials['email'],
                'ip' => request()->ip(),
                'reason' => 'Invalid password'
            ]);
            return false;
        }

        RateLimiter::clear($key);
        request()->session()->regenerate();

        return true;
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    public function sendPasswordResetLink(string $email)
    {
        $key = 'password.reset.' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            return false;
        }

        RateLimiter::hit($key, 60);

        return Password::sendResetLink(['email' => $email]);
    }

    public function resetPassword(array $data)
    {
        return Password::reset($data, function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();
        });
    }
}
