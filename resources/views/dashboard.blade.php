<x-layouts.app>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Dashboard')}}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">
            @if(in_array(auth()->user()?->role, ['admin', 'employee'], true))
                {{ __('Beheer gebruikers, medewerkers, bezoekers en bezoeken vanaf een plek.') }}
            @else
                {{ __('Je bent ingelogd als visitor. Beheerfuncties zijn niet beschikbaar.') }}
            @endif
        </p>
    </div>

    @if(in_array(auth()->user()?->role, ['admin', 'employee'], true))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-6">
            <a href="{{ route('users.index') }}"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Gebruikers') }}</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $stats['users'] }}</p>
                <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">{{ __('Open gebruikersbeheer') }}</p>
            </a>

            <a href="{{ route('employees.index') }}"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Medewerkers') }}</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $stats['employees'] }}</p>
                <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">{{ __('Open medewerkers') }}</p>
            </a>

            <a href="{{ route('visitors.index') }}"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Bezoekers') }}</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $stats['visitors'] }}</p>
                <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">{{ __('Open bezoekers') }}</p>
            </a>

            <a href="{{ route('visits.index') }}"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Bezoeken') }}</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $stats['visits'] }}</p>
                <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">{{ __('Open bezoeken') }}</p>
            </a>

            <a href="{{ route('visits.active') }}"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Aanwezig') }}</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $stats['active_visits'] }}</p>
                <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">{{ __('Actieve bezoekerslijst') }}</p>
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Snel naar beheer') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <a href="{{ route('users.create') }}"
                    class="px-4 py-3 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Nieuwe gebruiker') }}</a>
                <a href="{{ route('employees.create') }}"
                    class="px-4 py-3 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Nieuwe medewerker') }}</a>
                <a href="{{ route('visitors.create') }}"
                    class="px-4 py-3 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Nieuwe bezoeker') }}</a>
                <a href="{{ route('departments.index') }}"
                    class="px-4 py-3 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Afdelingen') }}
                    ({{ $stats['departments'] }})</a>
                <a href="{{ route('notifications.index') }}"
                    class="px-4 py-3 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Notificaties') }}</a>
                <a href="{{ route('visits.create') }}"
                    class="px-4 py-3 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Nieuw bezoek plannen') }}</a>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('Visitor account') }}</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                {{ __('Je bent ingelogd als visitor. Beheerfuncties zoals bezoekers, bezoeken, afdelingen en notificaties zijn niet beschikbaar.') }}
            </p>
        </div>
    @endif

    @if(auth()->check() && auth()->user()->role === 'visitor')
        @php $visitor = auth()->user()->visitor; @endphp
        @if((!$visitor || empty($visitor->company_name)) && !session('visitor_company_prompt_skipped'))
            <div x-data="{ open: true }" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md mx-4">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-3">{{ __('Van welk bedrijf kom je?') }}</h2>

                    <form method="POST" action="{{ route('visitor.company-info.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <x-forms.input name="company_name" label="{{ __('Bedrijf') }}" type="text" placeholder="{{ __('Bedrijfsnaam') }}" autofocus />
                        </div>

                        <x-button type="primary" class="w-full">{{ __('Opslaan') }}</x-button>
                    </form>

                    <form method="POST" action="{{ route('visitor.company-info.skip') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                            {{ __('Overslaan') }}
                        </button>
                    </form>
                </div>
            </div>
        @endif
    @endif

    </x-layouts.app>