<x-layouts.app>
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2">Afdeling bewerken</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Pas de naam van de afdeling aan.</p>

        <form action="{{ route('departments.update', $department) }}" method="POST"
            class="space-y-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium mb-1">Naam</label>
                <input id="name" name="name" type="text" value="{{ old('name', $department->name) }}" required
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" />
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white">Opslaan</button>
                <a href="{{ route('departments.index') }}" class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-700">Annuleren</a>
            </div>
        </form>
    </div>
</x-layouts.app>
