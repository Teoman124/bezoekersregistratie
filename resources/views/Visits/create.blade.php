<x-layouts.app>
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Nieuw bezoek</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Plan een bezoek in voor een bezoeker en een gastheer.</p>

        <form action="{{ route('visits.store') }}" method="POST"
            class="space-y-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            @csrf

            <div>
                <label for="visitor_id" class="block text-sm font-medium mb-1">Bezoeker</label>
                <select name="visitor_id" id="visitor_id" required
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    <option value="">Kies een bezoeker</option>
                    @foreach ($visitors as $visitor)
                        <option value="{{ $visitor->id }}" @selected(old('visitor_id', request('visitor_id')) == $visitor->id)>
                            {{ $visitor->user?->name ?? 'Onbekende bezoeker' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="host_employee_id" class="block text-sm font-medium mb-1">Gastheer / medewerker</label>
                <select name="host_employee_id" id="host_employee_id" required
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    <option value="">Kies een medewerker</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}" @selected(old('host_employee_id', request('host_employee_id')) == $employee->id)>
                            {{ $employee->user?->name ?? 'Onbekende medewerker' }}
                            @if($employee->department)
                                - {{ $employee->department->name }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="reason_of_visit" class="block text-sm font-medium mb-1">Reden van bezoek</label>
                <textarea name="reason_of_visit" id="reason_of_visit" rows="3"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">{{ old('reason_of_visit') }}</textarea>
            </div>

            <div>
                <label for="route_description" class="block text-sm font-medium mb-1">Routebeschrijving / Bericht voor bezoeker (optioneel)</label>
                <textarea name="route_description" id="route_description" rows="3" placeholder="Bijv: Parkeer op P1 en meld je bij de hoofdingang."
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">{{ old('route_description') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Deze tekst wordt direct meegestuurd in de uitnodigingsmail naar de bezoeker.</p>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium mb-1">Status</label>
                <select name="status" id="status"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    <option value="planned" @selected(old('status', 'planned') === 'planned')>Ingepland</option>
                    <option value="active" @selected(old('status') === 'active')>Aanwezig</option>
                    <option value="checked_out" @selected(old('status') === 'checked_out')>Uitgecheckt</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="expected_arrival_time" class="block text-sm font-medium mb-1">Verwachte aankomst</label>
                    <input type="datetime-local" name="expected_arrival_time" id="expected_arrival_time"
                        value="{{ old('expected_arrival_time') }}" required
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" />
                </div>

                <div>
                    <label for="expected_departure_time" class="block text-sm font-medium mb-1">Verwachte vertrek</label>
                    <input type="datetime-local" name="expected_departure_time" id="expected_departure_time"
                        value="{{ old('expected_departure_time') }}"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" />
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white">Opslaan</button>
                <a href="{{ route('visits.index') }}" class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-700">Annuleren</a>
            </div>
        </form>
    </div>
</x-layouts.app>