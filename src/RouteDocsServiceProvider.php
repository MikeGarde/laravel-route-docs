<?php

namespace RouteDocs;

use Illuminate\Support\ServiceProvider;
use RouteDocs\Console\ListEndpoints;
use RouteDocs\Console\ValidateEndpoints;

class RouteDocsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ListEndpoints::class,
                ValidateEndpoints::class,
            ]);
        }
    }
}
