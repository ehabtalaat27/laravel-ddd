<?php

namespace App\User\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $domainPath = app_path('User/Domain/Repositories');

        // If there's no Domain/Repositories folder — nothing to do
        if (!is_dir($domainPath)) {
            Log::warning('RepositoryServiceProvider: domain repositories folder not found: ' . $domainPath);
            return;
        }

        // Iterate all PHP files in Domain/Repositories (recursive)
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($domainPath));

        foreach ($iterator as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            // short file name, e.g. "UserRepositoryInterface"
            $short = $file->getBasename('.php');

            // expected interface FQCN
            $interface = "App\\User\\Domain\\Repositories\\{$short}";

            // only bind if the interface actually exists
            if (! interface_exists($interface)) {
                continue;
            }

            // expected implementation short name, e.g. "UserRepository"
            $implShort = str_replace('Interface', '', $short);

            // implementation FQCN - adjust this if your implementations live in a sub-namespace (e.g. Eloquent)
            $implementation = "App\\User\\Infrastructure\\Repositories\\{$implShort}";

            if (class_exists($implementation)) {
                $this->app->bind($interface, $implementation);
                Log::info("RepositoryServiceProvider bound: {$interface} -> {$implementation}");
            } else {
                Log::warning("RepositoryServiceProvider missing implementation for: {$interface}. Expected: {$implementation}");
            }
        }
    }
}
