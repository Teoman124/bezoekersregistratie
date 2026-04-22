<x-layouts.app>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Dashboard')}}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Beheer gebruikers, medewerkers, bezoekers en bezoeken vanaf een plek.') }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <a href="{{ route('users.index') }}" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Gebruikers') }}</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $stats['users'] }}</p>
            <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">{{ __('Open gebruikersbeheer') }}</p>
        </a>

        <a href="{{ route('employees.index') }}" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Medewerkers') }}</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $stats['employees'] }}</p>
            <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">{{ __('Open medewerkers') }}</p>
        </a>

        <a href="{{ route('visitors.index') }}" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Bezoekers') }}</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $stats['visitors'] }}</p>
            <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">{{ __('Open bezoekers') }}</p>
        </a>

        <a href="{{ route('visits.index') }}" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Bezoeken') }}</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $stats['visits'] }}</p>
            <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">{{ __('Open bezoeken') }}</p>
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Snel naar beheer') }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @if(auth()->user()?->role === 'admin')
                <a href="{{ route('users.create') }}" class="px-4 py-3 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Nieuwe gebruiker') }}</a>
            @endif
            <a href="{{ route('employees.create') }}" class="px-4 py-3 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Nieuwe medewerker') }}</a>
            <a href="{{ route('visitors.create') }}" class="px-4 py-3 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Nieuwe bezoeker') }}</a>
            <a href="{{ route('departments.index') }}" class="px-4 py-3 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Afdelingen') }} ({{ $stats['departments'] }})</a>
            <a href="{{ route('notifications.index') }}" class="px-4 py-3 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Notificaties') }}</a>
            <a href="{{ route('visits.create') }}" class="px-4 py-3 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Nieuw bezoek plannen') }}</a>
        </div>
    </div>

</x-layouts.app>
