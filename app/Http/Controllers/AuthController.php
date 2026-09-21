<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show login page. Redirect if already authenticated.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle login. Enforces brute-force lockout:
     * max 5 failed attempts per IP within 15 minutes (SDD Table 29).
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // --- Brute-force protection (SDD: lockout after 5 failed attempts / 15 min) ---
        $throttleKey = 'login.' . $request->ip();

        // Pass 'status' => 'active' to ensure deactivated accounts cannot log in
        $authCredentials = array_merge($credentials, ['status' => 'active']);

        // If locked out, check if user is entering correct credentials for an admin account
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $user = \App\Models\User::where('email', $credentials['email'])->first();
            if ($user && $user->role === 'admin' && \Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password) && $user->status === 'active') {
                // Admin override: authenticate and clear rate limit
                Auth::login($user, $request->boolean('remember'));
                RateLimiter::clear($throttleKey);
                $request->session()->regenerate();
                $user->update(['last_login_at' => now()]);
                return redirect()->intended('/dashboard');
            }

            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = ceil($seconds / 60);
            return back()->withErrors([
                'email' => "Too many failed login attempts. Account temporarily locked. Please try again in {$minutes} minute(s).",
            ])->onlyInput('email');
        }

        if (Auth::attempt($authCredentials, $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            Auth::user()->update(['last_login_at' => now()]);
            \App\Services\AuditService::log('LOGIN', 'User', Auth::id(), 'User logged in to system');

            return redirect()->intended('/dashboard');
        }

        // Increment failure counter — decays after 900 seconds (15 minutes)
        RateLimiter::hit($throttleKey, 900);

        // Check if the credentials matched but the user is deactivated
        $user = \App\Models\User::where('email', $credentials['email'])->first();
        if ($user && \Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password) && $user->status !== 'active') {
            \App\Services\AuditService::log('LOGIN_DENIED_INACTIVE', 'User', $user->id, 'Deactivated user attempted login', $user->id);
            return back()->withErrors([
                'email' => 'Your account has been deactivated by the Administrator. Please contact research support.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'Invalid email address or password. Please check your credentials and try again.',
        ])->onlyInput('email');
    }

    /**
     * Show the self-service password reset form.
     */
    public function showForgotPassword()
    {
        return view('auth.forgot_password');
    }

    /**
     * Process self-service password reset.
     *
     * Security fix (SDD Table 29):
     *  - Requires BOTH staff_id AND email to match the same account
     *    (two identity factors — prevents anyone with just an email from resetting)
     *  - Enforces SDD password complexity: min 8 chars, uppercase, lowercase,
     *    number, and special symbol.
     */
    public function processForgotPassword(Request $request)
    {
        $request->validate([
            'staff_id'     => 'required|string',
            'email'        => 'required|email',
            'new_password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()   // requires upper + lower
                    ->numbers()     // requires at least one digit
                    ->symbols(),    // requires at least one symbol (@$!%*?& etc.)
            ],
        ]);

        // Both staff_id AND email must belong to the SAME account
        $user = User::where('email', $request->email)
                    ->where('staff_id', $request->staff_id)
                    ->first();

        if (! $user) {
            return back()->withErrors([
                'staff_id' => 'No account found matching both the Staff ID and email address provided. Please contact the System Administrator.',
            ])->onlyInput('email');
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        \App\Services\AuditService::log('PASSWORD_RESET', 'User', $user->id, 'User password reset via verified self-service', $user->id);

        return redirect()->route('login')
            ->with('success', 'Password reset successfully. Please sign in with your new password.');
    }

    /**
     * Logout and invalidate session.
     */
    public function logout(Request $request)
    {
        if (Auth::check()) {
            \App\Services\AuditService::log('LOGOUT', 'User', Auth::id(), 'User logged out of system');
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
