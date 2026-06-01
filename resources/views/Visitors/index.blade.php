<x-layouts.app>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Bezoekers</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Overzicht van gekoppelde bezoekersprofielen.</p>
        </div>
        <a href="{{ route('visitors.create') }}" class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white">
            Nieuwe bezoeker
        </a>
    </div>

    <form method="GET" action="{{ route('visitors.index') }}" class="mb-4">
        <div class="flex flex-wrap gap-2 items-center">
            <label for="name" class="sr-only">Zoek bezoeker</label>
            <input id="name" name="name" type="search" value="{{ request('name') }}"
                class="w-full sm:w-auto flex-1 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                placeholder="Zoek op naam..." />
            <button type="submit"
                class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Zoeken</button>
            @if(request()->filled('name'))
            <a href="{{ route('visitors.index') }}"
                class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">Reset</a>
            @endif
        </div>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/40 text-gray-600 dark:text-gray-300">
                <tr>
                    <th class="px-4 py-3">Naam</th>
                    <th class="px-4 py-3">E-mail</th>
                    <th class="px-4 py-3">Aangemaakt</th>
                    <th class="px-4 py-3">Acties</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($visitors as $visitor)
                <tr>
                    <td class="px-4 py-3">{{ $visitor->user->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $visitor->user->email ?? '-' }}</td>
                    <td class="px-4 py-3">{{ optional($visitor->created_at)->format('d-m-Y H:i') ?? '-' }}</td>
                    <td class="px-4 py-3">
                        @include('components.action-buttons', [
                            'show' => route('visitors.show', $visitor),
                            'edit' => route('visitors.edit', $visitor),
                            'destroy' => route('visitors.destroy', $visitor),
                            'deleteConfirm' => 'Weet je zeker dat je deze bezoeker wilt verwijderen?'
                        ])
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Geen bezoekers
                        gevonden.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>