<x-layouts.app>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Notifications') }}</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Overview of all system notifications.') }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/40 text-gray-600 dark:text-gray-300">
                <tr>
                    <th class="px-4 py-3">{{ __('User') }}</th>
                    <th class="px-4 py-3">{{ __('Title') }}</th>
                    <th class="px-4 py-3">{{ __('Message') }}</th>
                    <th class="px-4 py-3">{{ __('Status') }}</th>
                    <th class="px-4 py-3">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($notifications as $notification)
                    <tr>
                        <td class="px-4 py-3">{{ $notification->user?->name ?? __('Unknown') }}</td>
                        <td class="px-4 py-3">{{ $notification->title }}</td>
                        <td class="px-4 py-3">{{ $notification->message }}</td>
                        <td class="px-4 py-3">
                            @if($notification->read)
                                <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200">{{ __('Read') }}</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">{{ __('Unread') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-2 items-center">
                                @include('components.action-buttons', [
                                    'show' => route('notifications.show', $notification),
                                    'edit' => auth()->user()?->role === 'admin' ? route('notifications.edit', $notification) : null,
                                    'destroy' => auth()->user()?->role === 'admin' ? route('notifications.destroy', $notification) : null,
                                    'deleteConfirm' => __('Are you sure you want to delete this notification?')
                                ])

                                @if(!$notification->read)
                                    <form action="{{ route('notifications.markAsRead', $notification) }}" method="POST" class="inline-flex">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 rounded-full bg-blue-600 text-white text-xs hover:bg-blue-700">{{ __('Mark as read') }}</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">{{ __('No notifications found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>
