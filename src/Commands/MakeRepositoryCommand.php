<?php

namespace MmrDev\LaravelStructureMaker\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeRepositoryCommand extends Command
{
    protected $signature = 'make:repository
        {name : Base name of the repository (e.g. User or Admin/User)}';

    protected $description = 'Create a repository and its interface';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle(): int
    {
        $name = $this->argument('name');

        [$classBase, $subPath] = $this->parseName($name);

        $repositoryClass = "{$classBase}";
        $interfaceClass  = "{$repositoryClass}Interface";

        $repositoryPath  = app_path("Repositories/{$subPath}{$repositoryClass}.php");
        $interfacePath   = app_path("Contracts/{$subPath}{$interfaceClass}.php");

        $this->ensureDirectory(dirname($repositoryPath));
        $this->ensureDirectory(dirname($interfacePath));

        if ($this->files->exists($repositoryPath) || $this->files->exists($interfacePath)) {
            $this->components->error('Repository or interface already exists.');
            return self::FAILURE;
        }

        $this->files->put(
            $repositoryPath,
            $this->buildStub('structure-maker-stubs/repository.stub', [
                'namespace' => $this->repositoryNamespace($subPath),
                'class'     => $repositoryClass,
                'interface' => $interfaceClass,
                'contract'  => $this->contractNamespace($subPath),
            ])
        );

        $this->files->put(
            $interfacePath,
            $this->buildStub('repository.interface.stub', [
                'namespace' => $this->contractNamespace($subPath),
                'interface' => $interfaceClass,
            ])
        );

        $this->components->info('Repository created successfully.');

        return self::SUCCESS;
    }

    protected function parseName(string $name): array
    {
        $name = str_replace('\\', '/', $name);
        $parts = explode('/', $name);

        $classBase = Str::studly(array_pop($parts));
        $subPath   = $parts
            ? Str::studly(implode('/', $parts)) . 'MakeRepositoryCommand.php/'
            : '';

        return [$classBase, $subPath];
    }

    protected function repositoryNamespace(string $subPath): string
    {
        return 'App\\Repositories' . $this->namespaceSuffix($subPath);
    }

    protected function contractNamespace(string $subPath): string
    {
        return 'App\\Contracts' . $this->namespaceSuffix($subPath);
    }

    protected function namespaceSuffix(string $subPath): string
    {
        return $subPath
            ? '\\' . str_replace('/', '\\', trim($subPath, '/'))
            : '';
    }

    protected function ensureDirectory(string $path): void
    {
        if (! $this->files->isDirectory($path)) {
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
