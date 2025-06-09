<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use RouteDocs\Support\RouteDocCollection;
use RouteDocs\Support\RouteDocEntry;
use RouteDocs\Support\RouteDocInspector;
use Tests\TestCase;

class CommandsTest extends TestCase
{
    protected function mockInspectorWithRoutes($routes): void
    {
        $mock = $this->createMock(RouteDocInspector::class);
        $mock->method('getDocumentedRoutes')->willReturn($routes);
        $this->app->instance(RouteDocInspector::class, $mock);
    }

    public function testListEndpointsSuccess()
    {
        $entry      = new RouteDocEntry('A', 'foo', 'GET', '/a', 'a', false);
        $collection = new RouteDocCollection([$entry]);
        $this->mockInspectorWithRoutes($collection);

        $result = Artisan::call('route:docs');
        $output = Artisan::output();

        $this->assertEquals(0, $result);
        $this->assertStringNotContainsString('error', $output);
        $this->assertStringContainsString('method', $output);
        $this->assertStringContainsString('path', $output);
        $this->assertStringContainsString('name', $output);
        $this->assertStringContainsString('class', $output);
        $this->assertStringContainsString('action', $output);
    }

    public function testListEndpointsWithErrors()
    {
        $entry      = new RouteDocEntry('A', 'foo', 'GET', '/a', 'a', true);
        $collection = new RouteDocCollection([$entry]);
        $this->mockInspectorWithRoutes($collection);

        $result = Artisan::call('route:docs');
        $output = Artisan::output();

        $this->assertEquals(0, $result);
        $this->assertStringNotContainsString('error', $output);
        $this->assertStringContainsString('method', $output);
        $this->assertStringContainsString('path', $output);
        $this->assertStringContainsString('name', $output);
        $this->assertStringContainsString('class', $output);
        $this->assertStringContainsString('action', $output);
    }

    public function testValidateEndpointsWithErrors()
    {
        $entry      = new RouteDocEntry('A', 'foo', 'GET', '/a', 'a', true);
        $collection = new RouteDocCollection([$entry]);
        $this->mockInspectorWithRoutes($collection);

        $result = Artisan::call('route:docs:validate');
        $output = Artisan::output();

        $this->assertEquals(1, $result); // Expect failure code
        $this->assertStringContainsString('missing', $output);
        $this->assertStringContainsString('GET', $output);
        $this->assertStringContainsString('/a', $output);
    }

    public function testValidateEndpointsSuccess()
    {
        $entry      = new RouteDocEntry('A', 'foo', 'GET', '/a', 'a', false);
        $collection = new RouteDocCollection([$entry]);
        $this->mockInspectorWithRoutes($collection);

        $result = Artisan::call('route:docs:validate');
        $output = Artisan::output();

        $this->assertEquals(0, $result);
        $this->assertStringContainsString('All documented routes are correctly registered', $output);
    }
}
