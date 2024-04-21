<?php

namespace app\Core;

/**
 * Class Router
 *
 * This class is responsible for handling the routes of the application.
 * It uses the Request and Response classes to get the request and response of the application.
 *
 * @package app\Core
 */
class Router
{
    /**
     * @var array $routes The routes of the application.
     */
    protected array $routes = [];

    /**
     * @var Request $request The request instance.
     */
    public Request $request;

    /**
     * @var Response $response The response instance.
     */
    public Response $response;

    /**
     * Router constructor.
     *
     * Initializes the request, response, and routes of the application.
     *
     * @param Request $request The request instance.
     * @param Response $response The response instance.
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->routes['get'] = [];
        $this->routes['post'] = [];
    }

    /**
     * Method get
     *
     * Adds a GET route to the application.
     *
     * @param array $routes The routes to add to the application.
     */
    public function register(array $routes): void
    {
        $this->routes['get'] = array_merge($this->routes['get'], $routes['get']);
        $this->routes['post'] = array_merge($this->routes['post'], $routes['post']);
    }

    /**
     * Method get
     *
     * Adds a GET route to the application.
     *
     * @param string $url The URL of the route.
     */
    public function setIntendedUrl(string $url): void
    {
        Application::$app->session->set('url.intended', $url);
    }

    /**
     * Method get
     *
     * Adds a GET route to the application.
     *
     * @param string $default The default path of the route.
     */
    public function intended(string $default = '/'): void
    {
        $path = Application::$app->session->get('url.intended');
        if(isset($path) && strlen($path) > 0) {
            Application::$app->response->redirect($path);
        }
        Application::$app->response->redirect($default);
    }

    /**
     * Method get
     *
     * Adds a GET route to the application.
     *
     * @return mixed
     */
    public function resolve(): mixed
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;
        if ($callback === false) {
            $this->response->setStateCode(404);
            Application::$app->controller->layout = 'auth';
            return $this->renderView('_404');
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

    /**
     * Method renderView
     *
     * Render view with params.
     *
     * @param $view
     * @param array $params
     * @return array|bool|string
     */
    public function renderView($view, array $params = []): array|bool|string
    {
        $layoutContent = $this->layoutContent();
        $viewContent = $this->renderViewContent($view, $params);
        // param = [[],[]]
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    /**
     * Method layoutContent
     *
     * Renders the layout content.
     *
     * @return bool|string The layout content.
     */
    protected function layoutContent(): bool|string
    {
        $layout = Application::$app->controller->layout;
        ob_start();
        include_once __DIR__ . "/../views/layouts/$layout.php";
        return ob_get_clean();
    }

    /**
     * Method renderViewContent
     *
     * Renders the content of the view.
     *
     * @param $view
     * @param array $params
     * @return array|bool|string
     */
    protected function renderViewContent($view, array $params = []): array|bool|string
    {
        foreach ($params as $key => $param) {
            $$key = $param;
        }
        ob_start();
        include_once Application::$ROOT_DIR . "/views/$view.php";
        return ob_get_clean();
    }

    /**
     * Method renderContent
     *
     * Renders the content of the view.
     *
     * @param $viewContent
     * @return array|bool|string
     */
    public function renderContent($viewContent): array|bool|string
    {
        $layoutContent = $this->layoutContent();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }
}