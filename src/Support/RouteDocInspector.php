<?php

namespace RouteDocs\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use RouteDocs\Attributes\formParam;
use RouteDocs\Attributes\HttpMethod;
use RouteDocs\Attributes\param;
use RouteDocs\Attributes\queryParam;

class RouteDocInspector
{
    protected string $controllerPath;
    protected bool   $requireNameMatch;

    public function __construct(?string $controllerPath = null, bool $requireNameMatch = false)
    {
        $this->controllerPath   = $controllerPath ?? base_path('app/Http/Controllers');
        $this->requireNameMatch = $requireNameMatch;
    }

    public function getDocumentedRoutes(): RouteDocCollection
    {
        $routes    = collect();
        $namespace = $this->controllerNamespace();

        foreach ($this->getPhpFiles($this->controllerPath) as $file) {
            $class = $this->getClassFromFile($file);
            if (!$class || !class_exists($class)) {
                continue;
            }
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

                    $validRoute = $this->routeExists(
                        name  : $instance->name,
                        path  : $instance->path,
                        method: $instance::method(),
                        class : $class,
                        action: $method->getName()
                    );

                    $rules     = [
                        'path'       => 'required|string|regex:/^\//',
                        'method'     => 'required|string|in:GET,POST,PUT,PATCH,DELETE,HEAD,OPTIONS',
                        'name'       => 'nullable|string|regex:/^[a-zA-Z0-9_.-]+$/',
                        'validRoute' => 'accepted', // aka true
                    ];
                    $messages  = [
                        'validRoute.accepted' => 'The route is invalid.',
                    ];
                    $validator = Validator::make(
                        [
                            'path'       => $instance->path,
                            'method'     => $instance::method(),
                            'name'       => $instance->name,
                            'validRoute' => $validRoute,
                        ],
                        $rules,
                        $messages
                    );

                    $entry = new RouteDocEntry(
                        class : $shortClass,
                        action: $method->getName(),
                        method: $instance::method(),
                        path  : $instance->path,
                        name  : $instance->name,
                    );

                    if ($validator->fails()) {
                        $entry->setError($validator->errors()->all());
                    }

                    $entry->params = $this->gatherParams($method);

                    $routes->push($entry);
                }
            }

        }

        return new RouteDocCollection($routes);
    }

    protected function routeExists(?string $name, string $path, string $method, string $class,
                                   string  $action): bool
    {
        $method = strtoupper($method);
        $path   = $path === '/' ? '/' : ltrim($path, '/');

        foreach (Route::getRoutes() as $route) {
            if (
                $route->uri() === $path &&
                in_array(strtoupper($method), $route->methods(), true)
            ) {
                $laravelAction = $route->getAction('controller');

                if (!str_contains($laravelAction, '@')) {
                    continue;
                }

                [$actualController, $actualMethod] = explode('@', $laravelAction);

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

    protected static function gatherParams(ReflectionMethod $method): array
    {
        $paramGroups = [
            'path'  => [],
            'query' => [],
            'form'  => [],
        ];

        foreach ($method->getAttributes() as $attr) {
            $instance = $attr->newInstance();

            if (!$instance instanceof Param) {
                continue;
            }

            $paramGroups[ $instance->type ][] = [
                'key'         => $instance->key,
                'cast'        => $instance->cast,
                'required'    => $instance->required,
                'description' => $instance->description,
                'example'     => $instance->example,
            ];
        }

        return $paramGroups;
    }

    private function controllerNamespace(): string
    {
        $basePath     = base_path() . '/';
        $relativePath = Str::after($this->controllerPath, $basePath);
        $trimmedPath  = trim($relativePath, '/');
        $namespace    = str_replace('/', '\\', $trimmedPath);
        $default      = 'App\\Http\\Controllers';

        return $namespace ?: $default;
    }

    private function getClassFromFile(string $file): ?string
    {
        $src = file_get_contents($file);
        if (preg_match('/namespace\s+([^;]+);/', $src, $nsMatch) &&
            preg_match('/class\s+([^\s{]+)/', $src, $classMatch)) {
            return trim($nsMatch[1]) . '\\' . trim($classMatch[1]);
        }

        return null;
    }

    private function getPhpFiles(string $dir): array
    {
        return File::allFiles($dir);
    }
}
