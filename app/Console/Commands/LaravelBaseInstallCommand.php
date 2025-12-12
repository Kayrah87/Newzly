<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use Exception;

class LaravelBaseInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-base:install
                            {--name= : The application name (default: Laravel-Base)}
                            {--user-name= : The default user name (default: Admin)}
                            {--user-email= : The default user email (default: admin@example.com)}
                            {--user-password= : The default user password (default: password)}
                            {--skip-composer : Skip running composer install}
                            {--skip-npm : Skip running npm install}
                            {--skip-user : Skip creating the base user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install and setup the Laravel Base application with all dependencies and initial configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting Laravel Base installation...');
        $this->newLine();

        // Get user input for configuration if not provided via options
        $this->gatherUserInput();

        // Show configuration summary
        $this->displayConfigurationSummary();

        // Confirm before proceeding
        if (!$this->confirm('Proceed with installation?', true)) {
            $this->info('Installation cancelled.');
            return Command::SUCCESS;
        }

        try {
            // Step 1: Setup environment file
            $this->setupEnvironment();

            // Step 2: Generate application key
            $this->generateApplicationKey();

            // Step 3: Install Composer dependencies
            if (!$this->option('skip-composer')) {
                $this->installComposerDependencies();
            }

            // Step 4: Install NPM dependencies
            if (!$this->option('skip-npm')) {
                $this->installNpmDependencies();
            }

            // Step 5: Create database
            $this->createDatabase();

            // Step 6: Run database migrations
            $this->runMigrations();

            // Step 7: Create base user
            if (!$this->option('skip-user')) {
                $this->createBaseUser();
            }

            // Step 8: Set application name
            $this->setApplicationName();

            // Step 9: Build frontend assets
            if (!$this->option('skip-npm')) {
                $this->buildAssets();
            }

            $this->newLine();
            $this->info('✅ Laravel Base installation completed successfully!');
            $this->displaySummary();

        } catch (Exception $e) {
            $this->error('❌ Installation failed: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Gather user input for configuration
     */
    private function gatherUserInput(): void
    {
        $this->info('📝 Configuration Setup');
        $this->info('Please provide the following information (press Enter to use defaults):');
        $this->newLine();

        // Application name
        if (!$this->option('name')) {
            $appName = $this->ask('Application name', 'Laravel-Base');
            $this->input->setOption('name', $appName);
        }

        // User details
        if (!$this->option('skip-user')) {
            if (!$this->option('user-name')) {
                $userName = $this->ask('Admin user name', 'Admin');
                $this->input->setOption('user-name', $userName);
            }

            if (!$this->option('user-email')) {
                $userEmail = $this->ask('Admin user email', 'admin@example.com');
                $this->input->setOption('user-email', $userEmail);
            }

            if (!$this->option('user-password')) {
                $userPassword = $this->secret('Admin user password (min 8 characters)');
                if (empty($userPassword)) {
                    $userPassword = 'password';
                }
                while (strlen($userPassword) < 8) {
                    $this->error('Password must be at least 8 characters long.');
                    $userPassword = $this->secret('Admin user password (min 8 characters)');
                    if (empty($userPassword)) {
                        $userPassword = 'password';
                    }
                }
                $this->input->setOption('user-password', $userPassword);
            }
        }

        // Installation options
        if (!$this->option('skip-composer') && !$this->option('skip-npm')) {
            $this->newLine();
            $this->info('📦 Installation Options');

            if ($this->confirm('Install Composer dependencies?', true)) {
                // Keep default (don't skip)
            } else {
                $this->input->setOption('skip-composer', true);
            }

            if ($this->confirm('Install NPM dependencies and build assets?', true)) {
                // Keep default (don't skip)
            } else {
                $this->input->setOption('skip-npm', true);
            }
        }

        $this->newLine();
    }

    /**
     * Display configuration summary before installation
     */
    private function displayConfigurationSummary(): void
    {
        $this->info('📋 Installation Configuration:');
        $this->info('────────────────────────────────────────');
        $this->info('• Application Name: ' . ($this->option('name') ?: 'Laravel-Base'));

        if (!$this->option('skip-user')) {
            $this->info('• Admin User: ' . ($this->option('user-name') ?: 'Admin'));
            $this->info('• Admin Email: ' . ($this->option('user-email') ?: 'admin@example.com'));
            $this->info('• Admin Password: ' . str_repeat('*', strlen($this->option('user-password') ?: 'password')));
        } else {
            $this->info('• Admin User: Skipped');
        }

        $this->info('• Composer Install: ' . ($this->option('skip-composer') ? 'Skipped' : 'Yes'));
        $this->info('• NPM Install: ' . ($this->option('skip-npm') ? 'Skipped' : 'Yes'));
        $this->newLine();
    }

    /**
     * Setup the environment file
     */
    private function setupEnvironment(): void
    {
        $this->info('📋 Setting up environment file...');

        $envExamplePath = base_path('.env.example');
        $envPath = base_path('.env');

        if (!File::exists($envExamplePath)) {
            throw new Exception('.env.example file not found');
        }

        if (File::exists($envPath)) {
            if (!$this->confirm('⚠️  .env file already exists. Overwrite it?', false)) {
                $this->info('  • Skipping .env file creation');
                return;
            }
        }

        File::copy($envExamplePath, $envPath);
        $this->info('  • Environment file created (.env)');
    }

    /**
     * Generate the application key
     */
    private function generateApplicationKey(): void
    {
        $this->info('🔑 Generating application key...');

        $result = Process::run('php artisan key:generate --ansi');

        if ($result->failed()) {
            throw new Exception('Failed to generate application key: ' . $result->errorOutput());
        }

        $this->info('  • Application key generated');
    }

    /**
     * Install Composer dependencies
     */
    private function installComposerDependencies(): void
    {
        $this->info('📦 Installing Composer dependencies...');

        $result = Process::run('composer install --no-dev --optimize-autoloader');

        if ($result->failed()) {
            $this->warn('  • Composer install failed, trying with dev dependencies...');
            $result = Process::run('composer install');

            if ($result->failed()) {
                throw new Exception('Failed to install Composer dependencies: ' . $result->errorOutput());
            }
        }

        $this->info('  • Composer dependencies installed');
    }

    /**
     * Install NPM dependencies
     */
    private function installNpmDependencies(): void
    {
        $this->info('📦 Installing NPM dependencies...');

        // Check if package.json exists
        if (!File::exists(base_path('package.json'))) {
            $this->warn('  • No package.json found, skipping NPM installation');
            return;
        }

        $result = Process::run('npm install');

        if ($result->failed()) {
            throw new Exception('Failed to install NPM dependencies: ' . $result->errorOutput());
        }

        $this->info('  • NPM dependencies installed');
    }

    /**
     * Create SQLite database file if it doesn't exist
     */
    private function createDatabase(): void
    {
        $this->info('🗃️  Setting up database...');

        try {
            $databasePath = database_path('database.sqlite');

            if (!file_exists($databasePath)) {
                // Create database directory if it doesn't exist
                $databaseDir = dirname($databasePath);
                if (!is_dir($databaseDir)) {
                    mkdir($databaseDir, 0755, true);
                }

                // Create empty SQLite database file
                touch($databasePath);
                chmod($databasePath, 0664);

                $this->info('  • SQLite database created at: ' . $databasePath);
            } else {
                $this->info('  • SQLite database already exists');
            }
        } catch (Exception $e) {
            throw new Exception('Failed to create database: ' . $e->getMessage());
        }
    }

    /**
     * Run database migrations
     */
    private function runMigrations(): void
    {
        $this->info('🗄️  Running database migrations...');

        try {
            Artisan::call('migrate', ['--force' => true]);
            $this->info('  • Database migrations completed');
        } catch (Exception $e) {
            throw new Exception('Failed to run migrations: ' . $e->getMessage());
        }
    }

    /**
     * Create the base user
     */
    private function createBaseUser(): void
    {
        $this->info('👤 Creating base user...');

        $name = $this->option('user-name') ?: 'Admin';
        $email = $this->option('user-email') ?: 'admin@example.com';
        $password = $this->option('user-password') ?: 'password';

        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            if (!$this->confirm("⚠️  User with email '{$email}' already exists. Update password?", false)) {
                $this->info('  • Skipping user creation');
                return;
            }

            $user = User::where('email', $email)->first();
            $user->update([
                'name' => $name,
                'password' => Hash::make($password),
            ]);
            $this->info('  • User updated successfully');
        } else {
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]);
            $this->info('  • Base user created successfully');
        }

        $this->info("    - Name: {$name}");
        $this->info("    - Email: {$email}");
        $this->info("    - Password: {$password}");
    }

    /**
     * Set the application name
     */
    private function setApplicationName(): void
    {
        $this->info('🏷️  Setting application name...');

        $appName = $this->option('name') ?: 'Laravel-Base';
        $envPath = base_path('.env');

        if (!File::exists($envPath)) {
            throw new Exception('.env file not found');
        }

        $envContent = File::get($envPath);
        $envContent = preg_replace('/^APP_NAME=.*/m', "APP_NAME=\"{$appName}\"", $envContent);
        File::put($envPath, $envContent);

        $this->info("  • Application name set to: {$appName}");
    }

    /**
     * Build frontend assets
     */
    private function buildAssets(): void
    {
        $this->info('🎨 Building frontend assets...');

        if (!File::exists(base_path('package.json'))) {
            $this->info('  • No package.json found, skipping asset build');
            return;
        }

        $result = Process::run('npm run build');

        if ($result->failed()) {
            $this->warn('  • Failed to build assets, but installation can continue');
            $this->warn('  • You can manually run: npm run build');
        } else {
            $this->info('  • Frontend assets built successfully');
        }
    }

    /**
     * Display installation summary
     */
    private function displaySummary(): void
    {
        $this->newLine();
        $this->info('📋 Installation Summary:');
        $this->info('────────────────────────────────────────');
        $this->info('✅ Environment file: .env created');
        $this->info('✅ Application key: Generated');

        if (!$this->option('skip-composer')) {
            $this->info('✅ Composer: Dependencies installed');
        }

        if (!$this->option('skip-npm')) {
            $this->info('✅ NPM: Dependencies installed');
            $this->info('✅ Assets: Built for production');
        }

        $this->info('✅ Database: Migrations completed');

        if (!$this->option('skip-user')) {
            $this->info('✅ Base User: Created');
        }

        $this->info('✅ App Name: ' . ($this->option('name') ?: 'Laravel-Base'));

        $this->newLine();
        $this->info('🚀 Next Steps:');
        $this->info('  • Start the development server: php artisan serve');

        if (!$this->option('skip-npm')) {
            $this->info('  • Start Vite dev server: npm run dev');
        }

        if (!$this->option('skip-user')) {
            $this->info('  • Login with: ' . ($this->option('user-email') ?: 'admin@example.com') . ' / ' . ($this->option('user-password') ?: 'password'));
        }

        $this->info('  • Visit: http://localhost:8000');
    }
}
