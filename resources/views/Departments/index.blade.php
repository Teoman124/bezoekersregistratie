<x-layouts.app>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Afdelingen</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Beheer de afdelingen binnen het bedrijf.</p>
        </div>
        <a href="{{ route('departments.create') }}" class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white">Nieuwe afdeling</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/40 text-gray-600 dark:text-gray-300">
                <tr>
                    <th class="px-4 py-3">Naam</th>
                    <th class="px-4 py-3">Medewerkers</th>
                    <th class="px-4 py-3">Acties</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($departments as $department)
                    <tr>
                        <td class="px-4 py-3">{{ $department->name }}</td>
                        <td class="px-4 py-3">{{ $department->employees_count ?? $department->employees->count() ?? 0 }}</td>
                        <td class="px-4 py-3">
                            @include('components.action-buttons', [
                                'show' => route('departments.show', $department),
                                'edit' => route('departments.edit', $department),
                                'destroy' => route('departments.destroy', $department),
                                'deleteConfirm' => 'Weet je zeker dat je deze afdeling wilt verwijderen?'
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Geen afdelingen gevonden.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>
