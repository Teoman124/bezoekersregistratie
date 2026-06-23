<x-layouts.app>
    <div class="mx-auto max-w-3xl space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('New message') }}</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Send a message to a user in the mailbox.') }}</p>
        </div>

        <form action="{{ route('mailbox.store') }}" method="POST"
            class="space-y-5 rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            @csrf

            <div>
                <label for="recipient_id"
                    class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Recipient') }}</label>
                <select id="recipient_id" name="recipient_id" required
                    class="w-full rounded-md border-gray-300 bg-white dark:border-gray-700 dark:bg-gray-900">
                    <option value="">{{ __('Choose a user') }}</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(old('recipient_id', $selectedUserId) == $user->id)>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('recipient_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="subject"
                    class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Subject') }}</label>
                <input id="subject" name="subject" type="text" value="{{ old('subject') }}" required
                    class="w-full rounded-md border-gray-300 bg-white dark:border-gray-700 dark:bg-gray-900" />
                @error('subject')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="body"
                    class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Message') }}</label>
                <textarea id="body" name="body" rows="8" required
                    class="w-full rounded-md border-gray-300 bg-white dark:border-gray-700 dark:bg-gray-900">{{ old('body') }}</textarea>
                @error('body')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    {{ __('Send') }}
                </button>
                <a href="{{ route('mailbox.index') }}"
                    class="rounded-md border border-gray-300 px-4 py-2 dark:border-gray-700">
                    {{ __('Cancel') }}
                </a>
            </div>
        </form>
    </div>
</x-layouts.app>