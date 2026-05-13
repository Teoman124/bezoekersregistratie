<x-layouts.app>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Gebruikers</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Overzicht van alle accounts in het systeem.</p>
        </div>
        @if(in_array(auth()->user()?->role, ['admin', 'employee'], true))
            <a href="{{ route('users.create') }}" class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white">
                Nieuwe gebruiker
            </a>
        @endif
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/40 text-gray-600 dark:text-gray-300">
                <tr>
                    <th class="px-4 py-3">Naam</th>
                    <th class="px-4 py-3">E-mail</th>
                    <th class="px-4 py-3">Rol</th>
                    <th class="px-4 py-3">Acties</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($users as $user)
                    <tr>
                        <td class="px-4 py-3">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3 capitalize">{{ $user->role }}</td>
                        <td class="px-4 py-3">
                            <div class="flex gap-3">
                                <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:underline">Bekijken</a>
                                <a href="{{ route('users.edit', $user) }}" class="text-blue-600 hover:underline">Bewerken</a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Weet je zeker dat je deze gebruiker wilt verwijderen?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Verwijderen</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Geen gebruikers
                            gevonden.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>