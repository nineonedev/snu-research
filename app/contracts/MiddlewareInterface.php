<?php 

namespace app\contracts; 

interface MiddlewareInterface {
    public function handle($request, callable $next);
}