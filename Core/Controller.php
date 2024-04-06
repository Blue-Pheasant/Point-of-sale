<?php

namespace App\Core;

use App\Core\Application;
use App\Core\Middleware;
use App\Core\Router;
use App\Core\Response;

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

    public function getFlash($key)
    {
        return Application::$app->session->getFlash($key);
    }

    public function refresh()
    {
        $currentUrl = Application::$app->request->getReqest();
        return Application::$app->response->redirect($currentUrl);
    }

    public function back()
    {
        return Application::$app->response->back();
    }
}