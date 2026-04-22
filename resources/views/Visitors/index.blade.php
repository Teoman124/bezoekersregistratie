<x-layouts.app>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Bezoekers</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Overzicht van gekoppelde bezoekersprofielen.</p>
        </div>
        <a href="{{ route('visitors.create') }}" class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white">
            Nieuwe bezoeker
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/40 text-gray-600 dark:text-gray-300">
                <tr>
                    <th class="px-4 py-3">Naam</th>
                    <th class="px-4 py-3">E-mail</th>
                    <th class="px-4 py-3">Aangemaakt</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($visitors as $visitor)
                    <tr>
                        <td class="px-4 py-3">{{ $visitor->user->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $visitor->user->email ?? '-' }}</td>
                        <td class="px-4 py-3">{{ optional($visitor->created_at)->format('d-m-Y H:i') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Geen bezoekers
                            gevonden.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>