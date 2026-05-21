<x-layouts.app>
    <div class="mx-auto max-w-3xl space-y-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Bericht bekijken</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $notification->sender_id === auth()->id() ? 'Dit is een verzonden bericht.' : 'Open het bericht en markeer het automatisch als gelezen.' }}
                </p>
            </div>

            <a href="{{ route('mailbox.create') }}"
                class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                Nieuw bericht
            </a>
        </div>

        <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Van</p>
                    <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ $notification->sender?->name ?? 'Systeem' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Naar</p>
                    <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ $notification->recipient?->name ?? 'Onbekende ontvanger' }}</p>
                </div>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Onderwerp</p>
                <p class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $notification->title }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Bericht</p>
                <p class="whitespace-pre-line text-gray-800 dark:text-gray-100">{{ $notification->message }}</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Type</p>
                    <p class="text-gray-900 dark:text-gray-100">
                        {{ $notification->sender_id === auth()->id() ? 'Verzonden' : 'Ontvangen' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                    <p class="text-gray-900 dark:text-gray-100">{{ $notification->read ? 'Gelezen' : 'Ongelezen' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Aangemaakt</p>
                    <p class="text-gray-900 dark:text-gray-100">
                        {{ optional($notification->created_at)->format('d-m-Y H:i') ?? '-' }}
                    </p>
                </div>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('mailbox.index', ['folder' => $notification->sender_id === auth()->id() ? 'sent' : null]) }}"
                    class="rounded-md border border-gray-300 px-4 py-2 dark:border-gray-700">
                    Terug
                </a>

                @if ($notification->recipient_id === auth()->id())
                    <form action="{{ route('mailbox.destroy', $notification) }}" method="POST"
                        onsubmit="return confirm('Weet je zeker dat je dit bericht wilt verwijderen?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-white hover:bg-red-700">
                            Verwijderen
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>