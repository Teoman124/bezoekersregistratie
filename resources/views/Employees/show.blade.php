<x-layouts.app>
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Medewerker details</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">Bekijk de gekoppelde gebruiker, afdeling en functie.</p>
            </div>
            <a href="{{ route('employees.edit', $employee) }}" class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white">Bewerken</a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 space-y-4">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Naam</p>
                <p class="text-lg font-medium text-gray-800 dark:text-gray-100">{{ $employee->user?->name ?? 'Onbekend' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">E-mail</p>
                <p class="text-lg font-medium text-gray-800 dark:text-gray-100">{{ $employee->user?->email ?? 'Onbekend' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Afdeling</p>
                <p class="text-lg font-medium text-gray-800 dark:text-gray-100">{{ $employee->department?->name ?? 'Onbekend' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Functie</p>
                <p class="text-lg font-medium text-gray-800 dark:text-gray-100">{{ $employee->function ?: 'Geen functie ingevuld' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Bezoeken als gastheer</p>
                <p class="text-lg font-medium text-gray-800 dark:text-gray-100">{{ $employee->visits->count() }}</p>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('employees.index') }}" class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-700">Terug</a>
        </div>
    </div>
</x-layouts.app>