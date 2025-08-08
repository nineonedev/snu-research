<?php

namespace app\lib\routing;

class Route
{
    public string $method;
    public string $uri;
    public $action;
    public array $middleware = [];
    public string $name = '';

    public function __construct(string $method, string $uri, $action)
    {
        $this->method = strtoupper($method);
        $this->uri = '/' . trim($uri, '/');
        $this->action = $action;
    }

    public function middleware(array $middleware): self
    {
        $this->middleware = $middleware;
        return $this;
    }

    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function matches(string $method, string $requestUri): bool
    {
        if (strtoupper($method) !== $this->method) return false;

        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $this->uri);
        return (bool)preg_match('#^' . $pattern . '$#', $requestUri);
    }

    public function extractParams(string $requestUri): array
    {
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $this->uri);
        preg_match('#^' . $pattern . '$#', $requestUri, $matches);
        return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
    }
}
