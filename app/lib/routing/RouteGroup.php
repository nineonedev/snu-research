<?php

namespace app\lib\routing;

class RouteGroup
{
    public array $middleware;
    public string $prefix;

    public function __construct(string $prefix = '', array $middleware = [])
    {
        $this->prefix = rtrim($prefix, '/');
        $this->middleware = $middleware;
    }

    public function applyTo(string $uri): string
    {
        return rtrim($this->prefix . '/' . ltrim($uri, '/'), '/');
    }

    public function mergeMiddleware(array $middlewares): array
    {
        return array_merge($this->middleware, $middlewares);
    }

    public function middleware(string $middleware): self 
    {
        $this->middleware[] = $middleware;
        return $this;
    }

    public function middlewares(array $middlewares): self
    {
        $this->middleware = array_merge($this->middleware, $middlewares);
        return $this;
    }

}
