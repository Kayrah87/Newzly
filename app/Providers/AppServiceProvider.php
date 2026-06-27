<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Platform super admins bypass all policy checks. Returning null
        // (not false) for everyone else lets the normal policies decide.
        Gate::before(function (User $user) {
            return $user->isAdmin() ? true : null;
        });
    }
}
