<?php 

namespace app\core; 

abstract class Controller {
    protected function json($data = [], int $status = 200): Response
    {
        return Response::json($data, $status); 
    }

    protected function view(string $template, array $data = [], $layout = 'home'): Response
    {
        return render($template, $data, $layout); 
    }

    protected function redirect(string $url): Response
    {
        return Response::redirect($url);
    }
}