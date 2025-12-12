<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\File;

class InstallLivewire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:livewire {--volt : Install with VOLT}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install and configure Livewire 3 with optional VOLT';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Installing Livewire 3...');

        // Install Livewire
        $result = Process::run('composer require livewire/livewire');
        
        if ($result->failed()) {
            $this->error('❌ Failed to install Livewire');
            $this->error($result->errorOutput());
            return 1;
        }

        $this->info('✅ Livewire installed successfully');

        // Install VOLT if requested
        if ($this->option('volt') || $this->confirm('Install Livewire VOLT for single-file components?')) {
            $this->installVolt();
        }

        // Create example components
        $this->createExampleComponents();

        // Update layouts
        $this->updateLayouts();

        $this->info('✅ Livewire setup completed!');
        $this->newLine();
        $this->info('📋 Next steps:');
        $this->info('  • Add @livewireStyles and @livewireScripts to your layout');
        $this->info('  • Create components with: php artisan make:livewire ComponentName');
        if ($this->option('volt')) {
            $this->info('  • Create VOLT components with: php artisan make:volt ComponentName');
        }
    }

    private function installVolt(): void
    {
        $this->info('📦 Installing Livewire VOLT...');
        
        $result = Process::run('composer require livewire/volt');
        
        if ($result->failed()) {
            $this->error('❌ Failed to install VOLT');
            $this->error($result->errorOutput());
            return;
        }

        $this->info('✅ VOLT installed successfully');
        
        // Install VOLT
        Process::run('php artisan volt:install');
        $this->info('  • VOLT installed and configured');
    }

    private function createExampleComponents(): void
    {
        $this->info('🔧 Creating example components...');

        // Create Counter component
        $counterComponent = <<<'PHP'
<?php

namespace App\Livewire;

use Livewire\Component;

class Counter extends Component
{
    public $count = 0;

    public function increment()
    {
        $this->count++;
    }

    public function decrement()
    {
        $this->count--;
    }

    public function render()
    {
        return view('livewire.counter');
    }
}
PHP;

        File::ensureDirectoryExists(app_path('Livewire'));
        File::put(app_path('Livewire/Counter.php'), $counterComponent);

        // Create Counter view
        $counterView = <<<'BLADE'
<div class="flex items-center space-x-4 p-4 bg-white rounded-lg shadow-sm">
    <button wire:click="decrement" 
            class="px-4 py-2 bg-red-500 text-white rounded-sm hover:bg-red-600 transition">
        -
    </button>
    
    <span class="text-2xl font-semibold">{{ $count }}</span>
    
    <button wire:click="increment" 
            class="px-4 py-2 bg-blue-500 text-white rounded-sm hover:bg-blue-600 transition">
        +
    </button>
</div>
BLADE;

        File::ensureDirectoryExists(resource_path('views/livewire'));
        File::put(resource_path('views/livewire/counter.blade.php'), $counterView);

        $this->info('  • Created example Counter component');
    }

    private function updateLayouts(): void
    {
        $this->info('🔧 Creating Livewire-ready layout...');

        $appLayout = <<<'BLADE'
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'Laravel Base' }}</title>
        
        <!-- Tailwind CSS (CDN for development) -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        @livewireStyles
    </head>
    <body class="bg-gray-100">
        <div class="min-h-screen">
            <nav class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <h1 class="text-xl font-semibold">Laravel Base</h1>
                        </div>
                    </div>
                </div>
            </nav>

            <main class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>

        @livewireScripts
    </body>
</html>
BLADE;

        File::ensureDirectoryExists(resource_path('views/layouts'));
        File::put(resource_path('views/layouts/app.blade.php'), $appLayout);

        $this->info('  • Created app layout with Livewire support');

        // Update welcome page to use layout
        $welcomePage = <<<'BLADE'
<x-layouts.app>
    <x-slot name="title">Welcome</x-slot>

    <div class="space-y-6">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
            <h2 class="text-2xl font-semibold mb-4">🎉 Livewire is Ready!</h2>
            <p class="text-gray-600 mb-6">Your Laravel Base template now includes Livewire 3 for reactive components.</p>
            
            <h3 class="text-lg font-medium mb-4">Example: Counter Component</h3>
            <livewire:counter />
        </div>
        
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-3">🚀 Next Steps</h3>
            <ul class="space-y-2 text-gray-600">
                <li>• Create components: <code class="bg-gray-100 px-2 py-1 rounded-sm">php artisan make:livewire ComponentName</code></li>
                <li>• Use in views: <code class="bg-gray-100 px-2 py-1 rounded-sm">&lt;livewire:component-name /&gt;</code></li>
                <li>• Add real-time features with wire:poll, wire:model, wire:click</li>
            </ul>
        </div>
    </div>
</x-layouts.app>
BLADE;

        File::put(resource_path('views/welcome.blade.php'), $welcomePage);
        $this->info('  • Updated welcome page with Livewire example');
    }
}