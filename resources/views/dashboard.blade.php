<x-layouts.app>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Dashboard')}}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">
            @if(in_array(auth()->user()?->role, ['admin', 'employee'], true))
            {{ __('Beheer gebruikers, medewerkers, bezoekers en bezoeken vanaf een plek.') }}
            @else
            {{ __('Je bent ingelogd als visitor. Beheerfuncties zijn niet beschikbaar.') }}
            @endif
        </p>
    </div>

    @if(in_array(auth()->user()?->role, ['admin', 'employee'], true))
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border-2 border-gray-300 col-span-2 flex flex-col justify-between">
            <div class="flex items-center gap-3 mb-2">
                <svg class="h-7 w-7 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 000 7.75" />
                </svg>
                <span class="text-lg font-semibold text-gray-700 dark:text-gray-200">Systeemoverzicht</span>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-center">
                <a href="{{ route('users.index') }}" class="block hover:bg-gray-50 dark:hover:bg-gray-700 rounded p-2">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Gebruikers</div>
                    <div class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $stats['users'] }}</div>
                </a>
                <a href="{{ route('employees.index') }}" class="block hover:bg-gray-50 dark:hover:bg-gray-700 rounded p-2">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Medewerkers</div>
                    <div class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $stats['employees'] }}</div>
                </a>
                <a href="{{ route('visitors.index') }}" class="block hover:bg-gray-50 dark:hover:bg-gray-700 rounded p-2">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Bezoekers</div>
                    <div class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $stats['visitors'] }}</div>
                </a>
                <a href="{{ route('visits.index') }}" class="block hover:bg-gray-50 dark:hover:bg-gray-700 rounded p-2">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Bezoeken</div>
                    <div class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $stats['visits'] }}</div>
                </a>
                <a href="{{ route('visits.active') }}" class="block hover:bg-gray-50 dark:hover:bg-gray-700 rounded p-2 col-span-2 md:col-span-1">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Aanwezig</div>
                    <div class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $stats['active_visits'] }}</div>
                </a>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border-2 border-blue-400 col-span-2 flex flex-col justify-between">
            <div class="flex items-center gap-3 mb-2">
                <svg class="h-7 w-7 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12l2 2 4-4" />
                </svg>
                <span class="text-lg font-semibold text-blue-700 dark:text-blue-300">Bezoekersoverzicht</span>
            </div>
            <div class="grid grid-cols-3 gap-2 text-center">
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Vandaag</div>
                    <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $stats['visits_today'] }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Gisteren</div>
                    <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $stats['visits_yesterday'] }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Deze week</div>
                    <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $stats['visits_this_week'] }}</div>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-center gap-2">
                <svg class="h-5 w-5 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" fill="none" stroke="currentColor" stroke-width="2" />
                    <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" />
                </svg>
                <span class="text-sm text-purple-700 dark:text-purple-400">Geplande bezoeken: <span class="font-bold">{{ $stats['planned_visits'] }}</span></span>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-green-200 dark:border-green-700 p-6 mb-6">
        <h2 class="text-lg font-semibold text-green-700 dark:text-green-300 mb-6">{{ __('Bezoekersinzichten') }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="rounded-lg bg-gray-50 dark:bg-gray-900 p-4 border border-gray-200 dark:border-gray-700">
                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Drukste dag') }}</div>
                <div class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-3">{{ $stats['busiest_day'] ?? __('Nog geen data') }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $stats['busiest_day_count'] }} {{ __('bezoeken') }}</div>
            </div>
            <div class="rounded-lg bg-gray-50 dark:bg-gray-900 p-4 border border-gray-200 dark:border-gray-700">
                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Gemiddelde duur') }}</div>
                <div class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-3">{{ $stats['average_visit_duration'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Voor afgeronde bezoeken') }}</div>
            </div>
            <div class="rounded-lg bg-gray-50 dark:bg-gray-900 p-4 border border-gray-200 dark:border-gray-700">
                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Verblijfsduur') }}</div>
                <div class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-3">{{ $chartsData['stayDurationStats']['median'] }}m</div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Mediaan (minuten)') }}</div>
            </div>
            <div class="rounded-lg bg-gray-50 dark:bg-gray-900 p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-3">
                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Top-medewerkers') }}</div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('Top 3') }}</span>
                </div>
                <ol class="space-y-1 text-xs text-gray-700 dark:text-gray-300">
                    @forelse($stats['top_employees'] as $employee)
                    <li class="flex items-center justify-between">
                        <span>{{ Str::limit($employee->user->name, 15) }}</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $employee->visits_count }}</span>
                    </li>
                    @empty
                    <li class="text-gray-500 dark:text-gray-400">{{ __('Geen data') }}</li>
                    @endforelse
                </ol>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Visits per Day Chart -->
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Bezoeken per dag (afgelopen week)') }}</h3>
                <canvas id="visitsPerDayChart" height="80"></canvas>
            </div>

            <!-- Visits per Week Chart -->
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Bezoeken per week (afgelopen 8 weken)') }}</h3>
                <canvas id="visitsPerWeekChart" height="80"></canvas>
            </div>

            <!-- Busiest Departments Chart -->
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Drukste afdelingen') }}</h3>
                <canvas id="busiestDepartmentsChart" height="80"></canvas>
            </div>

            <!-- Stay Duration Stats -->
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Verblijfsduur statistieken') }}</h3>
                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs text-gray-600 dark:text-gray-400">{{ __('Gemiddelde') }}</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $chartsData['stayDurationStats']['average'] }}m</span>
                        </div>
                        <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs text-gray-600 dark:text-gray-400">{{ __('Minimum') }}</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $chartsData['stayDurationStats']['min'] }}m</span>
                        </div>
                        <div class="h-2 bg-blue-200 dark:bg-blue-800 rounded-full w-1/3"></div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs text-gray-600 dark:text-gray-400">{{ __('Maximum') }}</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $chartsData['stayDurationStats']['max'] }}m</span>
                        </div>
                        <div class="h-2 bg-green-200 dark:bg-green-800 rounded-full" style="width: {{ min(100, ($chartsData['stayDurationStats']['max'] / 500) * 100) }}%"></div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs text-gray-600 dark:text-gray-400">{{ __('Mediaan') }}</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $chartsData['stayDurationStats']['median'] }}m</span>
                        </div>
                        <div class="h-2 bg-purple-200 dark:bg-purple-800 rounded-full" style="width: {{ min(100, ($chartsData['stayDurationStats']['median'] / 500) * 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 lg:col-span-2">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Snel naar beheer') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <a href="{{ route('users.create') }}"
                    class="px-4 py-3 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Nieuwe gebruiker') }}</a>
                <a href="{{ route('employees.create') }}"
                    class="px-4 py-3 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Nieuwe medewerker') }}</a>
                <a href="{{ route('visitors.create') }}"
                    class="px-4 py-3 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Nieuwe bezoeker') }}</a>
                <a href="{{ route('visits.active') }}"
                    class="px-4 py-3 rounded-md border border-rose-200 text-rose-700 dark:border-rose-800 dark:text-rose-300 hover:bg-rose-50 dark:hover:bg-rose-900/20">{{ __('Wie is er nu in het pand?') }}</a>
                <a href="{{ route('departments.index') }}"
                    class="px-4 py-3 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Afdelingen') }}
                    ({{ $stats['departments'] }})</a>
                <a href="{{ route('notifications.index') }}"
                    class="px-4 py-3 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Notificaties') }}</a>
                <a href="{{ route('visits.create') }}"
                    class="px-4 py-3 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 sm:col-span-2">{{ __('Nieuw bezoek plannen') }}</a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 lg:col-span-1">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('Aankomsten vandaag') }}</h2>
                <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-1 rounded-full">Laatste 5</span>
            </div>
            
            <ul class="space-y-4">
                @forelse($stats['recent_punctuality'] as $punctuality)
                    <li class="flex flex-col border-b border-gray-100 dark:border-gray-700 pb-3 last:border-0 last:pb-0">
                        <div class="flex justify-between items-start">
                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $punctuality['name'] }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $punctuality['time'] }}</span>
                        </div>
                        <div class="text-xs mt-1 {{ $punctuality['color_class'] }}">
                            {{ $punctuality['status_text'] }}
                        </div>
                    </li>
                @empty
                    <li class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
                        {{ __('Nog geen bezoeken ingecheckt vandaag.') }}
                    </li>
                @endforelse
            </ul>
        </div>

    </div>
    @else
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('Visitor account') }}</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
            {{ __('Je bent ingelogd als visitor. Beheerfuncties zoals bezoekers, bezoeken, afdelingen en notificaties zijn niet beschikbaar.') }}
        </p>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
            {{ __('Wil je een afspraak maken? Stuur dan een bericht via de mailbox naar de medewerker.') }}
        </p>
        <div class="mt-4">
            <a href="{{ route('mailbox.create') }}" class="inline-flex items-center px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white">
                {{ __('Afspraak aanvragen') }}
            </a>
            </p>
        </div>
        @endif

        @if(auth()->check() && auth()->user()->role === 'visitor')
        @php $visitor = auth()->user()->visitor; @endphp
        @if((!$visitor || empty($visitor->company_name)) && !session('visitor_company_prompt_skipped'))
        <div x-data="{ open: true }" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md mx-4">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-3">{{ __('Van welk bedrijf kom je?') }}</h2>

                <form method="POST" action="{{ route('visitor.company-info.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-forms.input name="company_name" label="{{ __('Bedrijf') }}" type="text" placeholder="{{ __('Bedrijfsnaam') }}" autofocus />
                    </div>

                    <x-button type="primary" class="w-full">{{ __('Opslaan') }}</x-button>
                </form>

                <form method="POST" action="{{ route('visitor.company-info.skip') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        {{ __('Overslaan') }}
                    </button>
                </form>
            </div>
        </div>
        @endif
        @endif

</x-layouts.app>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
    // Visits per Day Chart
    const visitsPerDayCtx = document.getElementById('visitsPerDayChart');
    if (visitsPerDayCtx) {
        new Chart(visitsPerDayCtx, {
            type: 'line',
            data: {
                labels: @json($chartsData['visitsPerDay']['labels']),
                datasets: [{
                    label: '{{ __("Bezoeken") }}',
                    data: @json($chartsData['visitsPerDay']['data']),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { color: document.documentElement.classList.contains('dark') ? 'rgba(75, 85, 99, 0.2)' : 'rgba(200, 200, 200, 0.2)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // Visits per Week Chart
    const visitsPerWeekCtx = document.getElementById('visitsPerWeekChart');
    if (visitsPerWeekCtx) {
        new Chart(visitsPerWeekCtx, {
            type: 'bar',
            data: {
                labels: @json($chartsData['visitsPerWeek']['labels']),
                datasets: [{
                    label: '{{ __("Bezoeken") }}',
                    data: @json($chartsData['visitsPerWeek']['data']),
                    backgroundColor: '#10b981',
                    borderColor: '#059669',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { color: document.documentElement.classList.contains('dark') ? 'rgba(75, 85, 99, 0.2)' : 'rgba(200, 200, 200, 0.2)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // Busiest Departments Chart
    const busiestDepartmentsCtx = document.getElementById('busiestDepartmentsChart');
    if (busiestDepartmentsCtx) {
        new Chart(busiestDepartmentsCtx, {
            type: 'bar',
            data: {
                labels: @json($chartsData['busiestDepartments']['labels']),
                datasets: [{
                    label: '{{ __("Bezoeken") }}',
                    data: @json($chartsData['busiestDepartments']['data']),
                    backgroundColor: '#f59e0b',
                    borderColor: '#d97706',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { color: document.documentElement.classList.contains('dark') ? 'rgba(75, 85, 99, 0.2)' : 'rgba(200, 200, 200, 0.2)' }
                    },
                    y: {
                        grid: { display: false }
                    }
                }
            }
        });
    }
</script>
