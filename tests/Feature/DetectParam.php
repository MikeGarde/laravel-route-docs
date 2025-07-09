<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use RouteDocs\Attributes\queryParam;
use RouteDocs\Support\RouteDocInspector;
use Tests\TestCase;

class QueryParamController
{
    #[queryParam('status', cast: 'bool', required: false, description: 'Filter by status')]
    public function index() {}
}

class DetectParamTest extends TestCase
{
    public function test_inspect_detects_query_param()
    {
        // Register route dynamically
        Route::get('/query-param-test', [QueryParamController::class, 'index']);

        $route = Route::getRoutes()->getByName(null); // Assuming it's the only one
        $this->assertNotNull($route);

        $entry = RouteDocInspector::getDocumentedRoutes($route);
        $this->assertNotNull($entry);

        $this->assertEquals(QueryParamController::class, $entry->controller);
        $this->assertEquals('index', $entry->method);
        $this->assertEquals('/query-param-test', $entry->uri);

        $this->assertIsArray($entry->queryParams);
        $this->assertCount(1, $entry->queryParams);

        $param = $entry->queryParams[0];
        $this->assertEquals('status', $param['key']);
        $this->assertEquals('bool', $param['cast']);
        $this->assertTrue($param['required']);
        $this->assertEquals('Filter by status', $param['description']);
    }
}
