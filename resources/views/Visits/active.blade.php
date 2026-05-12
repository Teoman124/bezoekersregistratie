<x-layouts.app>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Aanwezige bezoekers</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Toont alleen bezoekers die zijn ingecheckt en nog niet
                zijn uitgecheckt.</p>
        </div>
        <a href="{{ route('visits.index') }}" class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-700">
            Alle bezoeken
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/40 text-gray-600 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3">Naam</th>
                        <th class="px-4 py-3">Contactpersoon</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($visits as $visit)
                        <tr>
                            <td class="px-4 py-3">{{ $visit->visitor?->user?->name ?? 'Onbekend' }}</td>
                            <td class="px-4 py-3">{{ $visit->employee?->user?->name ?? 'Onbekend' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Er zijn geen
                                aanwezige bezoekers.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>