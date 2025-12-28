<?php

namespace MmrDev\LaravelStructureMaker;

use Illuminate\Support\ServiceProvider;
use MmrDev\LaravelStructureMaker\Commands\MakeRepositoryCommand;
use MmrDev\LaravelStructureMaker\Commands\MakeServiceCommand;

class LaravelStructureMakerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/lsm.php' => config_path('lsm.php'),
        ], 'structure-maker-config');
        $this->publishes([
            __DIR__ . '/../stubs' => base_path('stubs/structure-maker-stubs'),
        ], 'structure-maker-stubs');

    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/lsm.php', 'lsm'
        );
        // register command
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeServiceCommand::class,
                MakeRepositoryCommand::class
            ]);
        }
    }
}
