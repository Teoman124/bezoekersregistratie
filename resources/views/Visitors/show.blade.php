<x-layouts.app>
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Bezoeker details</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">Bekijk het gekoppelde bezoekersprofiel.</p>
            </div>
            <a href="{{ route('visitors.edit', $visitor) }}"
                class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white">Bewerken</a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 space-y-4">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Naam</p>
                <p class="text-lg font-medium text-gray-800 dark:text-gray-100">
                    {{ $visitor->user?->name ?? 'Onbekend' }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">E-mail</p>
                <p class="text-lg font-medium text-gray-800 dark:text-gray-100">
                    {{ $visitor->user?->email ?? 'Onbekend' }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Aantal bezoeken</p>
                <p class="text-lg font-medium text-gray-800 dark:text-gray-100">{{ $visitor->visits->count() }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Aangemaakt</p>
                <p class="text-lg font-medium text-gray-800 dark:text-gray-100">
                    {{ optional($visitor->created_at)->format('d-m-Y H:i') ?? '-' }}
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="border-b border-gray-200 dark:border-gray-700 p-4 flex flex-wrap gap-2">
                <a href="{{ route('visitors.show', $visitor) }}"
                    class="px-3 py-1.5 rounded-full text-sm border {{ !request('history') ? 'border-blue-600 text-blue-600' : 'border-gray-200 dark:border-gray-700' }}">Alle bezoeken</a>
                <a href="{{ route('visitors.show', ['visitor' => $visitor, 'history' => 'completed']) }}"
                    class="px-3 py-1.5 rounded-full text-sm border {{ request('history') === 'completed' ? 'border-blue-600 text-blue-600' : 'border-gray-200 dark:border-gray-700' }}">Afgerond</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/40 text-gray-600 dark:text-gray-300">
                        <tr>
                            <th class="px-4 py-3">Medewerker</th>
                            <th class="px-4 py-3">Reden</th>
                            <th class="px-4 py-3">Aankomst</th>
                            <th class="px-4 py-3">Vertrek</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Acties</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($visitor->visits->filter(function($visit) {
                        if (request('history') === 'completed') {
                        return $visit->check_out_time !== null;
                        }
                        return true;
                        }) as $visit)
                        <tr>
                            <td class="px-4 py-3">
                                {{ $visit->employee?->user?->name ?? 'Onbekend' }}
                                @if($visit->employee?->department)
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $visit->employee->department->name }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $visit->reason_of_visit ?: 'Geen reden ingevuld' }}</td>
                            <td class="px-4 py-3">{{ $visit->expected_arrival_time?->format('d-m-Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $visit->expected_departure_time?->format('d-m-Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if($visit->check_out_time)
                                <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200">Afgerond</span>
                                @elseif($visit->check_in_time)
                                <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">Actief</span>
                                @else
                                <span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300">Gepland</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('visits.show', $visit) }}" class="px-3 py-1 rounded-full bg-blue-600 text-white text-xs hover:bg-blue-700">Bekijken</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Geen bezoeken gevonden.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('visitors.index') }}"
                class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-700">Terug</a>
        </div>
    </div>
</x-layouts.app>