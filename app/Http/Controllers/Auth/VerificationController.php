<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
    public function notice(Request $request): RedirectResponse|View
    {
        $default = $request->user()->role === 'visitor'
            ? route('visits.myvisits', absolute: false)
            : route('dashboard', absolute: false);

        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended($default)
                    : view('auth.verify-email');
    }

    public function store(Request $request): RedirectResponse
    {
        $default = $request->user()->role === 'visitor'
            ? route('visits.myvisits', absolute: false)
            : route('dashboard', absolute: false);

        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended($default);
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        $default = $request->user()->role === 'visitor'
            ? route('visits.myvisits', absolute: false)
            : route('dashboard', absolute: false);

        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended($default.'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            /** @var MustVerifyEmail $user */
            $user = $request->user();

            event(new Verified($user));
        }

        return redirect()->intended($default.'?verified=1');
    }
}
