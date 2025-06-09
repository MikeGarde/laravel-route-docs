<?php

use PHPUnit\Framework\TestCase;
use RouteDocs\Support\RouteDocEntry;

class RouteDocEntryTest extends TestCase
{
    public function testRawArrayOutput()
    {
        $entry = new RouteDocEntry(
            class: 'App\\Http\\Controllers\\UserController',
            action: 'index',
            method: 'GET',
            path: '/users/{id}',
            name: 'users.show',
            error: false
        );

        $expected = [
            'error'  => '',
            'method' => 'GET',
            'path'   => '/users/{id}',
            'name'   => 'users.show',
            'class'  => 'App\\Http\\Controllers\\UserController',
            'action' => 'index',
        ];

        $this->assertEquals($expected, $entry->toArray());
    }

    public function testColorArrayOutput()
    {
        $entry = new RouteDocEntry(
            class: 'App\\Http\\Controllers\\UserController',
            action: 'store',
            method: 'POST',
            path: '/users/{id}',
            name: null,
            error: true
        );
        $entry->setOutputColor(true);

        $expected = [
            'error'  => '<fg=red>X</>',
            'method' => '<fg=yellow>POST</>',
            'path'   => '/users/<fg=yellow>{id}</>',
            'name'   => '',
            'class'  => '<fg=gray>App/</fg=gray><fg=gray>Http/</fg=gray><fg=gray>Controllers/</fg=gray>UserController',
            'action' => 'store',
        ];

        $this->assertEquals($expected, $entry->toArray());
    }

    public function testSetOutputColorSwitchesBackToRaw()
    {
        $entry = new RouteDocEntry(
            class: 'App\\Controller',
            action: 'edit',
            method: 'PATCH',
            path: '/edit/{item}',
            name: null,
            error: false
        );
        $entry->setOutputColor(true);
        $this->assertStringContainsString('<fg=cyan>PATCH</>', $entry->toArray()['method']);

        $entry->setOutputColor(false);
        $this->assertEquals('PATCH', $entry->toArray()['method']);
    }

    public function testHighlightPathVariables()
    {
        $entry = new RouteDocEntry('A', 'B', 'GET', '/foo/{bar}/baz/{id}');
        $entry->setOutputColor(true);
        $array = $entry->toArray();
        $this->assertEquals('/foo/<fg=yellow>{bar}</>/baz/<fg=yellow>{id}</>', $array['path']);
    }

    public function testColorHttpMethodDefault()
    {
        $entry = new RouteDocEntry('A', 'B', 'OPTIONS', '/foo');
        $entry->setOutputColor(true);
        $array = $entry->toArray();
        $this->assertEquals('<fg=white>OPTIONS</>', $array['method']);
    }
}
