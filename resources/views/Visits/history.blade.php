<x-layouts.app>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Bezoekgeschiedenis</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Zoeken en filteren van alle eerdere bezoeken.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('visits.export') }}"
                class="px-4 py-2 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white">Export CSV</a>
            <a href="{{ route('visits.index') }}"
                class="px-4 py-2 rounded-md bg-slate-600 hover:bg-slate-700 text-white">Actieve bezoeken</a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="border-b border-gray-200 dark:border-gray-700 p-4 space-y-4">
            <!-- Status filters -->
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('visits.history') }}"
                    class="px-3 py-1.5 rounded-full text-sm border {{ !request('status') ? 'border-blue-600 text-blue-600' : 'border-gray-200 dark:border-gray-700' }}">Alles</a>
                <a href="{{ route('visits.history', ['status' => 'planned']) }}"
                    class="px-3 py-1.5 rounded-full text-sm border {{ request('status') === 'planned' ? 'border-blue-600 text-blue-600' : 'border-gray-200 dark:border-gray-700' }}">Gepland</a>
                <a href="{{ route('visits.history', ['status' => 'active']) }}"
                    class="px-3 py-1.5 rounded-full text-sm border {{ request('status') === 'active' ? 'border-blue-600 text-blue-600' : 'border-gray-200 dark:border-gray-700' }}">Actief</a>
                <a href="{{ route('visits.history', ['status' => 'completed']) }}"
                    class="px-3 py-1.5 rounded-full text-sm border {{ request('status') === 'completed' ? 'border-blue-600 text-blue-600' : 'border-gray-200 dark:border-gray-700' }}">Afgerond</a>
            </div>

            <!-- Date and sorting filters -->
            <div class="flex flex-wrap gap-3">
                <div class="flex gap-2">
                    <span class="text-xs text-gray-600 dark:text-gray-400 font-medium pt-1">Datum:</span>
                    <a href="{{ route('visits.history', array_merge(request()->query(), ['date_filter' => 'yesterday'])) }}"
                        class="px-3 py-1 rounded-md text-xs border {{ request('date_filter') === 'yesterday' ? 'border-blue-600 bg-blue-50 text-blue-600 dark:bg-blue-900/30' : 'border-gray-300 dark:border-gray-600' }}">Gisteren</a>
                    <a href="{{ route('visits.history', array_merge(request()->query(), ['date_filter' => 'week'])) }}"
                        class="px-3 py-1 rounded-md text-xs border {{ request('date_filter') === 'week' ? 'border-blue-600 bg-blue-50 text-blue-600 dark:bg-blue-900/30' : 'border-gray-300 dark:border-gray-600' }}">Afgelopen week</a>
                    <a href="{{ route('visits.history', array_merge(request()->query(), ['date_filter' => 'month'])) }}"
                        class="px-3 py-1 rounded-md text-xs border {{ request('date_filter') === 'month' ? 'border-blue-600 bg-blue-50 text-blue-600 dark:bg-blue-900/30' : 'border-gray-300 dark:border-gray-600' }}">Deze maand</a>
                    <a href="{{ route('visits.history') }}"
                        class="px-3 py-1 rounded-md text-xs border border-gray-300 dark:border-gray-600">Reset</a>
                </div>

                <div class="flex gap-2">
                    <span class="text-xs text-gray-600 dark:text-gray-400 font-medium pt-1">Sorteren:</span>
                    <a href="{{ route('visits.history', array_merge(request()->query(), ['sort' => 'expected_arrival_time', 'order' => 'desc'])) }}"
                        class="px-3 py-1 rounded-md text-xs border {{ request('sort') === 'expected_arrival_time' && request('order') === 'desc' ? 'border-green-600 bg-green-50 text-green-600 dark:bg-green-900/30' : 'border-gray-300 dark:border-gray-600' }}">Nieuwste</a>
                    <a href="{{ route('visits.history', array_merge(request()->query(), ['sort' => 'expected_arrival_time', 'order' => 'asc'])) }}"
                        class="px-3 py-1 rounded-md text-xs border {{ request('sort') === 'expected_arrival_time' && request('order') === 'asc' ? 'border-green-600 bg-green-50 text-green-600 dark:bg-green-900/30' : 'border-gray-300 dark:border-gray-600' }}">Oudste</a>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/40 text-gray-600 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3">Datum</th>
                        <th class="px-4 py-3">Bezoeker</th>
                        <th class="px-4 py-3">Medewerker</th>
                        <th class="px-4 py-3">Afdeling</th>
                        <th class="px-4 py-3">Reden</th>
                        <th class="px-4 py-3">Aankomst</th>
                        <th class="px-4 py-3">Vertrek</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Acties</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($visits as $visit)
                    <tr>
                        <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400">{{ $visit->expected_arrival_time?->format('d-m-Y') ?? '-' }}</td>
                        <td class="px-4 py-3">
                            {{ $visit->visitor?->user?->name ?? 'Onbekend' }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $visit->employee?->user?->name ?? 'Onbekend' }}
                        </td>
                        <td class="px-4 py-3 text-xs">
                            {{ $visit->employee?->department?->name ?? '-' }}
                        </td>
                        <td class="px-4 py-3">{{ $visit->reason_of_visit ?: 'Geen reden ingevuld' }}</td>
                        <td class="px-4 py-3 text-xs">{{ $visit->expected_arrival_time?->format('H:i') ?? '-' }}
                            @if($visit->check_in_time)
                            <div class="text-gray-500">✓ {{ $visit->check_in_time?->format('H:i') }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs">{{ $visit->expected_departure_time?->format('H:i') ?? '-' }}
                            @if($visit->check_out_time)
                            <div class="text-gray-500">✓ {{ $visit->check_out_time?->format('H:i') }}</div>
                            @endif
                        </td>
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
                        <td colspan="9" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Geen bezoeken gevonden.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
