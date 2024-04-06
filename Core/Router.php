<?php

namespace App\Core;



class Router
{

    protected array $routes = [];
    public Request $request;
    public Response $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->routes['get'] = [];
        $this->routes['post'] = [];
    }

    public function register($routes)
    {
        $this->routes['get'] = array_merge($this->routes['get'], $routes['get']);
        $this->routes['post'] = array_merge($this->routes['post'], $routes['post']);
    }

    public function setIntendedUrl($url)
    {
        Application::$app->session->set('url.intended', $url);
    }

    public function intended($default = '/')
    {
        $path = Application::$app->session->get('url.intended');
        if(isset($path) && strlen($path) > 0) {
            return Application::$app->response->redirect($path);
        }
        return Application::$app->response->redirect($default);
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;
        if ($callback === false) {
            $this->response->setStateCode(404);
            Application::$app->controller->layout = 'auth';
            return $this->renderView('_404');
            exit;
        }
        if (is_string($callback)) {
            return $this->renderView($callback);
        }
        if (is_array($callback)) {
            Application::$app->controller->action = $callback[1];
            Application::$app->controller = new $callback[0]();

            $callback[0] = Application::$app->controller;
        }
        return call_user_func($callback, $this->request);
    }

    // Return the view
    public function renderView($view, $params = [])
    {
        $layoutContent = $this->layoutContent();
        $viewContent = $this->renderViewContent($view, $params);
        // param = [[],[]]
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    // return layout of the view
    protected function layoutContent()
    {
        $layout = Application::$app->controller->layout;
        ob_start();
        include_once __DIR__ . "/../views/layouts/$layout.php";
        return ob_get_clean();
    }

    // render content of the view
    protected function renderViewContent($view, $params = [])
    {
        foreach ($params as $key => $param) {
            $$key = $param;
        }
        ob_start();
        include_once Application::$ROOT_DIR . "/views/$view.php";
        return ob_get_clean();
    }

    // render only the content without layout
    public function renderContent($viewContent)
    {
        $layoutContent = $this->layoutContent();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }
}