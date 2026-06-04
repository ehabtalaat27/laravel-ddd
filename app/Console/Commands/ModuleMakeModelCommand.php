<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class ModuleMakeModelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Example:
     * php artisan module:make-model User UserInfo
     */
    protected $signature = 'module:make-model {module} {name}';

    /**
     * The console command description.
     */
    protected $description = 'Create a model inside a specific module following DDD structure';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $module = ucfirst($this->argument('module'));
        $name = ucfirst($this->argument('name'));

        $modelPath = app_path("{$module}/Infrastructure/Models");

        // Create directory if missing
        if (!File::exists($modelPath)) {
            File::makeDirectory($modelPath, 0755, true);
            $this->info("📁 Created directory: {$modelPath}");
        }

        // ✅ Build full path for model file
        $modelFile = "{$modelPath}/{$name}.php";

        // Check if exists
        if (File::exists($modelFile)) {
            $this->error("❌ Model already exists: {$modelFile}");
            return;
        }

        // ✅ Generate the model file manually (you could call artisan make:model too)
        $namespace = "App\\{$module}\\Infrastructure\\Models";

        $stub = <<<PHP
<?php

namespace {$namespace};

use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;
use Illuminate\\Database\\Eloquent\\Model;

class {$name} extends Model
{
    use HasFactory;

    protected \$guarded = [];
}

PHP;

        File::put($modelFile, $stub);

        // ✅ Create factory directory
        $factoryPath = app_path("{$module}/Infrastructure/Database/Factories");
        if (!File::exists($factoryPath)) {
            File::makeDirectory($factoryPath, 0755, true);
        }

        $this->info("✅ Model [{$name}] created successfully in module [{$module}]");
        $this->line("📄 {$modelFile}");
    }
}
