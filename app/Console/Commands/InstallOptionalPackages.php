<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class InstallOptionalPackages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:optional';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interactive installer for optional Laravel packages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Optional Package Installer');
        $this->info('Select which packages you\'d like to install:');
        $this->newLine();

        $packages = [
            'multitenancy' => [
                'package' => 'spatie/laravel-multitenancy',
                'name' => 'Spatie Multi Tenancy',
                'description' => 'Multi-tenant applications made easy'
            ],
            'filament' => [
                'package' => 'filament/filament',
                'name' => 'Filament Admin Panel',
                'description' => 'Beautiful admin panel for Laravel'
            ],
            'socialite' => [
                'package' => 'laravel/socialite',
                'name' => 'Laravel Socialite',
                'description' => 'OAuth authentication with social providers'
            ],
            'cashier-stripe' => [
                'package' => 'laravel/cashier',
                'name' => 'Laravel Cashier (Stripe)',
                'description' => 'Stripe billing integration'
            ],
            'telescope' => [
                'package' => 'laravel/telescope',
                'name' => 'Laravel Telescope',
                'description' => 'Debug assistant for Laravel'
            ],
            'horizon' => [
                'package' => 'laravel/horizon',
                'name' => 'Laravel Horizon',
                'description' => 'Queue monitoring dashboard'
            ],
            'octane' => [
                'package' => 'laravel/octane',
                'name' => 'Laravel Octane',
                'description' => 'Supercharge application performance'
            ],
            'blueprint' => [
                'package' => 'laravel-shift/blueprint',
                'name' => 'Laravel Blueprint',
                'description' => 'Code generation tool'
            ]
        ];

        $selected = [];
        foreach ($packages as $key => $package) {
            if ($this->confirm("Install {$package['name']}? ({$package['description']})")) {
                $selected[$key] = $package;
            }
        }

        if (empty($selected)) {
            $this->info('No packages selected. Goodbye! 👋');
            return;
        }

        foreach ($selected as $key => $package) {
            $this->installPackage($key, $package);
        }

        $this->info('✅ All selected packages installed!');
        $this->newLine();
        $this->info('📋 Remember to check each package\'s documentation for additional setup steps.');
    }

    private function installPackage(string $key, array $package): void
    {
        $this->info("📦 Installing {$package['name']}...");
        
        $result = Process::run("composer require {$package['package']}");
        
        if ($result->failed()) {
            $this->error("❌ Failed to install {$package['name']}");
            $this->error($result->errorOutput());
            return;
        }

        $this->info("✅ {$package['name']} installed successfully");

        // Run package-specific setup
        $this->setupPackage($key, $package);
    }

    private function setupPackage(string $key, array $package): void
    {
        switch ($key) {
            case 'multitenancy':
                $this->setupMultiTenancy();
                break;

            case 'filament':
                $this->setupFilament();
                break;

            case 'socialite':
                $this->setupSocialite();
                break;

            case 'cashier-stripe':
                $this->setupCashier();
                break;

            case 'telescope':
                $this->setupTelescope();
                break;

            case 'horizon':
                $this->setupHorizon();
                break;

            case 'octane':
                $this->setupOctane();
                break;

            case 'blueprint':
                $this->setupBlueprint();
                break;
        }
    }

    private function setupMultiTenancy(): void
    {
        $this->info('🔧 Setting up Multi Tenancy...');
        Process::run('php artisan vendor:publish --provider="Spatie\Multitenancy\MultitenancyServiceProvider" --tag="migrations"');
        $this->info('  • Published multi-tenancy migrations');
        $this->info('  • Remember to configure your tenant model and middleware');
    }

    private function setupFilament(): void
    {
        $this->info('🔧 Setting up Filament...');
        Process::run('php artisan filament:install --panels');
        $this->info('  • Filament panels installed');
        $this->info('  • Create admin user with: php artisan make:filament-user');
    }

    private function setupSocialite(): void
    {
        $this->info('🔧 Setting up Socialite...');
        $this->info('  • Add your social provider credentials to .env file');
        $this->info('  • Configure providers in config/services.php');
    }

    private function setupCashier(): void
    {
        $this->info('🔧 Setting up Cashier...');
        Process::run('php artisan vendor:publish --tag="cashier-migrations"');
        $this->info('  • Published Cashier migrations');
        $this->info('  • Add your Stripe keys to .env file');
    }

    private function setupTelescope(): void
    {
        $this->info('🔧 Setting up Telescope...');
        Process::run('php artisan telescope:install');
        $this->info('  • Telescope installed and configured');
        $this->info('  • Access at /telescope');
    }

    private function setupHorizon(): void
    {
        $this->info('🔧 Setting up Horizon...');
        Process::run('php artisan horizon:install');
        $this->info('  • Horizon installed and configured');
        $this->info('  • Start with: php artisan horizon');
        $this->info('  • Access dashboard at /horizon');
    }

    private function setupOctane(): void
    {
        $this->info('🔧 Setting up Octane...');
        
        $server = $this->choice('Choose Octane server:', [
            'swoole' => 'Swoole (recommended)',
            'roadrunner' => 'RoadRunner',
        ], 'swoole');

        Process::run("php artisan octane:install --server={$server}");
        $this->info("  • Octane installed with {$server} server");
        $this->info("  • Start with: php artisan octane:start");
    }

    private function setupBlueprint(): void
    {
        $this->info('🔧 Setting up Blueprint...');
        Process::run('php artisan vendor:publish --tag=blueprint-config');
        $this->info('  • Blueprint configuration published');
        $this->info('  • Use blueprint:walkthrough command for interactive model generation');
    }
}