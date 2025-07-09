<?php

namespace Tests\Feature;

use Examples\Http\Controllers\BookingController;
use Examples\Http\Controllers\ExampleController;
use Examples\Http\Controllers\PostApiController;
use Examples\Http\Controllers\PostController;
use RouteDocs\Support\RouteDocInspector;
use Tests\TestCase;
use function Symfony\Component\String\s;

class LaravelRouteGoodTest extends TestCase
{
    protected function defineRoutes($router): void
    {
        $router->get('/ping', [ExampleController::class, 'ping'])->name('ping.get');
        $router->get('/status', [ExampleController::class, 'status'])->name('status.get');
        $router->post('/status/{element}', [ExampleController::class, 'updateStatus'])->name('status.post');
        $router->get('/', [ExampleController::class, 'legacyHome']);
        $router->get('/home', [ExampleController::class, 'legacyHome'])->name('home.index');
        $router->post('/home', [ExampleController::class, 'legacyHome'])->name('home.post');

        $router->get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        $router->post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
        $router->get('/bookings/{id}', [BookingController::class, 'show'])->name('bookings.show');
        $router->delete('/bookings/{id}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
        $router->get('/bookings/stats/{frequency}', [BookingController::class, 'stats'])->name('bookings.stats');
        $router->resource('/posts', PostController::class);
        $router->apiResource('/api/posts', PostApiController::class);
    }

    public function testAttributeRouteMatchesRegisteredRoute()
    {
        $path      = __DIR__ . '/../../examples';
        $inspector = new RouteDocInspector($path);

        $routes = $inspector->getDocumentedRoutes();
        $array  = $routes->toArray();
        $errors = $routes->hasErrors();

        $this->assertNotEmpty($routes, 'No documented routes found.');
        $this->assertIsArray($array);

        // We don't want this and will give us a passing test next, but let's help the user out
        if ($errors) {
            foreach ($routes as $route) {
                if ($route->hasError()) {
                    $msg     = 'Error found in route: %s %s - %s';
                    $method  = $route->method;
                    $path    = $route->path;
                    $context = implode(', ', $route->getErrors());
                    $this->fail(sprintf($msg, $method, $path, $context));
                }
            }
        }

        $this->assertFalse($errors, 'Expected no errors but found some.');
    }
}
