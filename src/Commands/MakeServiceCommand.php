<?php

namespace MmrDev\LaravelStructureMaker\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeServiceCommand extends Command
{
    protected $signature = 'make:service
        {name : Service class name (e.g. User or Admin/User)}';

    protected $description = 'Create a service class';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle(): int
    {
        [$className, $subPath] = $this->parseName($this->argument('name'));

        $serviceClass = "{$className}";
        $path = app_path("Services/{$subPath}{$serviceClass}.php");

        if ($this->files->exists($path)) {
            $this->components->error('Service already exists.');
            return self::FAILURE;
        }

        $this->ensureDirectory(dirname($path));

        $this->files->put(
            $path,
            $this->buildStub('structure-maker-stubs/service.stub', [
                'namespace' => $this->namespace($subPath),
                'class' => $serviceClass,
            ])
        );

        $this->components->info("Service {$serviceClass} created successfully.");

        return self::SUCCESS;
    }

    protected function parseName(string $name): array
    {
        $name = str_replace('\\', '/', $name);
        $parts = explode('/', $name);

        $className = Str::studly(array_pop($parts));
        $subPath = $parts
            ? implode('/', array_map([Str::class, 'studly'], $parts)) . '/'
            : '';

        return [$className, $subPath];
    }

    protected function namespace(string $subPath): string
    {
        return 'App\\Services' . (
            $subPath
                ? '\\' . str_replace('/', '\\', trim($subPath, '/'))
                : ''
            );
    }

    protected function ensureDirectory(string $path): void
    {
        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0755, true);
        }
    }

    protected function buildStub(string $stub, array $replacements): string
    {
        $content = $this->files->get(base_path("stubs/{$stub}"));

        foreach ($replacements as $key => $value) {
            $content = str_replace("{{ {$key} }}", $value, $content);
        }

        return $content;
    }
}
