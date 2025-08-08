<?php

namespace app\lib\routing;

use app\core\Config;
use app\core\MiddlewareDispatcher;
use app\facades\App;
use Closure;

class Router
{
    protected RouteCollection $routes;
    protected array $groupStack = [];

    public function __construct()
    {
        $this->routes = new RouteCollection();
    }

    public function get(string $uri, $action): Route
    {
        return $this->addRoute('GET', $uri, $action);
    }

    public function post(string $uri, $action): Route
    {
        return $this->addRoute('POST', $uri, $action);
    }

    public function put(string $uri, $action): Route
    {
        return $this->addRoute('PUT', $uri, $action);
    }

    public function patch(string $uri, $action): Route
    {
        return $this->addRoute('PATCH', $uri, $action);
    }

    public function delete(string $uri, $action): Route
    {
        return $this->addRoute('DELETE', $uri, $action);
    }

    public function matchRoute(string $method, string $uri): ?Route
    {
        $uri = '/' . trim(parse_url($uri, PHP_URL_PATH), '/');
        return $this->routes->match($method, $uri);
    }

    public function debugRoutes(): void
    {
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<thead><tr><th>Method</th><th>URI</th><th>Name</th><th>Action</th></tr></thead><tbody>";

        foreach ($this->routes->all() as $method => $routes) {
            foreach ($routes as $route) {
                $action = is_array($route->action)
                    ? $route->action[0] . '@' . $route->action[1]
                    : (is_string($route->action) ? $route->action : 'Closure');

                echo "<tr>";
                echo "<td>{$method}</td>";
                echo "<td>{$route->uri}</td>";
                echo "<td>{$route->name}</td>";
                echo "<td>{$action}</td>";
                echo "</tr>";
            }
        }

        echo "</tbody></table>";
        exit;
    }


    public function resource(string $name, string $controller): void
    {
        $base = trim($name, '/');
    
        $this->get($base, [$controller, 'index'])->name("$name.index");
        $this->get("$base/create", [$controller, 'create'])->name("$name.create");
        $this->post($base, [$controller, 'store'])->name("$name.store");
        $this->get("$base/{id}", [$controller, 'show'])->name("$name.show");
        $this->get("$base/{id}/edit", [$controller, 'edit'])->name("$name.edit");
        $this->put("$base/{id}", [$controller, 'update'])->name("$name.update");
        $this->patch("$base/{id}", [$controller, 'update']);
        $this->delete("$base/{id}", [$controller, 'destroy'])->name("$name.destroy");
    }
    

    public function group($config, Closure $callback): RouteGroup
    {
        if (is_string($config)) {
            $config = ['prefix' => $config];
        }

        $prefix = $config['prefix'] ?? '';
        $middleware = $config['middleware'] ?? [];

        $group = new RouteGroup($prefix, $middleware);
        $this->groupStack[] = $group;

        $callback();

        array_pop($this->groupStack);

        return $group;
    }

    protected function addRoute(string $method, string $uri, $action): Route
    {
        $group = end($this->groupStack) ?: new RouteGroup();
        $uri = $group->applyTo($uri);
        $middlewares = $group->mergeMiddleware([]);

        $route = new Route($method, $uri, $action);
        $route->middleware($middlewares);

        $this->routes->add($route);

        return $route;
    }

    public function dispatch(string $method, string $uri)
    {
        $uri = '/' . trim(parse_url($uri, PHP_URL_PATH), '/');
        $route = $this->routes->match($method, $uri);

        if (!$route) {
            return render('errors.404', [], 404);
        }

        $params = $route->extractParams($uri);
        $dispatcher = new MiddlewareDispatcher();

        foreach ($route->middleware as $mw) {
            $dispatcher->add($mw);
        }

        return $dispatcher->run($params, function ($params) use ($route) {
            $request = App::request();
            $args = [$request, ...array_values($params)];

            // ✅ 1. 먼저 배열인지 확인 (클래스+메서드)
            if (is_array($route->action)) {
                [$controller, $method] = $route->action;
                return call_user_func_array([new $controller(), $method], $args);
            }

            // ✅ 2. 그 다음 일반 콜러블 (클로저 등)
            if (is_callable($route->action)) {
                return call_user_func_array($route->action, $args);
            }

            throw new \Exception("Invalid route action");
        });
    }

    public function route(string $name, array $params = []): ?string
    {
        $route = $this->routes->getByName($name);
        if (!$route) return null;

        $uri = $route->uri;
        foreach ($params as $key => $value) {
            $uri = str_replace("{" . $key . "}", $value, $uri);
        }

        return $uri;
    }
}
