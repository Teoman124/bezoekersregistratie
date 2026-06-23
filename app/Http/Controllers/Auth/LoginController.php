<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $this->ensureIsNotRateLimited($request);

        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey($request));

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey($request));

        $request->session()->regenerate();

        if ($request->user()?->role === 'visitor') {
            return redirect()->route('visits.myvisits');
        }

        return redirect()->intended(route('dashboard', absolute: false))->with('success', 'Je bent succesvol ingelogd!');
    }

    public function createVisitor(): View
    {
        return view('auth.visitor-login');
    }

    public function storeVisitor(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
        ]);

        $this->ensureVisitorIsNotRateLimited($request);

        $user = User::query()
            ->where('role', 'visitor')
            ->where('name', $validated['name'])
            ->first();

        if (! $user) {
            RateLimiter::hit($this->visitorThrottleKey($request));

            throw ValidationException::withMessages([
                'name' => trans('auth.failed'),
            ]);
        }

        Auth::login($user, false);

        RateLimiter::clear($this->visitorThrottleKey($request));
        $request->session()->regenerate();

        return redirect()->route('visits.myvisits');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', __('You have been logged out.'));
    }

    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function ensureVisitorIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->visitorThrottleKey($request), 5)) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($this->visitorThrottleKey($request));

        throw ValidationException::withMessages([
            'name' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->string('email')).'|'.$request->ip());
    }

    public function visitorThrottleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->string('name')).'|'.$request->ip());
    }
}
