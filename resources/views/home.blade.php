<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receptie & Bezoekersregistratie — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-950 text-slate-100 antialiased">
    <div class="min-h-screen bg-[radial-gradient(circle_at_top,rgba(59,130,246,0.18),transparent_28%),radial-gradient(circle_at_top_right,rgba(16,185,129,0.15),transparent_30%),linear-gradient(to_bottom,#020617,#0f172a)]">
        <header class="border-b border-white/10">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-6 lg:px-8">
                <div>
                    <p class="text-xs uppercase tracking-[0.24em] text-sky-300/80">Bezoekersregistratie</p>
                    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-white sm:text-4xl">Slimmer ontvangen, veiliger inchecken.</h1>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}" class="rounded-full border border-slate-500/40 bg-white/5 px-4 py-2 text-sm text-slate-100 transition hover:border-sky-300 hover:bg-slate-800">Medewerker inloggen</a>
                    <a href="{{ route('visitor.login') }}" class="rounded-full bg-sky-500 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-sky-500/20 transition hover:bg-sky-400">Bezoeker aanmelden</a>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-6 py-12 sm:px-8 lg:py-16">
            <section class="grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
                <div class="space-y-8">
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-8 shadow-[0_20px_70px_-40px_rgba(15,23,42,0.75)]">
                        <p class="text-sm uppercase tracking-[0.24em] text-sky-300">Projectomschrijving</p>
                        <p class="mt-4 text-slate-200 leading-7">Een bedrijf wil het bezoekersproces moderniseren. Op dit moment tekenen bezoekers hun naam in een papieren schrift bij de receptie. Dat is onprofessioneel, slecht leesbaar, en in een noodsituatie weet niemand wie er in het pand is.</p>
                        <p class="mt-4 text-slate-300 leading-7">De opdrachtgever wil een digitaal systeem waarbij bezoekers netjes worden aangemeld, de ontvanger automatisch een bericht krijgt, en een realtime overzicht beschikbaar is van wie er in het pand aanwezig is.</p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-6">
                            <h2 class="text-xl font-semibold text-white">Functionele eisen</h2>
                            <ul class="mt-5 space-y-3 text-slate-300 text-sm leading-7">
                                <li><span class="font-semibold text-sky-300">Receptionist:</span> inchecken, uitchecken, realtime overzicht en historie.</li>
                                <li><span class="font-semibold text-sky-300">Medewerker:</span> e-mailnotificatie en eigen bezoeken bekijken.</li>
                                <li><span class="font-semibold text-sky-300">Beheerder:</span> accounts, export en noodlijst.</li>
                            </ul>
                        </div>
                        <div class="rounded-3xl border border-white/10 bg-slate-900/70 p-6">
                            <h2 class="text-xl font-semibold text-white">Technische eisen</h2>
                            <ul class="mt-5 space-y-3 text-slate-300 text-sm leading-7">
                                <li>Laravel (nieuwste stabiele versie)</li>
                                <li>Database met users, visits en visitors</li>
                                <li>Authenticatie en rolgebaseerde autorisatie</li>
                                <li>E-mailnotificaties en realtime aanwezigheidslijst</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-white/10 bg-white/5 p-8 shadow-[0_40px_120px_-40px_rgba(15,23,42,0.8)] backdrop-blur-xl">
                    <div class="space-y-6">
                        <div class="flex items-start gap-4 text-slate-200">
                            <span class="inline-flex h-12 w-12 items-center justify-center rounded-3xl bg-sky-500/15 text-sky-300">01</span>
                            <div>
                                <h3 class="text-xl font-semibold">Snel aanmelden</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-400">Bezoekers worden direct geregistreerd met alle noodzakelijke velden.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 text-slate-200">
                            <span class="inline-flex h-12 w-12 items-center justify-center rounded-3xl bg-sky-500/15 text-sky-300">02</span>
                            <div>
                                <h3 class="text-xl font-semibold">Automatische alert</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-400">Ontvangers krijgen automatisch een bericht zodra hun bezoeker incheckt.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 text-slate-200">
                            <span class="inline-flex h-12 w-12 items-center justify-center rounded-3xl bg-sky-500/15 text-sky-300">03</span>
                            <div>
                                <h3 class="text-xl font-semibold">Realtime noodlijst</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-400">Zie wie er nog binnen is, met naam, bedrijf en contactpersoon.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 text-slate-200">
                            <span class="inline-flex h-12 w-12 items-center justify-center rounded-3xl bg-sky-500/15 text-sky-300">04</span>
                            <div>
                                <h3 class="text-xl font-semibold">Veilig en professioneel</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-400">Een visiteproces dat er netjes uitziet en altijd traceerbaar blijft.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mt-16 grid gap-10 lg:grid-cols-3">
                <article class="rounded-3xl border border-white/10 bg-white/5 p-8 shadow-xl shadow-slate-950/20">
                    <h3 class="text-xl font-semibold text-white">Receptionist</h3>
                    <ul class="mt-6 space-y-4 text-slate-300">
                        <li>Naam, bedrijf, contactpersoon en bezoekreden vastleggen</li>
                        <li>In- en uitchecken met precieze tijdstempels</li>
                        <li>Realtimelijst van alle aanwezige bezoekers</li>
                        <li>Historie vanaf gisteren of afgelopen week raadplegen</li>
                    </ul>
                </article>
                <article class="rounded-3xl border border-white/10 bg-white/5 p-8 shadow-xl shadow-slate-950/20">
                    <h3 class="text-xl font-semibold text-white">Medewerker</h3>
                    <ul class="mt-6 space-y-4 text-slate-300">
                        <li>Ontvang e-mail bij inchecken van jouw bezoeker</li>
                        <li>Bekijk je eigen geplande en afgeronde bezoeken</li>
                        <li>Dien een bezoek vooraf in voor de receptie</li>
                        <li>Krijg snel inzicht in wie jou komt bezoeken</li>
                    </ul>
                </article>
                <article class="rounded-3xl border border-white/10 bg-white/5 p-8 shadow-xl shadow-slate-950/20">
                    <h3 class="text-xl font-semibold text-white">Beheerder</h3>
                    <ul class="mt-6 space-y-4 text-slate-300">
                        <li>Medewerkersaccounts en rollen beheren</li>
                        <li>Alle bezoekersdata en historische visits inzien</li>
                        <li>Noodlijst exporteren als CSV of PDF</li>
                        <li>Bezoekersstatistieken opvragen</li>
                    </ul>
                </article>
            </section>

            <section class="mt-16 rounded-[2rem] border border-slate-700/70 bg-slate-900/70 p-8 shadow-[0_30px_80px_-30px_rgba(15,23,42,0.85)]">
                <div class="grid gap-8 lg:grid-cols-2">
                    <div>
                        <h3 class="text-2xl font-semibold text-white">Wat je hier kunt verwachten</h3>
                        <p class="mt-4 text-slate-300 leading-7">Een duidelijke startpagina voor bezoekers en medewerkers. Direct helder wat het systeem doet, welke rollen er zijn en hoe de registratie werkt.</p>
                    </div>
                    <div class="space-y-4 text-slate-300">
                        <div class="rounded-3xl bg-slate-950/80 p-5 ring-1 ring-white/5">
                            <p class="font-semibold text-white">Noodlijst</p>
                            <p class="mt-2 text-sm">Wie is op dat moment in het pand?</p>
                        </div>
                        <div class="rounded-3xl bg-slate-950/80 p-5 ring-1 ring-white/5">
                            <p class="font-semibold text-white">Notificaties</p>
                            <p class="mt-2 text-sm">Medewerkers zien direct dat hun bezoeker is gearriveerd.</p>
                        </div>
                        <div class="rounded-3xl bg-slate-950/80 p-5 ring-1 ring-white/5">
                            <p class="font-semibold text-white">Historie</p>
                            <p class="mt-2 text-sm">Bezoekers uit gisteren en afgelopen week terugvinden.</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-white/10 py-8">
            <div class="mx-auto max-w-7xl px-6 text-sm text-slate-500 sm:px-8">© {{ date('Y') }} {{ config('app.name') }} — Een moderne bezoekersregistratie voor een veilige en professionele ontvangst.</div>
        </footer>
    </div>
</body>

</html>
