<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    public function show()
    {
        return view('auth.verify-email');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $email = session('email');
        $cachedCode = Cache::get('verification_code_' . $email);

        if (!$cachedCode || $cachedCode !== $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code'
            ], 422);
        }

        // Retrieve registration data from session
        $registrationData = session('registration_data');
        if (!$registrationData || $registrationData['email'] !== $email) {
            return response()->json([
                'success' => false,
                'message' => 'Registration data not found or mismatched.'
            ], 404);
        }

        try {
            // Create the user
            $user = User::create([
                'first_name' => $registrationData['first_name'],
                'last_name' => $registrationData['last_name'],
                'email' => $registrationData['email'],
                'password' => Hash::make($registrationData['password']),
                'username' => strtolower($registrationData['first_name'] . $registrationData['last_name']),
                'profile_picture' => null,
                'banner_image' => null,
                'is_admin' => false,
            ]);
            $user->markEmailAsVerified();

            // Clean up session and cache
            session()->forget(['registration_data', 'email']);
            Cache::forget('verification_code_' . $email);

            // Auto-login the user
            Auth::login($user);

            return response()->json([
                'success' => true,
                'message' => 'Email verified and account created successfully',
                'redirect' => route('home')
            ]);
        } catch (\Exception $e) {
            Log::error('User creation failed after verification', [
                'error' => $e->getMessage(),
                'email' => $email
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create account. Please try again.'
            ], 500);
        }
    }

    public function resend(Request $request)
    {
        $email = session('email');
        $registrationData = session('registration_data');
        if (!$registrationData || $registrationData['email'] !== $email) {
            return response()->json([
                'success' => false,
                'message' => 'Registration data not found or mismatched.'
            ], 404);
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Cache::put('verification_code_' . $email, $code, now()->addMinutes(60));

        (new User(['email' => $email]))->notify(new CustomVerifyEmail($code));

        return response()->json([
            'success' => true,
            'message' => 'Verification code resent successfully'
        ]);
    }
}
