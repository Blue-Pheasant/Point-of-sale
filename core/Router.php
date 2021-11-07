<?php

namespace app\core;



class Router
{

    protected array $routers = [];
    public Request $request;
    public Response $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function get($path, $callback)
    {
        $this->routers['get'][$path] = $callback;
    }

    public function post($path, $callback)
    {
        $this->routers['post'][$path] = $callback;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routers[$method][$path] ?? false;
        if ($callback === false) {
            $this->response->setStateCode(404);
            return $this->renderView('_404');
            exit;
        }
        if (is_string($callback)) {
            return $this->renderView($callback);
        }
        if (is_array($callback)) {
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

            // $$key sẽ lấy giá trị của key làm tên biến và lưu value vào biến đó
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

    // public function redirect($location) 
    // {

    // }
}