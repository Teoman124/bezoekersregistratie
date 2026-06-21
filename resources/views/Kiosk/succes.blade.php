<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="5;url={{ route('kiosk.reset') }}">
    <title>{{ __('Succesvol Aangemeld') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-50 h-screen flex items-center justify-center">

    <div class="bg-white p-10 rounded-lg shadow-xl w-full max-w-lg text-center">
        <div class="text-green-500 mb-4">
            <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        
        <h1 class="text-4xl font-bold text-gray-800 mb-4">{{ __('Aanmelding Gelukt!') }}</h1>
        <p class="text-xl text-gray-600 mb-8">
            {{ __('Welkom. We hebben :name een seintje gegeven dat je er bent. Neem gerust even plaats in de hal.', ['name' => '<strong>' . session('host_name') . '</strong>']) }}
        </p>

        <p class="text-sm text-gray-400">
            {{ __('Dit scherm keert automatisch terug...') }}
        </p>
    </div>

</body>
</html>