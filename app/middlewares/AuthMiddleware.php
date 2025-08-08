<?php 

namespace app\middlewares;

use app\contracts\MiddlewareInterface;
use app\core\Response;
use app\core\Session;

class AuthMiddleware implements MiddlewareInterface {
    public function handle($request, callable $next)
    {
        if (!Session::get('user_id')) {
            Response::alert('로그인이 필요한 서비스입니다.', '/auth/signin');
        }

        return $next($request);
    }
}