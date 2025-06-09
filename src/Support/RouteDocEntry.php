<?php

namespace RouteDocs\Support;

class RouteDocEntry
{
    protected bool $useColor = false;

    public function __construct(
        public string  $class,
        public string  $action,
        public string  $method,
        public string  $path,
        public ?string $name = null,
        public bool    $error = false,
    ) {
    }

    public function setOutputColor(bool $useColor = true): void
    {
        $this->useColor = $useColor;
    }

    protected function colorHttpMethod(string $method): string
    {
        return match (strtoupper($method)) {
            'GET'    => '<fg=blue>GET</>',
            'POST'   => '<fg=yellow>POST</>',
            'PUT'    => '<fg=cyan>PUT</>',
            'PATCH'  => '<fg=cyan>PATCH</>',
            'DELETE' => '<fg=red>DELETE</>',
            default  => "<fg=white>{$method}</>",
        };
    }

    protected function highlightPathVariables(string $path): string
    {
        return preg_replace('/\{([^}]+)\}/', '<fg=yellow>{\\1}</>', $path);
    }

    protected function highlightNestedController(string $controller): string
    {
        $segments = explode('\\', $controller);

        return collect($segments)->map(function ($part, $i) use ($segments) {
            $isLast = $i === array_key_last($segments);

            return $isLast
                ? $part
                : "<fg=gray>{$part}" . '/' . "</fg=gray>";
        })->implode('');
    }

    public function colorArray(): array
    {
        return [
            'error'  => $this->error ? '<fg=red>X</>' : '',
            'method' => $this->colorHttpMethod($this->method),
            'path'   => $this->highlightPathVariables($this->path),
            'name'   => $this->name ?? '',
            'class'  => $this->highlightNestedController($this->class),
            'action' => $this->action,
        ];
    }

    public function rawArray(): array
    {
        return [
            'error'  => $this->error ? 'X' : '',
            'method' => $this->httpMethod,
            'path'   => $this->path,
            'name'   => $this->name ?? '',
            'class'  => $this->class,
            'action' => $this->action,
        ];
    }

    public function toArray(): array
    {
        return $this->useColor ? $this->colorArray() : $this->rawArray();
    }
}
