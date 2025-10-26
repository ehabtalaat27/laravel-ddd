<?php

namespace App\User\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class UserServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // ✅ Register module API routes
        $this->mapApiRoutes();

        // ✅ Load module-specific migrations
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        // ✅ Register module middleware
        $router = $this->app['router'];
        $router->aliasMiddleware(
            'user.active',
            \App\User\Presentation\Http\Middleware\EnsureUserIsActive::class
        );

        // Optionally load views/translations if your module has them
        // $this->loadViewsFrom(__DIR__.'/../../Presentation/Views', 'user');
    }

    protected function mapApiRoutes(): void
    {
        Route::prefix('api/v1')
            ->middleware(['api'])
            ->group(function () {
                require __DIR__ . '/../../Presentation/Http/routes/v1/api.php';
            });
    }
}
