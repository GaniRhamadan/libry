<?php

declare(strict_types=1);

namespace RentalHub\StarterKit\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RentalHub\StarterKit\Enums\UserRole;
use RentalHub\StarterKit\Http\Requests\LoginRequest;
use RentalHub\StarterKit\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('rental.dashboard');
        }

        return view('rental-hub::auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $throttleKey = Str::transliterate(Str::lower((string) $request->input('email')) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => __('rental-hub::rental.auth_throttle', ['seconds' => $seconds]),
            ]);
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($throttleKey, 60);
            throw ValidationException::withMessages([
                'email' => __('rental-hub::rental.auth_failed'),
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        return redirect()->intended(route('rental.dashboard'));
    }

    public function showRegisterForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('rental.dashboard');
        }

        return view('rental-hub::auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $userModel = config('rental-hub.user_model', 'App\\Models\\User');

        $user = DB::transaction(function () use ($request, $userModel) {
            return $userModel::create([
                'name' => (string) $request->input('name'),
                'email' => (string) $request->input('email'),
                'phone' => (string) $request->input('phone'),
                'rental_role' => UserRole::CUSTOMER->value,
                'password' => Hash::make((string) $request->input('password')),
            ]);
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('rental.dashboard')->with('success', (string) __('rental-hub::rental.register_success'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('rental.login')->with('success', (string) __('rental-hub::rental.logout_success'));
    }
}
