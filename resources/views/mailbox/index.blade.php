<x-layouts.app>
    <div class="flex flex-col gap-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Mailbox</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">Bekijk je inbox en verzonden berichten op één plek.
                </p>
            </div>

            <a href="{{ route('mailbox.create') }}"
                class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                Nieuw bericht
            </a>
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
            <div class="flex flex-wrap gap-2 border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                <a href="{{ route('mailbox.index') }}"
                    class="rounded-md px-3 py-2 text-sm font-medium {{ $folder !== 'sent' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200' }}">
                    Inbox ({{ $inboxCount }})
                </a>
                <a href="{{ route('mailbox.index', ['folder' => 'sent']) }}"
                    class="rounded-md px-3 py-2 text-sm font-medium {{ $folder === 'sent' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200' }}">
                    Verzonden ({{ $sentCount }})
                </a>
                <div class="ml-auto text-sm text-gray-600 dark:text-gray-400">
                    {{ $notifications->count() }} berichten
                </div>
            </div>

            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($notifications as $notification)
                    <a href="{{ route('mailbox.show', $notification) }}"
                        class="block px-4 py-4 transition hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $notification->title }}
                                    </h2>

                                    <span
                                        class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                                        {{ $folder === 'sent' ? 'Verzonden' : 'Ontvangen' }}
                                    </span>

                                    @if ($folder !== 'sent' && !$notification->read)
                                        <span
                                            class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                            Ongelezen
                                        </span>
                                    @endif
                                </div>

                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ \Illuminate\Support\Str::limit($notification->message, 120) }}
                                </p>

                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    @if ($folder === 'sent')
                                        Naar: {{ $notification->recipient?->name ?? 'Onbekende ontvanger' }} •
                                    @else
                                        Van: {{ $notification->sender?->name ?? 'Systeem' }} •
                                    @endif
                                    {{ optional($notification->created_at)->format('d-m-Y H:i') ?? '-' }}
                                </p>
                            </div>

                            <div class="text-sm font-medium text-blue-600">Bekijken</div>
                        </div>
                    </a>
                @empty
                    <div class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                        {{ $folder === 'sent' ? 'Je hebt nog geen verzonden berichten.' : 'Je inbox is leeg.' }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>