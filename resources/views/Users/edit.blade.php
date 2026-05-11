<x-layouts.app>
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2">Gebruiker bewerken</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Pas accountgegevens en rol aan.</p>

        <form action="{{ route('users.update', $user) }}" method="POST"
            class="space-y-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium mb-1">Naam</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" />
            </div>

            <div>
                <label for="email" class="block text-sm font-medium mb-1">E-mail</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" />
            </div>

            <div>
                <label for="role" class="block text-sm font-medium mb-1">Rol</label>
                <select id="role" name="role" required
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    <option value="">Kies een rol</option>
                    <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                    <option value="employee" @selected(old('role', $user->role) === 'employee')>Employee</option>
                    <option value="visitor" @selected(old('role', $user->role) === 'visitor')>Visitor</option>
                </select>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium mb-1">Nieuw wachtwoord</label>
                <input id="password" name="password" type="password"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" />
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white">Opslaan</button>
                <a href="{{ route('users.index') }}" class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-700">Annuleren</a>
            </div>
        </form>
    </div>
</x-layouts.app>