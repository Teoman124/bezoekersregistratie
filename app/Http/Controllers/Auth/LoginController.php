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

        if (auth()->user()->role === 'visitor') {
            $intended = $request->session()->get('url.intended');

            if ($intended && str_contains(parse_url($intended, PHP_URL_PATH), '/dashboard')) {
                $request->session()->forget('url.intended');
            }

            $redirectUrl = route('home', absolute: false);
        } else {
            $redirectUrl = route('dashboard', absolute: false);
        }

        return redirect()->intended($redirectUrl)->with('success', 'Je bent succesvol ingelogd!');
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

        return redirect()->intended(route('home', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
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
