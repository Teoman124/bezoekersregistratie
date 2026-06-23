<x-layouts.app>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Noodlijst</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Overzicht van alle aanwezige bezoekers en medewerkers.
                <span x-text="'Laatst bijgewerkt: ' + lastUpdated" class="text-xs text-gray-500"></span>
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('visits.active.export') }}" class="px-4 py-2 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white">
                Export CSV
            </a>
            <a href="{{ route('visits.index') }}" class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-700">
                Alle bezoeken
            </a>
        </div>
    </div>

    <div x-data="presenceBoard()" class="space-y-6">
        <!-- Bezoekers tabel -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="border-b border-gray-200 dark:border-gray-700 p-4 flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Aanwezige bezoekers</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Bezoekers die nu binnen zijn en hun contactpersoon.</p>
                </div>
                <span class="text-sm font-medium text-blue-600 dark:text-blue-400">
                    Aantal: <span x-text="count"></span>
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/40 text-gray-600 dark:text-gray-300">
                        <tr>
                            <th class="px-4 py-3">Bezoeker</th>
                            <th class="px-4 py-3">Bedrijf</th>
                            <th class="px-4 py-3">Contactpersoon</th>
                            <th class="px-4 py-3">Aankomst</th>
                            <th class="px-4 py-3">Reden</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="visit in visits" :key="visit.id">
                            <tr>
                                <td class="px-4 py-3" x-text="visit.visitor?.user?.name ?? 'Onbekend'"></td>
                                <td class="px-4 py-3" x-text="visit.visitor?.company_name ?? '-'"></td>
                                <td class="px-4 py-3" x-text="visit.employee?.user?.name ?? 'Onbekend'"></td>
                                <td class="px-4 py-3" x-text="new Date(visit.check_in_time).toLocaleString()"></td>
                                <td class="px-4 py-3" x-text="visit.reason_of_visit ?? '-'"></td>
                            </tr>
                        </template>
                        <tr x-show="visits.length === 0">
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                Er zijn geen aanwezige bezoekers.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Medewerkers tabel (blijft statisch, of je kunt ook hier polling voor doen) -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="border-b border-gray-200 dark:border-gray-700 p-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Aanwezige medewerkers</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">Medewerkers die nu gekoppeld zijn aan een actief bezoek.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/40 text-gray-600 dark:text-gray-300">
                        <tr>
                            <th class="px-4 py-3">Naam</th>
                            <th class="px-4 py-3">E-mail</th>
                            <th class="px-4 py-3">Afdeling</th>
                            <th class="px-4 py-3">Functie</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($employees as $employee)
                            <tr>
                                <td class="px-4 py-3">{{ $employee->user?->name ?? 'Onbekend' }}</td>
                                <td class="px-4 py-3">{{ $employee->user?->email ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $employee->department?->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $employee->function ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                    Er zijn geen aanwezige medewerkers gevonden.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function presenceBoard() {
            return {
                visits: [],
                count: 0,
                lastUpdated: '',
                init() {
                    this.fetch();
                    setInterval(() => this.fetch(), 10000); // elke 10 seconden
                },
                fetch() {
                    fetch('/api/active-visits', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            // Als je token-gebaseerde auth gebruikt, voeg dan de Authorization header toe:
                            // 'Authorization': 'Bearer ' + localStorage.getItem('token')
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.visits = data.visits;
                        this.count = data.count;
                        this.lastUpdated = new Date().toLocaleTimeString();
                    })
                    .catch(err => console.error('Polling error:', err));
                }
            }
        }
    </script>
</x-layouts.app>