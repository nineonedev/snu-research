<?php 

namespace app\facades;

use app\lib\routing\Route as RoutingRoute;
use app\lib\routing\Router;

class Route {
    protected static Router $router; 
    protected static ?RoutingRoute $currentRoute; 

    public static function init(): void 
    {
        static::$router = new Router();
    }

    public static function __callStatic($method, $args)
    {
        return static::$router->$method(...$args); 
    }

    public static function currentRouteName(): ?string
    {
        return static::$currentRoute ? static::$currentRoute->name : null;
    }


    public static function dispatch()
    {
        $request = App::request();
        $method = $request->method();
        $uri = parse_url($request->path(), PHP_URL_PATH);

        static::$currentRoute = static::$router->matchRoute($method, $uri);
        
        return static::$router->dispatch($method, $uri);
    }

    public function getRouter(): Router
    {
        return static::$router;
    }
    
    public static function debugRoutes(): void
    {
        static::$router->debugRoutes();
    }

}