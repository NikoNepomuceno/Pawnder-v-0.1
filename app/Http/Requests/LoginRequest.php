<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }

    protected function prepareForValidation()
    {
        $key = Str::lower($this->input('email')) . '|' . $this->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => __('Too many login attempts. Please try again in :seconds seconds.', [
                    'seconds' => RateLimiter::availableIn($key),
                ]),
            ]);
        }

        $user = User::where('email', $this->email)->first();

        if (!$user) {
            RateLimiter::hit($key, 60);
            throw ValidationException::withMessages([
                'email' => 'No account found with this email address.'
            ]);
        }

        if ($user->is_banned) {
            throw ValidationException::withMessages([
                'email' => 'This account has been banned. Please contact support.'
            ]);
        }
    }
}
