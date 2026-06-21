<x-layouts.app>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Mijn bezoeken</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Overzicht van jouw geplande, actieve en afgeronde
                bezoeken.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if(in_array(auth()->user()?->role, ['admin', 'employee'], true))
                <a href="{{ route('visits.index') }}"
                    class="px-4 py-2 rounded-md bg-slate-600 hover:bg-slate-700 text-white">Alle bezoeken</a>
                <a href="{{ route('visits.create') }}"
                    class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white">Nieuw bezoek</a>
            @elseif(auth()->user()?->role === 'visitor')
                <a href="{{ route('mailbox.create') }}"
                    class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white">Afspraak aanvragen</a>
            @endif
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="border-b border-gray-200 dark:border-gray-700 p-4 flex flex-wrap gap-2">
            <a href="{{ route('visits.myvisits') }}"
                class="px-3 py-1.5 rounded-full text-sm border {{ request('status') ? 'border-gray-200 dark:border-gray-700' : 'border-blue-600 text-blue-600' }}">Alles</a>
            <a href="{{ route('visits.myvisits', ['status' => 'planned']) }}"
                class="px-3 py-1.5 rounded-full text-sm border {{ request('status') === 'planned' ? 'border-blue-600 text-blue-600' : 'border-gray-200 dark:border-gray-700' }}">Gepland</a>
            <a href="{{ route('visits.myvisits', ['status' => 'in']) }}"
                class="px-3 py-1.5 rounded-full text-sm border {{ request('status') === 'in' ? 'border-blue-600 text-blue-600' : 'border-gray-200 dark:border-gray-700' }}">Actief</a>
            <a href="{{ route('visits.myvisits', ['status' => 'out']) }}"
                class="px-3 py-1.5 rounded-full text-sm border {{ request('status') === 'out' ? 'border-blue-600 text-blue-600' : 'border-gray-200 dark:border-gray-700' }}">Afgerond</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/40 text-gray-600 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3">{{ auth()->user()?->role === 'visitor' ? 'Medewerker' : 'Bezoeker' }}</th>
                        <th class="px-4 py-3">Reden</th>
                        <th class="px-4 py-3">Aankomst</th>
                        <th class="px-4 py-3">Vertrek</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">NDA</th> <!-- 🔥 Nieuwe kolom -->
                        <th class="px-4 py-3">Acties</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($visits as $visit)
                        <tr>
                            <td class="px-4 py-3">
                                @if(auth()->user()?->role === 'visitor')
                                    {{ $visit->employee?->user?->name ?? 'Onbekend' }}
                                    @if($visit->employee?->department)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $visit->employee->department->name }}</div>
                                    @endif
                                @else
                                    {{ $visit->visitor?->user?->name ?? 'Onbekend' }}
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $visit->reason_of_visit ?: 'Geen reden ingevuld' }}</td>
                            <td class="px-4 py-3">{{ $visit->expected_arrival_time?->format('d-m-Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $visit->expected_departure_time?->format('d-m-Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if($visit->check_out_time)
                                    <span
                                        class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200">Afgerond</span>
                                @elseif($visit->check_in_time)
                                    <span
                                        class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">Actief</span>
                                @else
                                    <span
                                        class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300">Gepland</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <!-- 🔥 NDA Status en knop -->
                                @if(auth()->user()?->role === 'visitor')
                                    @if($visit->agreed_to_rules)
                                        <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                            ✅ Getekend
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 block">
                                            {{ $visit->agreed_at?->format('d-m-Y H:i') }}
                                        </span>
                                    @else
                                        <div class="flex flex-col items-start gap-1">
                                            <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300">
                                                ⚠️ Niet getekend
                                            </span>
                                            @if(!$visit->check_out_time)
                                                <a href="{{ route('visitor.nda.show', $visit) }}" 
                                                   class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors">
                                                    📝 Teken NDA
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-500">Bezoek afgerond</span>
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <!-- Admin/Employee zien NDA status -->
                                    @if($visit->agreed_to_rules)
                                        <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                            ✅ {{ $visit->agreed_at?->format('d-m-Y H:i') }}
                                        </span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300">
                                            ❌ Niet getekend
                                        </span>
                                    @endif
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    @if(auth()->user()?->role !== 'visitor')
                                        @if(!$visit->check_in_time)
                                            <form action="{{ route('visits.checkin', $visit) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="px-3 py-1 rounded-full bg-green-600 text-white text-xs hover:bg-green-700">Inchecken</button>
                                            </form>
                                        @endif

                                        @if($visit->check_in_time && !$visit->check_out_time)
                                            <a href="{{ route('visits.checkout', $visit) }}"
                                                class="px-3 py-1 rounded-full bg-amber-600 text-white text-xs hover:bg-amber-700">Uitchecken</a>
                                        @endif

                                        <a href="{{ route('visits.edit', $visit) }}"
                                            class="px-3 py-1 rounded-full bg-slate-600 text-white text-xs hover:bg-slate-700">Bewerken</a>
                                    @endif

                                    <a href="{{ route('visits.show', $visit) }}"
                                        class="px-3 py-1 rounded-full bg-blue-600 text-white text-xs hover:bg-blue-700">Bekijken</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Geen bezoeken
                                gevonden.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>