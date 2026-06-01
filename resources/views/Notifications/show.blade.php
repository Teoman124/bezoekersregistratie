<x-layouts.app>
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Notificatie details</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">Bekijk de inhoud en status van deze notificatie.</p>
            </div>
            @if(auth()->user()?->role === 'admin')
                <a href="{{ route('notifications.edit', $notification) }}" class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white">Bewerken</a>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 space-y-4">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Gebruiker</p>
                <p class="text-lg font-medium text-gray-800 dark:text-gray-100">{{ $notification->user?->name ?? 'Onbekend' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Titel</p>
                <p class="text-lg font-medium text-gray-800 dark:text-gray-100">{{ $notification->title }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Bericht</p>
                <p class="text-gray-800 dark:text-gray-100 whitespace-pre-line">{{ $notification->message }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                <p class="text-lg font-medium text-gray-800 dark:text-gray-100">{{ $notification->read ? 'Gelezen' : 'Ongelezen' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Aangemaakt</p>
                <p class="text-lg font-medium text-gray-800 dark:text-gray-100">{{ optional($notification->created_at)->format('d-m-Y H:i') ?? '-' }}</p>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('notifications.index') }}" class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-700">Terug</a>
        </div>
    </div>
</x-layouts.app>