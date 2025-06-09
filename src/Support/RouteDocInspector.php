<?php

namespace RouteDocs\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use RouteDocs\Attributes\HttpMethod;

class RouteDocInspector
{
    protected string $controllerPath;
    protected bool   $requireNameMatch;

    public function __construct(?string $controllerPath = null, bool $requireNameMatch = false)
    {
        $this->controllerPath   = $controllerPath ?? base_path('app/Http/Controllers');
        $this->requireNameMatch = $requireNameMatch;
    }

    protected function controllerNamespace(): string
    {
        $basePath     = base_path() . '/';
        $relativePath = Str::after($this->controllerPath, $basePath);
        $trimmedPath  = trim($relativePath, '/');
        $namespace    = str_replace('/', '\\', $trimmedPath);
        $default      = 'App\\Http\\Controllers';

        return $namespace ?: $default;
    }

    public function getDocumentedRoutes(): RouteDocCollection
    {
        $routes = collect();
        $seen   = [];

        foreach ($this->getPhpFiles($this->controllerPath) as $file) {
            require_once $file;

            foreach (get_declared_classes() as $class) {
                $namespace = $this->controllerNamespace();
                if (!Str::startsWith(Str::lower($class), Str::lower($namespace . '\\'))) {
                    continue;
                }
                if (in_array($class, $seen, true)) {
                    continue;
                }
                $seen[] = $class;

                $ref = new ReflectionClass($class);

                foreach ($ref->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                    foreach ($method->getAttributes() as $attr) {
                        $instance = $attr->newInstance();

                        if (!is_subclass_of($instance, HttpMethod::class)) {
                            continue;
                        }

                        $shortClass = Str::startsWith(Str::lower($class), Str::lower($namespace . '\\'))
                            ? substr($class, strlen($namespace) + 1)
                            : $class;

                        $error = !$this->routeExists(
                            name  : $instance->name,
                            path  : $instance->path,
                            method: $instance::method(),
                            class : $class,
                            action: $method->getName()
                        );

                        $entry = new RouteDocEntry(
                            class : $shortClass,
                            action: $method->getName(),
                            method: $instance::method(),
                            path  : $instance->path,
                            name  : $instance->name,
                            error : $error
                        );

                        $routes->push($entry);
                    }
                }
            }
        }

        return new RouteDocCollection($routes);
    }

    protected function routeExists(?string $name, string $path, string $method, string $class,
                                   string  $action): bool
    {
        $action = strtoupper($action);
        $path   = $path === '/' ? '/' : ltrim($path, '/');

        foreach (Route::getRoutes() as $route) {
            if (
                $route->uri() === $path &&
                in_array(strtoupper($action), $route->methods(), true)
            ) {
                $action = $route->getAction('controller');

                if (!str_contains($action, '@')) {
                    continue;
                }

                [$actualController, $actualMethod] = explode('@', $action);

                if (
                    ltrim($actualController, '\\') === ltrim($class, '\\') &&
                    $actualMethod === $action
                ) {
                    if ($this->requireNameMatch || $name !== null) {
                        return $route->getName() === $name;
                    }

                    return true;
                }
            }
        }

        return false;
    }

    protected function getPhpFiles(string $dir): array
    {
        return File::allFiles($dir);
    }
}
