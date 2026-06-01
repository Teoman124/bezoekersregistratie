<x-layouts.app>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Medewerkers</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Beheer medewerkers en hun afdeling.</p>
        </div>
        <a href="{{ route('employees.create') }}" class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white">
            Nieuwe medewerker
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/40 text-gray-600 dark:text-gray-300">
                <tr>
                    <th class="px-4 py-3">Naam</th>
                    <th class="px-4 py-3">E-mail</th>
                    <th class="px-4 py-3">Afdeling</th>
                    <th class="px-4 py-3">Functie</th>
                    <th class="px-4 py-3">Acties</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($employees as $employee)
                    <tr>
                        <td class="px-4 py-3">{{ $employee->user->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $employee->user->email ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $employee->department->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $employee->function ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @include('components.action-buttons', [
                                'show' => route('employees.show', $employee),
                                'edit' => route('employees.edit', $employee),
                                'destroy' => route('employees.destroy', $employee),
                                'deleteConfirm' => 'Weet je zeker dat je deze medewerker wilt verwijderen?'
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Geen medewerkers
                            gevonden.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>