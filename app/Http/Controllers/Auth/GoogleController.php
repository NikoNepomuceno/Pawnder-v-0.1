<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use GuzzleHttp\Exception\ClientException;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        // Regenerate session to prevent session fixation
        session()->regenerate();

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Parse name more robustly
            $name = $googleUser->getName();
            $nameParts = explode(' ', $name, 2);
            $firstName = $nameParts[0] ?? $name;
            $lastName = $nameParts[1] ?? '';

            // Get the user's profile picture from Google
            $profilePicture = $googleUser->getAvatar();

            // Check if user exists
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Generate a unique username from email for new users only
                $email = $googleUser->getEmail();
                $username = strtolower(explode('@', $email)[0]);
                $baseUsername = $username;
                $counter = 1;

                // Keep trying until we find a unique username
                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername . $counter;
                    $counter++;
                }
            }

            // Use updateOrCreate to keep profile info in sync
            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $user ? $user->username : $username, // Preserve existing username
                    'email_verified_at' => now(),
                    'password' => bcrypt(Str::random(16)),
                    'profile_picture' => $user ? $user->profile_picture : $profilePicture, // Preserve existing profile picture
                    'google_id' => $googleUser->getId(),
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken ?? null,
                ]
            );

            // Log the user in
            Auth::login($user, true);

            // Redirect to home with success message
            return redirect()->intended(route('home'))->with('success', 'Successfully logged in with Google!');
        } catch (InvalidStateException $e) {
            Log::error('Google OAuth state validation failed', [
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            return redirect()->route('login')->with('error', 'Your Google session expired. Please try again.');
        } catch (ClientException $e) {
            Log::error('Google API error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            return redirect()->route('login')->with('error', 'Could not connect to Google. Please try again.');
        } catch (\Exception $e) {
            Log::error('Google login error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'exception' => get_class($e)
            ]);
            return redirect()->route('login')->with('error', 'Google login failed. Please try again.');
        }
    }
}
