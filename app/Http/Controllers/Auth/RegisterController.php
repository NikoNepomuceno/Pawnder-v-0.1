<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255|regex:/^[A-Za-z\s]+$/',
            'last_name' => 'required|string|max:255|regex:/^[A-Za-z\s]+$/',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            'password_confirmation' => 'required|same:password',
            'privacy_agreement' => 'required|accepted'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Store registration data in session (except password_confirmation)
        $registrationData = $request->only([
            'first_name', 'last_name', 'email', 'password', 'privacy_agreement'
        ]);
        session(['registration_data' => $registrationData]);

        // Generate verification code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Cache::put('verification_code_' . $request->email, $code, now()->addMinutes(60));

        // Send verification email
        (new \App\Models\User([ 'email' => $request->email ]))->notify(new CustomVerifyEmail($code));

        // Store email in session for verification page
        session(['email' => $request->email]);

        return response()->json([
            'success' => true,
            'message' => 'Verification code sent. Please verify your email.',
            'redirect' => route('verify.email.page')
        ]);
    }
} 