<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Bezoekersregistratie' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 font-sans antialiased text-gray-900">
    <div class="min-h-screen flex flex-col">
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Bezoekersregistratie</h1>
            </div>
        </header>

        <main class="flex-grow max-w-7xl w-full mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                {{ $slot }}
            </div>
        </main>

        <footer class="bg-white text-center py-4 text-sm text-gray-500 mt-auto">
            <p>&copy; {{ date('Y') }} Bezoekersregistratie</p>
        </footer>
    </div>
</body>

</html>npm run dev