<?php

namespace RouteDocs\Console;

use Illuminate\Console\Command;
use RouteDocs\Support\RouteDocInspector;

class ValidateEndpoints extends Command
{
    protected $signature   = 'route:docs:validate {--path= : Path to controller directory}';
    protected $description = 'Validate route attribute usage across controllers.';

    public function __construct(RouteDocInspector $inspector)
    {
        // This allows both CLI flexibility and test mocking
        parent::__construct();
        $this->inspector = $inspector;
    }

    public function handle(): int
    {
        // If --path is provided, re-instantiate inspector with path
        if ($path = $this->option('path')) {
            $this->inspector = new RouteDocInspector($path);
        }

        $routes = $this->inspector->getDocumentedRoutes();
        $errors = $routes->onlyErrors();

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
