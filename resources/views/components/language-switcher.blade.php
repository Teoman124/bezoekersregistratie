@php
    $currentLocale = app()->getLocale();
@endphp

<div class="flex items-center justify-end gap-2">
    @foreach (['nl' => 'NL', 'en' => 'EN'] as $locale => $label)
        <a href="{{ route('lang.switch', ['locale' => $locale]) }}"
            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold tracking-wide transition {{ $currentLocale === $locale ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600' }}">
            {{ $label }}
        </a>
    @endforeach
</div>