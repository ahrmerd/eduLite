<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
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
        Model::unguard();

        Gate::define('role', function (User $user, $role) {
            return $user->hasRole($role);
            // return $user->roles->pluck('name')->contains($role);
        });

        // Define a gate for teams
        
        Gate::define('anyRole', function (User $user, $roles) {
            return $user->roles->pluck('name')->intersect($roles)->isNotEmpty();
        });
    }
}
