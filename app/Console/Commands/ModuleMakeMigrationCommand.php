<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class ModuleMakeMigrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'module:make-migration 
                            {module : The name of the module (e.g. User)} 
                            {name : The name of the migration (e.g. create_users_table)}';

    /**
     * The console command description.
     */
    protected $description = 'Create a migration file inside a specific module (DDD structure)';

    /**
     * Execute the console command.
     */
  public function handle(): void
{
    $module = ucfirst($this->argument('module'));
    $name = $this->argument('name');

    $migrationPath = app_path("{$module}/Infrastructure/Database/Migrations");

    // Ensure directory exists
    if (!File::exists($migrationPath)) {
        File::makeDirectory($migrationPath, 0755, true);
        $this->info("📁 Created directory: {$migrationPath}");
    }

    // ✅ Fix: make path relative to base_path()
    $relativePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $migrationPath);

    // Run the Artisan migration command
    Artisan::call('make:migration', [
        'name' => $name,
        '--path' => $relativePath,
    ]);

    $this->info("✅ Migration created for module [{$module}] at:");
    $this->line("   {$migrationPath}");
    $this->newLine();
    $this->line(Artisan::output());
}

}
