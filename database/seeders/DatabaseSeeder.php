<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Find all module DatabaseSeeders automatically
        $modulesPath = app_path();
        $seeders = $this->findModuleSeeders($modulesPath);

        foreach ($seeders as $seederClass) {
            $this->call($seederClass);
        }
    }

    protected function findModuleSeeders(string $path): array
    {
        $seeders = [];

        // Scan all folders under app/
        foreach (File::directories($path) as $modulePath) {
            $seederFile = $modulePath . '/Infrastructure/Database/Seeders/DatabaseSeeder.php';

            if (File::exists($seederFile)) {
                // Convert path to class name
                $moduleName = basename($modulePath);
                $seederClass = "App\\{$moduleName}\\Infrastructure\\Database\\Seeders\\DatabaseSeeder";
                $seeders[] = $seederClass;
            }
        }

        return $seeders;
    }
}
