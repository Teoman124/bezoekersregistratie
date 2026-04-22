<x-layouts.app>
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Bezoek details</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">Bekijk alle informatie van dit bezoek.</p>
            </div>
            <a href="{{ route('visits.edit', $visit) }}" class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white">Bewerken</a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 space-y-4">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Bezoeker</p>
                <p class="text-lg font-medium text-gray-800 dark:text-gray-100">{{ $visit->visitor?->user?->name ?? 'Onbekend' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Medewerker</p>
                <p class="text-lg font-medium text-gray-800 dark:text-gray-100">{{ $visit->employee?->user?->name ?? 'Onbekend' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Reden</p>
                <p class="text-gray-800 dark:text-gray-100">{{ $visit->reason_of_visit ?: 'Geen reden ingevuld' }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Verwachte aankomst</p>
                    <p class="text-gray-800 dark:text-gray-100">{{ $visit->expected_arrival_time?->format('d-m-Y H:i') ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Verwachte vertrek</p>
                    <p class="text-gray-800 dark:text-gray-100">{{ $visit->expected_departure_time?->format('d-m-Y H:i') ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Inchecktijd</p>
                    <p class="text-gray-800 dark:text-gray-100">{{ $visit->check_in_time?->format('d-m-Y H:i') ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Uitchecktijd</p>
                    <p class="text-gray-800 dark:text-gray-100">{{ $visit->check_out_time?->format('d-m-Y H:i') ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('visits.index') }}" class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-700">Terug</a>
            @if(!$visit->check_in_time)
                <a href="{{ route('visits.checkin', $visit) }}" class="px-4 py-2 rounded-md bg-green-600 hover:bg-green-700 text-white">Inchecken</a>
            @endif
            @if($visit->check_in_time && !$visit->check_out_time)
                <a href="{{ route('visits.checkout', $visit) }}" class="px-4 py-2 rounded-md bg-amber-600 hover:bg-amber-700 text-white">Uitchecken</a>
            @endif
        </div>
    </div>
</x-layouts.app>
