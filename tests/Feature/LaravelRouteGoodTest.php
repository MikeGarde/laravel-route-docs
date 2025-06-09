<?php

namespace Tests\Feature;

use Examples\Http\Controllers\BookingController;
use Examples\Http\Controllers\ExampleController;
use RouteDocs\Support\RouteDocInspector;
use Tests\TestCase;

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
        $router->post('/bookings/{id}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
        $router->get('/bookings/stats/daily', [BookingController::class, 'dailyStats'])->name('bookings.stats.daily');
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

        $this->assertFalse($errors, 'Expected no errors but found some.');
    }
}
