<x-layouts.app>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Bezoeken</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Overzicht van alle ingeplande en afgeronde bezoeken.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('visits.active') }}" class="px-4 py-2 rounded-md bg-rose-600 hover:bg-rose-700 text-white">
                Wie is er nu in het pand?
            </a>
            <a href="{{ route('visits.history') }}" class="px-4 py-2 rounded-md bg-slate-600 hover:bg-slate-700 text-white">
                Bezoekgeschiedenis
            </a>
            <a href="{{ route('visits.export') }}" class="px-4 py-2 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white">
                Export CSV
            </a>
            <a href="{{ route('visits.create') }}" class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white">
                Nieuw bezoek
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('visits.index') }}" class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 mb-6 flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label for="search" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Zoeken (Naam)</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Bezoeker of gastheer..."
                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm">
        </div>

        <div>
            <label for="date" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Datum</label>
            <input type="date" name="date" id="date" value="{{ request('date') }}"
                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm">
        </div>

        <div>
            <label for="department_id" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Afdeling</label>
            <select name="department_id" id="department_id" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm">
                <option value="">Alle afdelingen</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" @selected(request('department_id') == $dept->id)>{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="host_id" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Gastheer</label>
            <select name="host_id" id="host_id" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm">
                <option value="">Alle gastheren</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" @selected(request('host_id') == $emp->id)>{{ $emp->user?->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="status" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
            <select name="status" id="status" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm">
                <option value="">Alles</option>
                <option value="planned" @selected(request('status') === 'planned')>Ingepland</option>
                <option value="in" @selected(request('status') === 'in')>Actief</option>
                <option value="out" @selected(request('status') === 'out')>Uitgecheckt</option>
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">Filter</button>
            @if(request()->anyFilled(['search', 'date', 'department_id', 'host_id', 'status']))
                <a href="{{ route('visits.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md text-sm hover:bg-gray-300 dark:hover:bg-gray-600">Wis</a>
            @endif
        </div>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/40 text-gray-600 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3">Bezoeker</th>
                        <th class="px-4 py-3">Medewerker</th>
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
                        <td class="px-4 py-3">{{ $visit->visitor?->user?->name ?? 'Onbekend' }}</td>
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
                            <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200">Uitgecheckt</span>
                            @elseif($visit->check_in_time)
                            <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">Actief</span>
                            @else
                            <span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300">Ingepland</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-2">
                                @if(!$visit->check_in_time)
                                <form action="{{ route('visits.checkin', $visit) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 rounded-full bg-green-600 text-white text-xs hover:bg-green-700">Inchecken</button>
                                </form>
                                @endif

                                @if($visit->check_in_time && !$visit->check_out_time)
                                <a href="{{ route('visits.checkout', $visit) }}" class="px-3 py-1 rounded-full bg-amber-600 text-white text-xs hover:bg-amber-700">Uitchecken</a>
                                @endif

                                <a href="{{ route('visits.show', $visit) }}" class="px-3 py-1 rounded-full bg-blue-600 text-white text-xs hover:bg-blue-700">Bekijken</a>
                                <a href="{{ route('visits.edit', $visit) }}" class="px-3 py-1 rounded-full bg-slate-600 text-white text-xs hover:bg-slate-700">Bewerken</a>
                                <form action="{{ route('visits.destroy', $visit) }}" method="POST" onsubmit="return confirm('Weet je zeker dat je dit bezoek wilt verwijderen?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 rounded-full bg-red-600 text-white text-xs hover:bg-red-700">Verwijderen</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Geen bezoeken gevonden die matchen met deze filters.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>