<?php

use PHPUnit\Framework\TestCase;
use RouteDocs\Support\RouteDocEntry;
use RouteDocs\Support\RouteDocCollection;

class RouteDocCollectionTest extends TestCase
{
    protected function makeEntry($class, $action, $method, $path, $name = null, $error = false)
    {
        return new RouteDocEntry($class, $action, $method, $path, $name, $error);
    }

    public function testSortByKey()
    {
        $a = $this->makeEntry('A', 'foo', 'GET', '/a', 'a');
        $b = $this->makeEntry('B', 'bar', 'POST', '/b', 'b');
        $c = $this->makeEntry('C', 'baz', 'PUT', '/c', 'c');
        $collection = new RouteDocCollection([$b, $c, $a]);

        $sorted = $collection->sortByKey('class');
        $this->assertEquals(['A', 'B', 'C'], $sorted->pluck('class')->all());
    }

    public function testHasErrors()
    {
        $a = $this->makeEntry('A', 'foo', 'GET', '/a', 'a', false);
        $b = $this->makeEntry('B', 'bar', 'POST', '/b', 'b', true);
        $collection = new RouteDocCollection([$a, $b]);

        $this->assertTrue($collection->hasErrors());
        $this->assertFalse((new RouteDocCollection([$a]))->hasErrors());
    }

    public function testOnlyErrors()
    {
        $a = $this->makeEntry('A', 'foo', 'GET', '/a', 'a', false);
        $b = $this->makeEntry('B', 'bar', 'POST', '/b', 'b', true);
        $collection = new RouteDocCollection([$a, $b]);

        $errors = $collection->onlyErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals('B', $errors->first()->class);
    }

    public function testToDisplayArrayWithAndWithoutErrorAndColor()
    {
        $a = $this->makeEntry('A', 'foo', 'GET', '/a', 'a', false);
        $b = $this->makeEntry('B', 'bar', 'POST', '/b', 'b', true);
        $collection = new RouteDocCollection([$a, $b]);

        // With error column and color
        $withErrorColor = $collection->toDisplayArray(true, true);
        $this->assertEquals('<fg=red>X</>', $withErrorColor[1]['error']);
        $this->assertStringContainsString('<fg=blue>GET</>', $withErrorColor[0]['method']);

        // Without error column, with color
        $noErrorColor = $collection->toDisplayArray(false, true);
        $this->assertArrayNotHasKey('error', $noErrorColor[0]);
        $this->assertStringContainsString('<fg=yellow>POST</>', $noErrorColor[1]['method']);

        // Without error column, no color
        $noErrorNoColor = $collection->toDisplayArray(false, false);
        $this->assertEquals('GET', $noErrorNoColor[0]['method']);
        $this->assertArrayNotHasKey('error', $noErrorNoColor[0]);
    }
}
