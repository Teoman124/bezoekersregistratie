<x-layouts.app>
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2">Notificatie bewerken</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Werk de ontvanger, inhoud en status bij.</p>

        <form action="{{ route('notifications.update', $notification) }}" method="POST"
            class="space-y-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            @csrf
            @method('PUT')

            <div>
                <label for="user_id" class="block text-sm font-medium mb-1">Gebruiker</label>
                <select id="user_id" name="user_id" required
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    <option value="">Kies een gebruiker</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(old('user_id', $notification->user_id) == $user->id)>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="title" class="block text-sm font-medium mb-1">Titel</label>
                <input id="title" name="title" type="text" value="{{ old('title', $notification->title) }}" required
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" />
            </div>

            <div>
                <label for="message" class="block text-sm font-medium mb-1">Bericht</label>
                <textarea id="message" name="message" rows="5" required
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">{{ old('message', $notification->message) }}</textarea>
            </div>

            <div class="flex items-center gap-2">
                <input id="read" name="read" type="checkbox" value="1" @checked(old('read', $notification->read))
                    class="rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900" />
                <label for="read" class="text-sm font-medium">Gelezen</label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white">Opslaan</button>
                <a href="{{ route('notifications.index') }}" class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-700">Annuleren</a>
            </div>
        </form>
    </div>
</x-layouts.app>