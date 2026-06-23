<x-layouts.app>
    <div class="mx-auto max-w-3xl space-y-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('View message') }}</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $notification->sender_id === auth()->id() ? __('This is a sent message.') : __('Open the message and mark it as read automatically.') }}
                </p>
            </div>

            <a href="{{ route('mailbox.create') }}"
                class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                {{ __('New message') }}
            </a>
        </div>

        <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('From') }}</p>
                    <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ $notification->sender?->name ?? __('System') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('To') }}</p>
                    <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ $notification->recipient?->name ?? __('Unknown recipient') }}</p>
                </div>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Subject') }}</p>
                <p class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $notification->title }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Message') }}</p>
                <p class="whitespace-pre-line text-gray-800 dark:text-gray-100">{{ $notification->message }}</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Type') }}</p>
                    <p class="text-gray-900 dark:text-gray-100">
                        {{ $notification->sender_id === auth()->id() ? __('Sent') : __('Received') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Status') }}</p>
                    <p class="text-gray-900 dark:text-gray-100">{{ $notification->read ? __('Read') : __('Unread') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Created at') }}</p>
                    <p class="text-gray-900 dark:text-gray-100">
                        {{ optional($notification->created_at)->format('d-m-Y H:i') ?? '-' }}
                    </p>
                </div>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('mailbox.index', ['folder' => $notification->sender_id === auth()->id() ? 'sent' : null]) }}"
                    class="rounded-md border border-gray-300 px-4 py-2 dark:border-gray-700">
                    {{ __('Back') }}
                </a>

                @if ($notification->recipient_id === auth()->id())
                    <form action="{{ route('mailbox.destroy', $notification) }}" method="POST"
                        onsubmit="return confirm('{{ __('Are you sure you want to delete this message?') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-white hover:bg-red-700">
                            {{ __('Delete') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>