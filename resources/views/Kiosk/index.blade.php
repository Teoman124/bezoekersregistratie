<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Welkom Kiosk') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center relative">

    <div class="absolute top-6 right-8 text-lg font-bold">
        <a href="{{ route('lang.switch', 'nl') }}" class="{{ app()->getLocale() == 'nl' ? 'text-blue-600' : 'text-gray-400' }}">NL</a> 
        <span class="text-gray-300 mx-2">|</span> 
        <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() == 'en' ? 'text-blue-600' : 'text-gray-400' }}">EN</a>
    </div>

    <div class="bg-white p-10 rounded-lg shadow-xl w-full max-w-md text-center">
        <h1 class="text-3xl font-bold mb-2">{{ __('Welkom') }}</h1>
        <p class="text-gray-600 mb-8">{{ __('Vul je naam in om je aan te melden voor je afspraak.') }}</p>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                {{ __(session('error')) }}
            </div>
        @endif

        <form action="{{ route('kiosk.checkin') }}" method="POST">
            @csrf
            <div class="mb-6">
                <input type="text" name="name" placeholder="{{ __('Je volledige naam') }}" required autofocus
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 text-lg">
            </div>
            
            <button type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg text-lg transition duration-200">
                {{ __('Aanmelden') }}
            </button>
        </form>
    </div>

</body>
</html>