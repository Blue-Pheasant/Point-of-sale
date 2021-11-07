<?php

namespace app\core;

use app\core\Application;
use app\middlewares\BaseMiddleware;
use app\core\Session;

class Controller
{
    public string $layout = 'main';
    public $view;
    
    public function __construct()
    {
        $this->view = new View;
		Session::remove('success');
		Session::remove('error');
		Session::remove('errors');
		Session::remove('oldInput');
    }

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
        $this->middlewares[] = $middleware;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}