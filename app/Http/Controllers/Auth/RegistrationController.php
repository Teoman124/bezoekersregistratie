<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'visitor'; // Geef de nieuwe gebruiker een standaard rol

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        return redirect(route('home', absolute: false))->with('success', 'Welkom! Je account is succesvol aangemaakt.');
    }

    public function createVisitor(): View
    {
        return view('auth.visitor-register');
    }

    public function storeVisitor(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:'.User::class.',name'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => 'visitor+'.Str::uuid().'@anonymous.local',
            'password' => Str::random(40),
            'role' => 'visitor',
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('visitor.company-info', absolute: false));
    }

    public function createCompanyInfo(): View
    {
        return view('auth.visitor-company-info');
    }

    public function storeCompanyInfo(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
        ]);

        $user = $request->user();
        $user->visitor()->updateOrCreate([], $validated);

        $request->session()->forget('visitor_company_prompt_skipped');

        return redirect(route('home', absolute: false))->with('success', 'Bedrijfinformatie opgeslagen!');
    }

    public function skipCompanyInfo(Request $request): RedirectResponse
    {
        $request->session()->put('visitor_company_prompt_skipped', true);

        return redirect(route('home', absolute: false));
    }
}
