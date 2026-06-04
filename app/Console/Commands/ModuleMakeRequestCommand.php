<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleMakeRequestCommand extends Command
{
    protected $signature = 'module:make-request {module} {name}';
    protected $description = 'Create a Form Request inside a module Presentation/Http/Requests (supports nested folders)';

    public function handle(): void
    {
        $module = ucfirst($this->argument('module'));
        $name = $this->argument('name');

        // Handle nested folder structure like Api/V1/UserRequest
        $pathParts = explode('/', $name);
        $className = ucfirst(array_pop($pathParts));
        $subPath = implode('/', $pathParts);

        $basePath = app_path("{$module}/Presentation/Http/Requests");
        $fullPath = $subPath ? "{$basePath}/{$subPath}" : $basePath;

        // Create nested directory if it doesn’t exist
        if (!File::exists($fullPath)) {
            File::makeDirectory($fullPath, 0755, true);
            $this->info("📁 Created directory: {$fullPath}");
        }

        $file = "{$fullPath}/{$className}.php";
        if (File::exists($file)) {
            $this->error("❌ Request already exists: {$file}");
            return;
        }

        // Convert folder path to namespace (App\User\Presentation\Http\Requests\Api\V1)
        $namespace = "App\\{$module}\\Presentation\\Http\\Requests" . ($subPath ? '\\' . Str::replace('/', '\\', $subPath) : '');

        $stub = <<<PHP
<?php

namespace {$namespace};

use Illuminate\\Foundation\\Http\\FormRequest;

class {$className} extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            //
        ];
    }
}

PHP;

        File::put($file, $stub);
        $this->info("✅ Request [{$className}] created successfully in module [{$module}]");
    }
}
