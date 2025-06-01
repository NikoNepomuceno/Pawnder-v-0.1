<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Services\ProfilePictureService;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class GoogleController extends Controller
{
    protected ProfilePictureService $profilePictureService;

    public function __construct(ProfilePictureService $profilePictureService)
    {
        $this->profilePictureService = $profilePictureService;
    }

    public function redirectToGoogle(Request $request)
    {
        // Rate limiting for OAuth redirects
        $key = 'google_oauth_redirect:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);
            return redirect()->route('login')
                ->with('error', "Too many OAuth attempts. Please try again in {$seconds} seconds.");
        }

        RateLimiter::hit($key, 300); // 5 minutes

        // Regenerate session to prevent session fixation
        session()->regenerate();

        // Store state for additional validation
        $state = Str::random(40);
        session(['oauth_state' => $state]);

        return Socialite::driver('google')
            ->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            // Rate limiting for OAuth callbacks
            $key = 'google_oauth_callback:' . $request->ip();
            if (RateLimiter::tooManyAttempts($key, 20)) {
                $seconds = RateLimiter::availableIn($key);
                return redirect()->route('login')
                    ->with('error', "Too many OAuth attempts. Please try again in {$seconds} seconds.");
            }

            RateLimiter::hit($key, 300); // 5 minutes

            $googleUser = Socialite::driver('google')->user();

            // Validate required Google user data
            if (!$this->validateGoogleUserData($googleUser)) {
                throw new \Exception('Invalid or incomplete Google user data received');
            }

            // Parse name more robustly
            $name = $googleUser->getName();
            $nameParts = explode(' ', trim($name), 2);
            $firstName = $nameParts[0] ?? $name;
            $lastName = $nameParts[1] ?? '';

            // Sanitize names
            $firstName = $this->sanitizeName($firstName);
            $lastName = $this->sanitizeName($lastName);

            // Check if user exists
            $existingUser = User::where('email', $googleUser->getEmail())->first();

            $username = null;
            if (!$existingUser) {
                // Generate a unique username from email for new users only
                $username = $this->generateUniqueUsername($googleUser->getEmail());
            }

            // Process profile picture
            $profilePictureUrl = null;
            if ($googleUser->getAvatar()) {
                $profilePictureUrl = $this->profilePictureService->processGoogleProfilePicture(
                    $googleUser->getAvatar(),
                    $existingUser ? (string)$existingUser->id : 'temp_' . time(),
                    $existingUser?->profile_picture
                );
            }

            // Use database transaction for data consistency
            $user = DB::transaction(function () use ($googleUser, $firstName, $lastName, $username, $existingUser, $profilePictureUrl) {
                return User::updateOrCreate(
                    ['email' => $googleUser->getEmail()],
                    [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'username' => $existingUser ? $existingUser->username : $username, // Preserve existing username
                        'email_verified_at' => now(),
                        'password' => bcrypt(Str::random(16)),
                        'profile_picture' => $profilePictureUrl ?? $existingUser?->profile_picture,
                        'google_id' => $googleUser->getId(),
                        'google_token' => $googleUser->token,
                        'google_refresh_token' => $googleUser->refreshToken ?? null,
                    ]
                );
            });

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

    /**
     * Validate Google user data
     *
     * @param mixed $googleUser
     * @return bool
     */
    private function validateGoogleUserData($googleUser): bool
    {
        return $googleUser &&
               $googleUser->getEmail() &&
               $googleUser->getName() &&
               $googleUser->getId() &&
               filter_var($googleUser->getEmail(), FILTER_VALIDATE_EMAIL);
    }

    /**
     * Sanitize name input
     *
     * @param string $name
     * @return string
     */
    private function sanitizeName(string $name): string
    {
        // Remove any non-letter characters except spaces, hyphens, and apostrophes
        $sanitized = preg_replace('/[^a-zA-Z\s\-\']/u', '', $name);

        // Trim and limit length
        return substr(trim($sanitized), 0, 50);
    }

    /**
     * Generate unique username from email
     *
     * @param string $email
     * @return string
     */
    private function generateUniqueUsername(string $email): string
    {
        $baseUsername = strtolower(explode('@', $email)[0]);

        // Remove any non-alphanumeric characters except underscores
        $baseUsername = preg_replace('/[^a-z0-9_]/', '', $baseUsername);

        // Ensure minimum length
        if (strlen($baseUsername) < 3) {
            $baseUsername = 'user_' . $baseUsername;
        }

        // Limit length
        $baseUsername = substr($baseUsername, 0, 20);

        $username = $baseUsername;
        $counter = 1;

        // Keep trying until we find a unique username
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . '_' . $counter;
            $counter++;

            // Prevent infinite loop
            if ($counter > 9999) {
                $username = $baseUsername . '_' . time();
                break;
            }
        }

        return $username;
    }
}
