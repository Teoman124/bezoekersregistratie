<x-layouts.app>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Gebruikers</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Overzicht van alle accounts in het systeem.</p>
        </div>
        @if(auth()->user()?->role === 'admin')
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
                            @include('components.action-buttons', [
                                'show' => route('users.show', $user),
                                'edit' => auth()->user()?->role === 'admin' ? route('users.edit', $user) : null,
                                'destroy' => auth()->user()?->role === 'admin' ? route('users.destroy', $user) : null,
                                'deleteConfirm' => 'Weet je zeker dat je deze gebruiker wilt verwijderen?'
                            ])
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