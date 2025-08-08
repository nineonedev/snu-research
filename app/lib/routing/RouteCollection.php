<?php 

namespace app\lib\routing;

class RouteCollection
{
    protected array $routes = [];
    protected array $namedRoutes = [];

    public function add(Route $route): void
    {
        $this->routes[$route->method][] = $route;

        if ($route->name) {
            $this->namedRoutes[$route->name] = $route;
        }
    }

    public function match(string $method, string $uri): ?Route
    {
        foreach ($this->routes[strtoupper($method)] ?? [] as $route) {
            if ($route->matches($method, $uri)) {
                return $route;
            }
        }

        return null;
    }

    public function getByName(string $name): ?Route
    {
        return $this->namedRoutes[$name] ?? null;
    }

    public function all(): array
    {
        return $this->routes;
    }
}