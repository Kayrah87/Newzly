<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel Base</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    </head>
    <body class="font-sans bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 py-8">
            <div class="text-center mb-12">
                <h1 class="text-5xl font-semibold text-gray-900 mb-4">Laravel Base Template</h1>
                <p class="text-xl text-gray-600">A comprehensive Laravel starter template with modern packages and console commands</p>
            </div>

            <div class="bg-white rounded-lg shadow-xs p-8 mb-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">🎉 Installation Successful!</h2>
                <p class="text-gray-600 mb-6">Your Laravel Base template has been successfully set up with the following core components:</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-4 border-l-4 border-blue-500">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Laravel 12</h3>
                        <p class="text-gray-600">Latest stable Laravel framework with all modern features</p>
                    </div>
                    <div class="p-4 border-l-4 border-green-500">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">PEST Testing</h3>
                        <p class="text-gray-600">Modern PHP testing framework included and configured</p>
                    </div>
                    <div class="p-4 border-l-4 border-purple-500">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Laravel Sanctum</h3>
                        <p class="text-gray-600">API authentication system ready to use</p>
                    </div>
                    <div class="p-4 border-l-4 border-orange-500">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tailwind CSS</h3>
                        <p class="text-gray-600">Utility-first CSS framework with Vite integration</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-xs p-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">🚀 Development Setup Complete</h2>
                <p class="text-gray-600 mb-4">Your Laravel 12 application is now ready for development with:</p>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        <span class="text-gray-700">Laravel Framework 12.25.0</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                        <span class="text-gray-700">Tailwind CSS 4 with Vite</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                        <span class="text-gray-700">Alpine.js for reactive components</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                        <span class="text-gray-700">VS Code debug configuration</span>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html><?php /**PATH /home/kay/Work/Repos/Laravel-Base/resources/views/welcome.blade.php ENDPATH**/ ?>