<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\File;

class CreateTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:test-data 
                            {--users=10 : Number of users to create}
                            {--fresh : Fresh database migration before seeding}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create comprehensive test data for the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🌱 Creating test data for Laravel Base...');

        if ($this->option('fresh')) {
            $this->info('🔄 Running fresh migrations...');
            Process::run('php artisan migrate:fresh');
            $this->info('✅ Database refreshed');
        }

        // Create base seeders and factories
        $this->createUserFactory();
        $this->createUserSeeder();
        $this->createRolesAndPermissionsSeeder();
        $this->updateDatabaseSeeder();

        // Run the seeders
        $this->runSeeders();

        $this->info('✅ Test data creation completed!');
        $this->newLine();
        $this->info('📊 Generated data:');
        $this->info("  • {$this->option('users')} users with various roles");
        $this->info('  • Basic roles and permissions structure');
        $this->info('  • Sample application settings');
    }

    private function createUserFactory(): void
    {
        $this->info('🏭 Creating User factory...');

        $factory = <<<'PHP'
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user should be an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
    }

    /**
     * Indicate that the user should be a manager.
     */
    public function manager(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->name() . ' (Manager)',
        ]);
    }
}
PHP;

        File::ensureDirectoryExists(database_path('factories'));
        File::put(database_path('factories/UserFactory.php'), $factory);
        $this->info('  • User factory created');
    }

    private function createUserSeeder(): void
    {
        $this->info('🌱 Creating User seeder...');

        $seeder = <<<PHP
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        \$admin = User::factory()->admin()->create();
        
        // Assign admin role if it exists
        if (Role::where('name', 'admin')->exists()) {
            \$admin->assignRole('admin');
        }

        // Create manager users
        \$managers = User::factory(3)->manager()->create();
        
        // Assign manager role if it exists
        if (Role::where('name', 'manager')->exists()) {
            \$managers->each(fn (\$user) => \$user->assignRole('manager'));
        }

        // Create regular users
        \$users = User::factory({$this->option('users')})->create();
        
        // Assign user role if it exists
        if (Role::where('name', 'user')->exists()) {
            \$users->each(fn (\$user) => \$user->assignRole('user'));
        }

        \$this->command->info('👥 Users created successfully');
        \$this->command->info('   • 1 admin user (admin@example.com)');
        \$this->command->info('   • 3 manager users');
        \$this->command->info('   • {$this->option('users')} regular users');
        \$this->command->info('   • Default password: password');
    }
}
PHP;

        File::put(database_path('seeders/UserSeeder.php'), $seeder);
        $this->info('  • User seeder created');
    }

    private function createRolesAndPermissionsSeeder(): void
    {
        $this->info('🔐 Creating Roles and Permissions seeder...');

        $seeder = <<<'PHP'
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            
            // Content management
            'content.view',
            'content.create',
            'content.edit',
            'content.delete',
            'content.publish',
            
            // Settings
            'settings.view',
            'settings.edit',
            
            // System
            'system.maintenance',
            'system.logs',
            'system.backup',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Admin role - all permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());
        
        // Manager role - content and user management
        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->givePermissionTo([
            'users.view',
            'users.create',
            'users.edit',
            'content.view',
            'content.create',
            'content.edit',
            'content.delete',
            'content.publish',
        ]);
        
        // Editor role - content management only
        $editorRole = Role::create(['name' => 'editor']);
        $editorRole->givePermissionTo([
            'content.view',
            'content.create',
            'content.edit',
            'content.publish',
        ]);
        
        // User role - basic permissions
        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo([
            'content.view',
        ]);

        $this->command->info('🔐 Roles and permissions created:');
        $this->command->info('   • admin (all permissions)');
        $this->command->info('   • manager (users + content)');
        $this->command->info('   • editor (content only)');
        $this->command->info('   • user (view only)');
    }
}
PHP;

        File::put(database_path('seeders/RolesAndPermissionsSeeder.php'), $seeder);
        $this->info('  • Roles and Permissions seeder created');
    }

    private function updateDatabaseSeeder(): void
    {
        $this->info('📋 Updating DatabaseSeeder...');

        $seeder = <<<'PHP'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
        ]);

        $this->command->info('🌱 Database seeding completed!');
    }
}
PHP;

        File::put(database_path('seeders/DatabaseSeeder.php'), $seeder);
        $this->info('  • DatabaseSeeder updated');
    }

    private function runSeeders(): void
    {
        $this->info('🚀 Running seeders...');
        
        $result = Process::run('php artisan db:seed');
        
        if ($result->failed()) {
            $this->error('❌ Failed to run seeders');
            $this->error($result->errorOutput());
            return;
        }

        $this->info($result->output());
    }
}