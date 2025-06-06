<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Prompts\Clear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Notifications\CustomVerifyEmail;
use App\Services\AuthService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Handles user authentication including registration, login, logout, and password reset.
 */
class AuthController extends Controller
{
    /**
     * The authentication service instance.
     *
     * @var \App\Services\AuthService
     */
    protected AuthService $authService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\AuthService  $authService
     * @return void
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Show the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegister(): View
    {
        return view('auth.Register');
    }

    /**
     * Handle user registration.
     *
     * @param  \App\Http\Requests\RegisterRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        try {
            $registrationData = $this->authService->register($request->validated());

            Session::put('registration_data', $registrationData);
            Session::put('email', $request->validated('email'));

            return redirect()->route('verify.email.page')
                ->with('success', 'Please check your email for the verification code.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLogin(): View
    {
        return view('auth.Login');
    }

    /**
     * Handle user login.
     *
     * @param  \App\Http\Requests\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        try {
            if ($this->authService->login($request->validated(), $request->has('remember'))) {
                return redirect()->route('home')->with('success', 'Successfully logged in!');
            }

            return back()->withErrors(['email' => 'The password you entered is incorrect.']);
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'An error occurred during login. Please try again.']);
        }
    }

    /**
     * Handle user logout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        $this->authService->logout();
        return redirect('/')->with('success', 'Logged out successfully!');
    }

    /**
     * Show the forgot password form.
     *
     * @return \Illuminate\View\View
     */
    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.exists' => 'No account found with this email address.'
        ]);

        // Check rate limiting
        $key = 'password.reset.' . $request->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 3)) {
            return back()->withErrors(['email' => 'Too many password reset attempts. Please try again in ' . \Illuminate\Support\Facades\RateLimiter::availableIn($key) . ' seconds.']);
        }

        if ($this->authService->sendPasswordResetLink($request->string('email'))) {
            return back()->with('status', 'Password reset link sent to your email address.');
        }

        return back()->withErrors(['email' => 'Unable to send password reset link. Please try again later.']);
    }

    /**
     * Show the password reset form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function showResetPassword(Request $request): View
    {
        return view('auth.reset-password', ['token' => $request->string('token')]);
    }

    /**
     * Handle password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
                'confirmed'
            ],
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one number, and one special character (@$!%*?&)'
        ]);

        $status = $this->authService->resetPassword($request->only(
            'email',
            'password',
            'password_confirmation',
            'token'
        ));

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
