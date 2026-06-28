<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|playfair-display:700,800,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-ink antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-newsprint">
            <div class="text-center">
                <a href="/" class="font-display text-4xl font-black tracking-tight text-ink">
                    Newzly<span class="text-press-600">.</span>
                </a>
                <p class="mt-1 np-kicker">Newsletter Management</p>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-white border-t-4 border-press-600 np-card">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
