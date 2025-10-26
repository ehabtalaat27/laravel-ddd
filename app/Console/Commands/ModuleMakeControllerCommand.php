<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleMakeControllerCommand extends Command
{
    protected $signature = 'module:make-controller {module} {name}';
    protected $description = 'Create a Controller inside a module Presentation/Http/Controllers (supports nested folders)';

    public function handle(): void
    {
        $module = ucfirst($this->argument('module'));
        $name = $this->argument('name');

        $pathParts = explode('/', $name);
        $className = ucfirst(array_pop($pathParts));
        $subPath = implode('/', $pathParts);

        $basePath = app_path("{$module}/Presentation/Http/Controllers");
        $fullPath = $subPath ? "{$basePath}/{$subPath}" : $basePath;

        if (!File::exists($fullPath)) {
            File::makeDirectory($fullPath, 0755, true);
            $this->info("📁 Created directory: {$fullPath}");
        }

        $file = "{$fullPath}/{$className}.php";
        if (File::exists($file)) {
            $this->error("❌ Controller already exists: {$file}");
            return;
        }

        $namespace = "App\\{$module}\\Presentation\\Http\\Controllers" . ($subPath ? '\\' . Str::replace('/', '\\', $subPath) : '');

        $stub = <<<PHP
<?php

namespace {$namespace};

use App\Shared\Base\BaseApiController;
use Illuminate\Http\Request;

class {$className} extends BaseApiController
{
    public function index()
    {
        //
    }
}

PHP;

        File::put($file, $stub);
        $this->info("✅ Controller [{$className}] created successfully in module [{$module}]");
    }
}
