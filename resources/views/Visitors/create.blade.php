<x-layouts.app>
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2">Nieuwe bezoeker</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Koppel een user met rol visitor aan een
            bezoekersprofiel.</p>

        <form action="{{ route('visitors.store') }}" method="POST"
            class="space-y-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            @csrf

            <div>
                <label for="user_id" class="block text-sm font-medium mb-1">Gebruiker (rol: visitor)</label>
                <select id="user_id" name="user_id" required
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    <option value="">Kies een gebruiker</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $user->name }}
                            ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white">Opslaan</button>
                <a href="{{ route('visitors.index') }}"
                    class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-700">Annuleren</a>
            </div>
        </form>
    </div>
</x-layouts.app>