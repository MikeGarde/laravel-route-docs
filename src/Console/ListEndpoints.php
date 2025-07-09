<?php

namespace RouteDocs\Console;

use Illuminate\Console\Command;
use RouteDocs\Support\RouteDocInspector;

class ListEndpoints extends Command
{
    protected $signature   = 'route:docs' .
                             ' {--sort=path : Sort by "path", "name", or "controller"}' .
                             ' {--json : Output in JSON format}' .
                             ' {--path= : Path to controller directory}';
    protected $description = 'List all endpoints defined using HTTP method attributes';

    protected RouteDocInspector $inspector;

    public function __construct(RouteDocInspector $inspector)
    {
        parent::__construct();
        $this->inspector = $inspector;
    }

    public function handle(): int
    {
        $sortKey = $this->option('sort') ?? 'path';
        $path    = $this->option('path');

        if ($path) {
            $this->inspector = new RouteDocInspector($path);
        }

        $routes = $this->inspector->getDocumentedRoutes();
        $sorted = $routes->sortByKey($sortKey);

        if ($this->option('json')) {
            $this->line($sorted->toJson());

            return Command::SUCCESS;
        }

        $hasErrors = $sorted->hasErrors();
        $headers   = $hasErrors
            ? ['error', 'method', 'path', 'name', 'class', 'action']
            : ['method', 'path', 'name', 'class', 'action'];

        $this->table($headers, $sorted->toDisplayArray($hasErrors));

        return Command::SUCCESS;
    }
}
