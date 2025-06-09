<?php

namespace Feature;

use Examples\Http\Controllers\BookingController;
use Examples\Http\Controllers\ExampleController;
use RouteDocs\Support\RouteDocInspector;
use Tests\TestCase;

class LaravelRouteBadTest extends TestCase
{
    protected function defineRoutes($router): void
    {
        $router->get('/ping', [ExampleController::class, 'ping'])->name('ping.get');
        $router->get('/status', [ExampleController::class, 'status'])->name('status.get');
        $router->post('/status/{element}', [ExampleController::class, 'updateStatus'])->name('status.post');
        $router->get('/', [ExampleController::class, 'legacyHome']);
        $router->get('/home', [ExampleController::class, 'legacyHome'])->name('home.index');
        $router->post('/home', [ExampleController::class, 'legacyHome'])->name('home.post');
    }

    public function testAttributeRouteMissingRegisteredRoute()
    {
        $path      = __DIR__ . '/../../examples';
        $inspector = new RouteDocInspector($path);

        $routes = $inspector->getDocumentedRoutes();
        $array  = $routes->toArray();
        $errors = $routes->hasErrors();

        $this->assertNotEmpty($routes, 'No documented routes found.');
        $this->assertIsArray($array);

        $this->assertTrue($errors, 'Expected errors but found none.');
    }
}
