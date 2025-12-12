<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\File;

class InstallFrontend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:frontend {--tailwind4 : Install Tailwind CSS v4}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install and configure frontend assets with Tailwind CSS and Alpine.js';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Setting up frontend assets...');

        // Install NPM dependencies
        $this->installNpmPackages();

        // Setup Tailwind CSS
        $this->setupTailwind();

        // Setup Vite configuration
        $this->setupVite();

        // Create CSS and JS files
        $this->createAssetFiles();

        $this->info('✅ Frontend setup completed!');
        $this->newLine();
        $this->info('📋 Next steps:');
        $this->info('  • Run: npm run dev (for development)');
        $this->info('  • Run: npm run build (for production)');
        $this->info('  • Add @vite directive to your layout files');
    }

    private function installNpmPackages(): void
    {
        $this->info('📦 Installing NPM packages...');

        $packages = [
            '@tailwindcss/forms',
            '@tailwindcss/typography',
            '@tailwindcss/aspect-ratio',
            'alpinejs',
            'autoprefixer',
            'postcss',
            'vite',
            'laravel-vite-plugin'
        ];

        if ($this->option('tailwind4')) {
            $packages[] = 'tailwindcss@next';
        } else {
            $packages[] = 'tailwindcss';
        }

        $result = Process::run('npm install ' . implode(' ', $packages) . ' --save-dev');
        
        if ($result->failed()) {
            $this->error('❌ Failed to install NPM packages');
            $this->error($result->errorOutput());
            return;
        }

        $this->info('✅ NPM packages installed');
    }

    private function setupTailwind(): void
    {
        $this->info('🔧 Setting up Tailwind CSS...');

        // Initialize Tailwind
        Process::run('npx tailwindcss init -p');

        // Update Tailwind config
        $tailwindConfig = $this->option('tailwind4') ? $this->getTailwind4Config() : $this->getTailwind3Config();
        File::put(base_path('tailwind.config.js'), $tailwindConfig);

        $this->info('  • Tailwind configuration created');
    }

    private function getTailwind3Config(): string
    {
        return <<<'JS'
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./app/**/*.php",
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('@tailwindcss/aspect-ratio'),
  ],
}
JS;
    }

    private function getTailwind4Config(): string
    {
        return <<<'JS'
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./app/**/*.php",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
JS;
    }

    private function setupVite(): void
    {
        $this->info('🔧 Setting up Vite configuration...');

        $viteConfig = <<<'JS'
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
JS;

        File::put(base_path('vite.config.js'), $viteConfig);
        $this->info('  • Vite configuration created');
    }

    private function createAssetFiles(): void
    {
        $this->info('🔧 Creating asset files...');

        // Create CSS directory and file
        File::ensureDirectoryExists(resource_path('css'));
        $appCss = <<<'CSS'
@tailwind base;
@tailwind components;
@tailwind utilities;

/* Custom styles */
@layer base {
    html {
        font-family: 'Inter', system-ui, sans-serif;
    }
}

@layer components {
    .btn {
        @apply inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150;
    }
    
    .btn-secondary {
        @apply bg-gray-600 hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:ring-gray-500;
    }
    
    .btn-danger {
        @apply bg-red-600 hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:ring-red-500;
    }
    
    .card {
        @apply bg-white overflow-hidden shadow-xs rounded-lg;
    }
    
    .card-body {
        @apply p-6;
    }
    
    .form-input {
        @apply block w-full rounded-md border-gray-300 shadow-xs focus:border-blue-500 focus:ring-blue-500;
    }
    
    .form-label {
        @apply block text-sm font-medium text-gray-700 mb-1;
    }
}

@layer utilities {
    .text-balance {
        text-wrap: balance;
    }
}
CSS;

        File::put(resource_path('css/app.css'), $appCss);

        // Create JS directory and file
        File::ensureDirectoryExists(resource_path('js'));
        $appJs = <<<'JS'
import './bootstrap';
import Alpine from 'alpinejs';

// Make Alpine available globally
window.Alpine = Alpine;

// Start Alpine
Alpine.start();

// Custom JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('Laravel Base: Frontend assets loaded!');
});
JS;

        File::put(resource_path('js/app.js'), $appJs);

        // Create bootstrap file
        $bootstrapJs = <<<'JS'
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CSRF token
const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}
JS;

        File::put(resource_path('js/bootstrap.js'), $bootstrapJs);

        $this->info('  • Asset files created');
        $this->info('    - resources/css/app.css (with Tailwind utilities and custom components)');
        $this->info('    - resources/js/app.js (with Alpine.js setup)');
    }
}