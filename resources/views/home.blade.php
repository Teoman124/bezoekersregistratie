<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Reception & Visitor Registration') }} — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-950 text-slate-100 antialiased">
    <div class="min-h-screen bg-[radial-gradient(circle_at_top,rgba(59,130,246,0.18),transparent_28%),radial-gradient(circle_at_top_right,rgba(16,185,129,0.15),transparent_30%),linear-gradient(to_bottom,#020617,#0f172a)]">
        <header class="border-b border-white/10">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-6 lg:px-8">
                <div>
                    <p class="text-xs uppercase tracking-[0.24em] text-sky-300/80">{{ __('Visitor registration') }}</p>
                    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-white sm:text-4xl">{{ __('Smarter reception, safer check-in.') }}</h1>
                </div>
                <div class="flex items-center gap-3">
                    <x-language-switcher />
                    <a href="{{ route('login') }}" class="rounded-full border border-slate-500/40 bg-white/5 px-4 py-2 text-sm text-slate-100 transition hover:border-sky-300 hover:bg-slate-800">{{ __('Employee sign in') }}</a>
                    <a href="{{ route('visitor.login') }}" class="rounded-full bg-sky-500 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-sky-500/20 transition hover:bg-sky-400">{{ __('Visitor sign in') }}</a>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-6 py-12 sm:px-8 lg:py-16">
            <section class="grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
                <div class="space-y-8">
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-8 shadow-[0_20px_70px_-40px_rgba(15,23,42,0.75)]">
                        <p class="text-sm uppercase tracking-[0.24em] text-sky-300">{{ __('Project overview') }}</p>
                        <p class="mt-4 text-slate-200 leading-7">{{ __('A company wants to modernize the visitor process. Visitors currently sign their name in a paper log at reception. That is unprofessional, hard to read, and in an emergency nobody knows who is in the building.') }}</p>
                        <p class="mt-4 text-slate-300 leading-7">{{ __('The client wants a digital system where visitors are registered neatly, the recipient is notified automatically, and a real-time overview is available of who is inside the building.') }}</p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-6">
                            <h2 class="text-xl font-semibold text-white">{{ __('Functional requirements') }}</h2>
                            <ul class="mt-5 space-y-3 text-slate-300 text-sm leading-7">
                                <li><span class="font-semibold text-sky-300">{{ __('Receptionist') }}:</span> {{ __('check in, check out, real-time overview, and history.') }}</li>
                                <li><span class="font-semibold text-sky-300">{{ __('Employee') }}:</span> {{ __('email notifications and viewing their own visits.') }}</li>
                                <li><span class="font-semibold text-sky-300">{{ __('Admin') }}:</span> {{ __('accounts, export, and emergency list.') }}</li>
                            </ul>
                        </div>
                        <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-6">
                            <h2 class="text-xl font-semibold text-white">{{ __('Technical requirements') }}</h2>
                            <ul class="mt-5 space-y-3 text-slate-300 text-sm leading-7">
                                <li>Laravel ({{ __('latest stable version') }})</li>
                                <li>{{ __('Database with users, visits, and visitors') }}</li>
                                <li>{{ __('Authentication and role-based authorization') }}</li>
                                <li>{{ __('Email notifications and real-time attendance list') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-white/10 bg-white/5 p-8 shadow-[0_40px_120px_-40px_rgba(15,23,42,0.8)] backdrop-blur-xl">
                    <div class="space-y-6">
                        <div class="flex items-start gap-4 text-slate-200">
                            <span class="inline-flex h-12 w-12 items-center justify-center rounded-3xl bg-sky-500/15 text-sky-300">01</span>
                            <div>
                                <h3 class="text-xl font-semibold">{{ __('Fast registration') }}</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-400">{{ __('Visitors are registered immediately with all required fields.') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 text-slate-200">
                            <span class="inline-flex h-12 w-12 items-center justify-center rounded-3xl bg-sky-500/15 text-sky-300">02</span>
                            <div>
                                <h3 class="text-xl font-semibold">{{ __('Automatic alert') }}</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-400">{{ __('Recipients automatically receive a message as soon as their visitor checks in.') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 text-slate-200">
                            <span class="inline-flex h-12 w-12 items-center justify-center rounded-3xl bg-sky-500/15 text-sky-300">03</span>
                            <div>
                                <h3 class="text-xl font-semibold">{{ __('Real-time emergency list') }}</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-400">{{ __('See who is still inside, with name, company, and contact person.') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 text-slate-200">
                            <span class="inline-flex h-12 w-12 items-center justify-center rounded-3xl bg-sky-500/15 text-sky-300">04</span>
                            <div>
                                <h3 class="text-xl font-semibold">{{ __('Safe and professional') }}</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-400">{{ __('A visitor process that looks polished and remains traceable at all times.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mt-16 grid gap-10 lg:grid-cols-3">
                <article class="rounded-3xl border border-white/10 bg-white/5 p-8 shadow-xl shadow-slate-950/20">
                    <h3 class="text-xl font-semibold text-white">{{ __('Receptionist') }}</h3>
                    <ul class="mt-6 space-y-4 text-slate-300">
                        <li>{{ __('Capture name, company, contact person, and reason for visit.') }}</li>
                        <li>{{ __('Check in and out with precise timestamps.') }}</li>
                        <li>{{ __('Real-time list of all present visitors.') }}</li>
                        <li>{{ __('View history from yesterday or the past week.') }}</li>
                    </ul>
                </article>
                <article class="rounded-3xl border border-white/10 bg-white/5 p-8 shadow-xl shadow-slate-950/20">
                    <h3 class="text-xl font-semibold text-white">{{ __('Employee') }}</h3>
                    <ul class="mt-6 space-y-4 text-slate-300">
                        <li>{{ __('Receive an email when your visitor checks in.') }}</li>
                        <li>{{ __('View your own planned and completed visits.') }}</li>
                        <li>{{ __('Submit a visit in advance for reception.') }}</li>
                        <li>{{ __('Get quick insight into who is visiting you.') }}</li>
                    </ul>
                </article>
                <article class="rounded-3xl border border-white/10 bg-white/5 p-8 shadow-xl shadow-slate-950/20">
                    <h3 class="text-xl font-semibold text-white">{{ __('Admin') }}</h3>
                    <ul class="mt-6 space-y-4 text-slate-300">
                        <li>{{ __('Manage employee accounts and roles.') }}</li>
                        <li>{{ __('View all visitor data and historical visits.') }}</li>
                        <li>{{ __('Export the emergency list as CSV or PDF.') }}</li>
                        <li>{{ __('Request visitor statistics.') }}</li>
                    </ul>
                </article>
            </section>

            <section class="mt-16 rounded-[2rem] border border-slate-700/70 bg-slate-900/70 p-8 shadow-[0_30px_80px_-30px_rgba(15,23,42,0.85)]">
                <div class="grid gap-8 lg:grid-cols-2">
                    <div>
                        <h3 class="text-2xl font-semibold text-white">{{ __('What to expect here') }}</h3>
                        <p class="mt-4 text-slate-300 leading-7">{{ __('A clear start page for visitors and employees. Immediately see what the system does, which roles exist, and how registration works.') }}</p>
                    </div>
                    <div class="space-y-4 text-slate-300">
                        <div class="rounded-3xl bg-slate-950/80 p-5 ring-1 ring-white/5">
                            <p class="font-semibold text-white">{{ __('Emergency list') }}</p>
                            <p class="mt-2 text-sm">{{ __('Who is currently in the building?') }}</p>
                        </div>
                        <div class="rounded-3xl bg-slate-950/80 p-5 ring-1 ring-white/5">
                            <p class="font-semibold text-white">{{ __('Notifications') }}</p>
                            <p class="mt-2 text-sm">{{ __('Employees immediately see that their visitor has arrived.') }}</p>
                        </div>
                        <div class="rounded-3xl bg-slate-950/80 p-5 ring-1 ring-white/5">
                            <p class="font-semibold text-white">{{ __('History') }}</p>
                            <p class="mt-2 text-sm">{{ __('Find visitors from yesterday and the past week.') }}</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-white/10 py-8">
            <div class="mx-auto max-w-7xl px-6 text-sm text-slate-500 sm:px-8">© {{ date('Y') }} {{ config('app.name') }} — {{ __('A modern visitor registration for a safe and professional reception.') }}</div>
        </footer>
    </div>
</body>

</html>
