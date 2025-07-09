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
        $this->assertStringContainsString('error', $output);
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

    public function testListOutputJsonFormat()
    {
        $class  = 'A';
        $action = 'foo';
        $method = 'GET';
        $path   = '/a';
        $name   = 'a';
        $error  = false;

        $entry      = new RouteDocEntry($class, $action, $method, $path, $name, $error);
        $collection = new RouteDocCollection([$entry]);
        $this->mockInspectorWithRoutes($collection);

        $result = Artisan::call('route:docs', ['--json' => true]);
        $output = Artisan::output();

        $this->assertEquals(0, $result);
        $this->assertJson($output);

        $data = json_decode($output, true);
        $this->assertIsArray($data);
        $this->assertCount(1, $data);

        $entry = $data[0];

        $this->assertArrayHasKey('class', $entry);
        $this->assertArrayHasKey('action', $entry);
        $this->assertArrayHasKey('method', $entry);
        $this->assertArrayHasKey('path', $entry);
        $this->assertArrayHasKey('name', $entry);
        $this->assertArrayHasKey('error', $entry);

        $this->assertEquals($class, $entry['class']);
        $this->assertEquals($action, $entry['action']);
        $this->assertEquals($method, $entry['method']);
        $this->assertEquals($path, $entry['path']);
        $this->assertEquals($name, $entry['name']);
        $this->assertEquals($error, $entry['error']);
    }
}
