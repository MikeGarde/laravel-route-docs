<?php

namespace RouteDocs\Console;

use Illuminate\Console\Command;
use RouteDocs\Support\RouteDocInspector;

class ValidateEndpoints extends Command
{
    protected $signature   = 'route:docs:validate {--path= : Path to controller directory}';
    protected $description = 'Validate route attribute usage across controllers.';

    public function handle(): int
    {
        $inspector = new RouteDocInspector($this->option('path') ?? null);
        $routes    = $inspector->getDocumentedRoutes();
        $errors    = $routes->onlyErrors();

        if ($errors->isEmpty()) {
            $this->info('✔ All documented routes are correctly registered.');

            return Command::SUCCESS;
        }

        $this->error('✖ Some documented routes are missing from the Laravel route list:');
        $this->table(
            ['method', 'path', 'name', 'class', 'method_name'],
            $errors->toDisplayArray()
        );

        return Command::FAILURE;
    }
}
