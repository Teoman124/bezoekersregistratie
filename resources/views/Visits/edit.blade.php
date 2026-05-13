<x-layouts.app>
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2">Bezoek bewerken</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Werk de gegevens van dit bezoek bij.</p>

        <form action="{{ route('visits.update', $visit) }}" method="POST"
            class="space-y-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            @csrf
            @method('PUT')

            <div>
                <label for="visitor_id" class="block text-sm font-medium mb-1">Bezoeker</label>
                <select name="visitor_id" id="visitor_id" required
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    @foreach ($visitors as $visitor)
                        <option value="{{ $visitor->id }}" @selected(old('visitor_id', $visit->visitor_id) == $visitor->id)>
                            {{ $visitor->user?->name ?? 'Onbekende bezoeker' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="host_employee_id" class="block text-sm font-medium mb-1">Gastheer / medewerker</label>
                <select name="host_employee_id" id="host_employee_id" required
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}" @selected(old('host_employee_id', $visit->host_employee_id) == $employee->id)>
                            {{ $employee->user?->name ?? 'Onbekende medewerker' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="reason_of_visit" class="block text-sm font-medium mb-1">Reden van bezoek</label>
                <textarea name="reason_of_visit" id="reason_of_visit" rows="4"
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">{{ old('reason_of_visit', $visit->reason_of_visit) }}</textarea>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium mb-1">Status</label>
                <select name="status" id="status" required
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    <option value="planned" @selected(old('status', $visit->currentStatus()) === 'planned')>Ingepland
                    </option>
                    <option value="active" @selected(old('status', $visit->currentStatus()) === 'active')>Aanwezig
                    </option>
                    <option value="checked_out" @selected(old('status', $visit->currentStatus()) === 'checked_out')>
                        Uitgecheckt</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="expected_arrival_time" class="block text-sm font-medium mb-1">Verwachte aankomst</label>
                    <input type="datetime-local" name="expected_arrival_time" id="expected_arrival_time"
                        value="{{ old('expected_arrival_time', optional($visit->expected_arrival_time)->format('Y-m-d\TH:i')) }}"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" />
                </div>

                <div>
                    <label for="expected_departure_time" class="block text-sm font-medium mb-1">Verwachte
                        vertrek</label>
                    <input type="datetime-local" name="expected_departure_time" id="expected_departure_time"
                        value="{{ old('expected_departure_time', optional($visit->expected_departure_time)->format('Y-m-d\TH:i')) }}"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="check_in_time" class="block text-sm font-medium mb-1">Inchecktijd</label>
                    <input type="datetime-local" name="check_in_time" id="check_in_time"
                        value="{{ old('check_in_time', optional($visit->check_in_time)->format('Y-m-d\TH:i')) }}"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" />
                </div>

                <div>
                    <label for="check_out_time" class="block text-sm font-medium mb-1">Uitchecktijd</label>
                    <input type="datetime-local" name="check_out_time" id="check_out_time"
                        value="{{ old('check_out_time', optional($visit->check_out_time)->format('Y-m-d\TH:i')) }}"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" />
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white">Opslaan</button>
                <a href="{{ route('visits.index') }}"
                    class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-700">Annuleren</a>
            </div>
        </form>
    </div>
</x-layouts.app>