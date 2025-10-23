<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        foreach (glob(app_path('*/Infrastructure/Providers/*ServiceProvider.php')) as $provider) {
           $class = (string) Str::of($provider)
    ->replace(app_path(), 'App')
    ->replace('/', '\\')
    ->replace('.php', '');

            $this->app->register($class);
            logger()->info("Registered provider: {$class}");
        }
    }
}
