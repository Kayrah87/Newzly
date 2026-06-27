@props(['publication', 'title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $title ? $title.' — '.$publication->name : $publication->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="flex flex-col items-center mb-6 text-center">
                @if($publication->logoUrl())
                    <img src="{{ $publication->logoUrl() }}" alt="{{ $publication->name }}" class="h-16 w-16 object-contain rounded mb-3">
                @endif
                <h1 class="text-2xl font-bold">{{ $publication->name }}</h1>
                @if($publication->description)
                    <p class="text-gray-600 mt-1">{{ $publication->description }}</p>
                @endif
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6">
                {{ $slot }}
            </div>

            <p class="text-center text-xs text-gray-400 mt-6">
                Powered by Newzly
            </p>
        </div>
    </div>
</body>
</html>
