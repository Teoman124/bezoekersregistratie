<x-layouts.auth :title="__('Visitor Login')">
    <div
        class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <div class="mb-3">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Visitor Sign In') }}</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ __('Sign in anonymously with only your name.') }}
                </p>
            </div>

            <form method="POST" action="{{ route('visitor.login.store') }}" class="space-y-3">
                @csrf
                <div>
                    <x-forms.input label="{{ __('Name') }}" name="name" type="text" placeholder="{{ __('Your Name') }}" autofocus />
                </div>

                <x-button type="primary" class="w-full">{{ __('Sign in as Visitor') }}</x-button>
            </form>

            <div class="text-center mt-6">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('No visitor profile yet?') }}
                    <a href="{{ route('visitor.register') }}"
                        class="text-blue-600 dark:text-blue-400 hover:underline font-medium">{{ __('Register with name') }}</a>
                </p>
            </div>

            <div class="text-center mt-3">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Admin/employee account?') }}
                    <a href="{{ route('login') }}"
                        class="text-blue-600 dark:text-blue-400 hover:underline font-medium">{{ __('Sign in with account') }}</a>
                </p>
            </div>
        </div>
    </div>
</x-layouts.auth>