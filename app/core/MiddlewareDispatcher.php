<?php 

namespace app\core;

use Closure;

class MiddlewareDispatcher {
    protected array $middlewares = []; 

    public function add(string $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;  
    }

    public function run($request, callable $final)
    {
        $pipeline = array_reduce(
            array_reverse($this->middlewares),
            fn($next, $middleware) => fn($request) => (new $middleware)->handle($request, $next),
            $final
        );

        return $pipeline($request);
    }

    public function handle(Request $request, Closure $core): Response
    {
        $middlewareStack = array_reverse($this->middlewares); 

        $next = $core; 

        foreach ($middlewareStack as $middleware) {
            $next = function (Request $req) use ($middleware, $next) {
                if(is_string($middleware)) {
                    $middleware = new $middleware(); 
                }

                return $middleware->handle($req, $next); 
            }; 
        }

        return $next($request); 
    }
    

}