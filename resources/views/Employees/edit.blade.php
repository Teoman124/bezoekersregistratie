<x-layouts.app>
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2">Medewerker bewerken</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Werk de medewerker en bijbehorende gegevens bij.</p>

        <form action="{{ route('employees.update', $employee) }}" method="POST"
            class="space-y-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            @csrf
            @method('PUT')

            <div>
                <label for="user_id" class="block text-sm font-medium mb-1">Gebruiker (rol: employee)</label>
                <select id="user_id" name="user_id" required
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    <option value="">Kies een gebruiker</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(old('user_id', $employee->user_id) == $user->id)>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="department_id" class="block text-sm font-medium mb-1">Afdeling</label>
                <select id="department_id" name="department_id" required
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    <option value="">Kies een afdeling</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" @selected(old('department_id', $employee->department_id) == $department->id)>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="function" class="block text-sm font-medium mb-1">Functie</label>
                <input id="function" name="function" type="text" value="{{ old('function', $employee->function) }}"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" />
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white">Opslaan</button>
                <a href="{{ route('employees.index') }}" class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-700">Annuleren</a>
            </div>
        </form>
    </div>
</x-layouts.app>