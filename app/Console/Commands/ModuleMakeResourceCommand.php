<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleMakeResourceCommand extends Command
{
    protected $signature = 'module:make-resource {module} {name}';
    protected $description = 'Create an API Resource inside a module Presentation/Http/Resources (supports nested folders)';

    public function handle(): void
    {
        $module = ucfirst($this->argument('module'));
        $name = $this->argument('name');

        $pathParts = explode('/', $name);
        $className = ucfirst(array_pop($pathParts));
        $subPath = implode('/', $pathParts);

        $basePath = app_path("{$module}/Presentation/Http/Resources");
        $fullPath = $subPath ? "{$basePath}/{$subPath}" : $basePath;

        if (!File::exists($fullPath)) {
            File::makeDirectory($fullPath, 0755, true);
            $this->info("📁 Created directory: {$fullPath}");
        }

        $file = "{$fullPath}/{$className}.php";
        if (File::exists($file)) {
            $this->error("❌ Resource already exists: {$file}");
            return;
        }

        $namespace = "App\\{$module}\\Presentation\\Http\\Resources" . ($subPath ? '\\' . Str::replace('/', '\\', $subPath) : '');

        $stub = <<<PHP
<?php

namespace {$namespace};

use Illuminate\\Http\\Resources\\Json\\JsonResource;

class {$className} extends JsonResource
{
    public function toArray(\$request): array
    {
        return [
            //
        ];
    }
}

PHP;

        File::put($file, $stub);
        $this->info("✅ Resource [{$className}] created successfully in module [{$module}]");
    }
}
