<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed a platform super admin. Idempotent — safe to re-run.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@newzly.test');
        $password = env('ADMIN_PASSWORD', 'password');

        // forceFill so is_admin (deliberately not mass-assignable) is set.
        $admin = User::firstOrNew(['email' => $email]);
        $admin->forceFill([
            'name' => 'Super Admin',
            'password' => Hash::make($password),
            'is_admin' => true,
            'email_verified_at' => now(),
        ])->save();

        $this->command?->info("Super admin ready: {$admin->email}");
    }
}
