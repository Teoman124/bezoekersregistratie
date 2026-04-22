<x-layouts.app>
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2">Nieuwe afdeling</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Voeg een afdeling toe aan het systeem.</p>

        <form action="{{ route('departments.store') }}" method="POST"
            class="space-y-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium mb-1">Naam</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" />
                @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white">Opslaan</button>
                <a href="{{ route('departments.index') }}" class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-700">Annuleren</a>
            </div>
        </form>
    </div>
</x-layouts.app>
