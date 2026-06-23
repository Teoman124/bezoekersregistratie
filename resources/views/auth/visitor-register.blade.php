<x-layouts.auth :title="__('Visitor Registration')">
    <div
        class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <div class="mb-3">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Register as Visitor') }}</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ __('Register anonymously with only your name.') }}
                </p>
            </div>

            <form method="POST" action="{{ route('visitor.register.store') }}" class="space-y-3">
                @csrf
                <div>
                    <x-forms.input label="{{ __('Name') }}" name="name" type="text" placeholder="{{ __('Your Name') }}" autofocus />
                </div>

                <x-button type="primary" class="w-full">{{ __('Continue as Visitor') }}</x-button>
            </form>

            <div class="text-center mt-6">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Already registered as visitor?') }}
                    <a href="{{ route('visitor.login') }}"
                        class="text-blue-600 dark:text-blue-400 hover:underline font-medium">{{ __('Sign in with name') }}</a>
                </p>
            </div>

            <div class="text-center mt-3">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Need full account (admin/employee)?') }}
                    <a href="{{ route('register') }}"
                        class="text-blue-600 dark:text-blue-400 hover:underline font-medium">{{ __('Create account') }}</a>
                </p>
            </div>
        </div>
    </div>
</x-layouts.auth>