<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[\p{L}\s\'\-]+$/u'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[\p{L}\s\'\-]+$/u'],
            'email' => 'required|email|unique:users',
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
                'confirmed'
            ],
            'privacy_agreement' => 'required|accepted'
        ];
    }

    public function messages()
    {
        return [
            'first_name.regex' => 'First name can only contain letters, spaces, hyphens, and apostrophes',
            'last_name.regex' => 'Last name can only contain letters, spaces, hyphens, and apostrophes',
            'password.regex' => 'Password must contain at least one uppercase letter, one number, and one special character (@$!%*?&)',
            'privacy_agreement.required' => 'You must agree to the privacy policy to register'
        ];
    }

    protected function prepareForValidation()
    {
        $key = 'register.' . $this->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => __('Too many registration attempts. Please try again in :seconds seconds.', [
                    'seconds' => RateLimiter::availableIn($key),
                ]),
            ]);
        }
    }
}
