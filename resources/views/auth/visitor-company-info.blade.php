<x-layouts.auth :title="__('Company Information')">
    <div
        class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Company Information') }}</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                    {{ __('Please provide your company information so we can better assist you.') }}
                </p>
            </div>

            <form method="POST" action="{{ route('visitor.company-info.store') }}" class="space-y-4">
                @csrf
                <div>
                    <x-forms.input label="{{ __('Company Name') }}" name="company_name" type="text"
                        placeholder="{{ __('Your Company Name') }}" autofocus />
                </div>

                <div class="flex gap-3 pt-2">
                    <x-button type="primary" class="flex-1">{{ __('Continue') }}</x-button>
                    <a href="{{ route('home') }}"
                        class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        {{ __('Skip') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.auth>