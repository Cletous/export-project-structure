<?php

namespace Makuruwan\ExportProjectStructure;

use Illuminate\Support\ServiceProvider;
use Makuruwan\ExportProjectStructure\Commands\ExportProjectStructureCommand;

class ExportProjectStructureServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/export-project-structure.php',
            'export-project-structure'
        );
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/export-project-structure.php' => config_path('export-project-structure.php'),
        ], 'export-project-structure-config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ExportProjectStructureCommand::class,
            ]);
        }
    }
}