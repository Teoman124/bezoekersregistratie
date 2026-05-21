<x-layouts.app>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Notificaties</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Overzicht van alle systeemnotificaties.</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/40 text-gray-600 dark:text-gray-300">
                <tr>
                    <th class="px-4 py-3">Gebruiker</th>
                    <th class="px-4 py-3">Titel</th>
                    <th class="px-4 py-3">Bericht</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Acties</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($notifications as $notification)
                    <tr>
                        <td class="px-4 py-3">{{ $notification->user?->name ?? 'Onbekend' }}</td>
                        <td class="px-4 py-3">{{ $notification->title }}</td>
                        <td class="px-4 py-3">{{ $notification->message }}</td>
                        <td class="px-4 py-3">
                            @if($notification->read)
                                <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200">Gelezen</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">Ongelezen</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-2 items-center">
                                @include('components.action-buttons', [
                                    'show' => route('notifications.show', $notification),
                                    'edit' => route('notifications.edit', $notification),
                                    'destroy' => route('notifications.destroy', $notification),
                                    'deleteConfirm' => 'Weet je zeker dat je deze notificatie wilt verwijderen?'
                                ])

                                @if(!$notification->read)
                                    <form action="{{ route('notifications.markAsRead', $notification) }}" method="POST" class="inline-flex">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 rounded-full bg-blue-600 text-white text-xs hover:bg-blue-700">Markeer als gelezen</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Geen notificaties gevonden.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>
