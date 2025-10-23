<?php

namespace App\User\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class UserServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Load routes for this module
        $this->loadRoutesFrom(__DIR__ . '/../../Presentation/Http/routes/v1/api.php');

        // Load migrations for this module
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        // Register middleware
        $router = $this->app['router'];
        $router->aliasMiddleware('user.active', \App\User\Presentation\Http\Middleware\EnsureUserIsActive::class);
        // Load translations or views if needed
        // $this->loadViewsFrom(__DIR__.'/../../Presentation/Views', 'user');
    }
}
