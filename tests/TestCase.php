<?php

namespace Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use RouteDocs\RouteDocsServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            RouteDocsServiceProvider::class,
        ];
    }
}
