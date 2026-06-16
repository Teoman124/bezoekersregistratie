<x-layouts.app>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Noodlijst</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Overzicht van alle aanwezige bezoekers en medewerkers voor een snelle ontruiming.</p>
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

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="border-b border-gray-200 dark:border-gray-700 p-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Aanwezige bezoekers</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">Bezoekers die nu binnen zijn en hun contactpersoon.</p>
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
                        @forelse($visits as $visit)
                            <tr>
                                <td class="px-4 py-3">{{ $visit->visitor?->user?->name ?? 'Onbekend' }}</td>
                                <td class="px-4 py-3">{{ $visit->visitor?->company_name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $visit->employee?->user?->name ?? 'Onbekend' }}</td>
                                <td class="px-4 py-3">{{ $visit->check_in_time?->format('d-m-Y H:i') ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $visit->reason_of_visit ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Er zijn geen aanwezige bezoekers.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

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
                                <td colspan="4" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Er zijn geen aanwezige medewerkers gevonden.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>