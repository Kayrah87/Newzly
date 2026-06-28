@props(['publication', 'title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $title ? $title.' — '.$publication->name : $publication->name }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|playfair-display:700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-newsprint text-ink antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="flex flex-col items-center mb-6 text-center">
                @if($publication->logoUrl())
                    <img src="{{ $publication->logoUrl() }}" alt="{{ $publication->name }}" class="h-16 w-16 object-contain border border-ink/10 bg-white p-1 mb-3">
                @endif
                <div class="w-full np-rule pt-3">
                    <h1 class="font-display text-3xl font-black leading-tight">{{ $publication->name }}</h1>
                </div>
                @if($publication->description)
                    <p class="text-ink-soft mt-2">{{ $publication->description }}</p>
                @endif
            </div>

            <div class="bg-white border-t-4 border-press-600 np-card p-6">
                {{ $slot }}
            </div>

            <p class="text-center text-xs text-ink-soft/60 mt-6 uppercase tracking-[0.15em]">
                Powered by Newzly
            </p>
        </div>
    </div>
</body>
</html>
