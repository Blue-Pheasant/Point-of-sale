<?php

namespace app\core;

use app\core\Application;
use app\core\Middleware;
use app\core\Router;
use app\core\Response;

class Controller
{
    public string $layout = 'main';
    public string $action = '';
    public Middleware $middleware;

    public function render($view, $params = [])
    {
        return Application::$app->router->renderView($view, $params);
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function registerMiddleware($middleware, $actions = [])
    {
        $this->middleware = new $middleware($actions);
        $this->middleware->execute();
    }

    public function getMiddleware()
    {
        return $this->middleware;
    }

    public function redirect($url)
    {
        return  Application::$app->response->redirect($url);
    }

    public function intended($url)
    {
        return Application::$app->router->intended($url);
    }

    public function setFlash($key, $message)
    {
        return Application::$app->session->setFlash($key, $message);
    }
}