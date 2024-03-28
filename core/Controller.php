<?php

namespace app\core;

use app\core\Application;
use app\middlewares\BaseMiddleware;

class Controller
{
    public string $layout = 'main';
    public string $action = '';
    public BaseMiddleware $middleware;

    
    public function render($view, $params = [])
    {
        return Application::$app->router->renderView($view, $params);
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function registerMiddleware(BaseMiddleware $middleware)
    {
        $this->middleware = $middleware;
        $this->middleware->execute();
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}