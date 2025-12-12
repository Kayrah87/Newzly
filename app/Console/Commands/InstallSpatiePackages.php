<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class InstallSpatiePackages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:spatie-packages 
                            {--all : Install all Spatie packages}
                            {--settings : Install Spatie Settings}
                            {--permissions : Install Spatie Permissions}
                            {--media : Install Spatie Media Library}
                            {--logs : Install Spatie Activity Log}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install and configure Spatie packages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Installing Spatie Packages...');

        $packages = [];
        
        if ($this->option('all')) {
            $packages = [
                'spatie/laravel-settings' => 'Settings',
                'spatie/laravel-permission' => 'Permissions',
                'spatie/laravel-medialibrary' => 'Media Library',
                'spatie/laravel-activitylog' => 'Activity Log'
            ];
        } else {
            if ($this->option('settings')) {
                $packages['spatie/laravel-settings'] = 'Settings';
            }
            if ($this->option('permissions')) {
                $packages['spatie/laravel-permission'] = 'Permissions';
            }
            if ($this->option('media')) {
                $packages['spatie/laravel-medialibrary'] = 'Media Library';
            }
            if ($this->option('logs')) {
                $packages['spatie/laravel-activitylog'] = 'Activity Log';
            }
        }

        if (empty($packages) && !$this->option('all')) {
            $packages = $this->askForPackages();
        }

        foreach ($packages as $package => $name) {
            $this->installPackage($package, $name);
        }

        $this->info('✅ Spatie packages installation completed!');
        $this->newLine();
        $this->info('📋 Don\'t forget to:');
        $this->info('  • Run migrations: php artisan migrate');
        $this->info('  • Publish configs: php artisan vendor:publish');
        $this->info('  • Update your User model with appropriate traits');
    }

    private function askForPackages(): array
    {
        $availablePackages = [
            'spatie/laravel-settings' => 'Settings - Store application settings in the database',
            'spatie/laravel-permission' => 'Permissions - Associate users and roles with permissions',
            'spatie/laravel-medialibrary' => 'Media Library - Associate files with Eloquent models',
            'spatie/laravel-activitylog' => 'Activity Log - Log activity inside your Laravel app'
        ];

        $selected = [];
        
        $this->info('Available Spatie packages:');
        foreach ($availablePackages as $package => $description) {
            if ($this->confirm("Install {$description}?")) {
                $selected[$package] = explode(' - ', $description)[0];
            }
        }

        return $selected;
    }

    private function installPackage(string $package, string $name): void
    {
        $this->info("📦 Installing {$name} ({$package})...");
        
        $result = Process::run("composer require {$package}");
        
        if ($result->failed()) {
            $this->error("❌ Failed to install {$name}");
            $this->error($result->errorOutput());
            return;
        }

        $this->info("✅ {$name} installed successfully");

        // Run package-specific setup
        $this->setupPackage($package, $name);
    }

    private function setupPackage(string $package, string $name): void
    {
        switch ($package) {
            case 'spatie/laravel-permission':
                $this->info('🔧 Setting up Spatie Permissions...');
                Process::run('php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"');
                $this->info('  • Published migrations and config');
                break;

            case 'spatie/laravel-settings':
                $this->info('🔧 Setting up Spatie Settings...');
                Process::run('php artisan vendor:publish --provider="Spatie\LaravelSettings\LaravelSettingsServiceProvider" --tag="migrations"');
                $this->info('  • Published settings migration');
                break;

            case 'spatie/laravel-medialibrary':
                $this->info('🔧 Setting up Spatie Media Library...');
                Process::run('php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="migrations"');
                $this->info('  • Published media migrations');
                break;

            case 'spatie/laravel-activitylog':
                $this->info('🔧 Setting up Spatie Activity Log...');
                Process::run('php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"');
                $this->info('  • Published activity log migrations');
                break;
        }
    }
}