<?php

namespace RouteDocs\Console;

use Illuminate\Console\Command;
use RouteDocs\Support\RouteDocInspector;

class ListEndpoints extends Command
{
    protected $signature   = 'route:docs {--sort=path : Sort by "path", "name", or "controller"}';
    protected $description = 'List all endpoints defined using HTTP method attributes';

    public function handle(): int
    {
        $inspector = new RouteDocInspector();
        $routes    = $inspector->getDocumentedRoutes();

        $sortKey = $this->option('sort') ?? 'path';
        $sorted  = $routes->sortByKey($sortKey);

        $hasErrors = $sorted->hasErrors();
        $headers   = $hasErrors
            ? ['error', 'method', 'path', 'name', 'class', 'action']
            : ['method', 'path', 'name', 'class', 'action'];

        $this->table($headers, $sorted->toDisplayArray($hasErrors));

        return Command::SUCCESS;
    }
}
