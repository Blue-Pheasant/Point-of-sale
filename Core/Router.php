<?php

namespace app\Core;

/**
 * Resolves the incoming request to a route. Supports dynamic path parameters
 * (`/{id}`), the GET/POST/PUT/PATCH/DELETE verbs (with `_method` spoofing for
 * HTML forms), and a per-route middleware pipeline that runs BEFORE the action.
 *
 * Render logic is fully delegated to {@see View} — no duplicate layout/content
 * rendering lives here.
 *
 * @package app\Core
 */
class Router
{
    /**
     * @var array<string, array<int, array{path: string, callback: mixed, middleware: array<int, array{0: string, 1: array<int, string>}>}>>
     */
    protected array $routes = [];

    private const METHODS = ['get', 'post', 'put', 'patch', 'delete'];

    public Request  $request;
    public Response $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request  = $request;
        $this->response = $response;
        foreach (self::METHODS as $method) {
            $this->routes[$method] = [];
        }
    }

    /** @param array<string, array<int, mixed>> $routes */
    public function register(array $routes): void
    {
        foreach (self::METHODS as $method) {
            if (!empty($routes[$method])) {
                $this->routes[$method] = array_merge($this->routes[$method], $routes[$method]);
            }
        }
    }

    public function setIntendedUrl(string $url): void
    {
        Application::$app->session->set('url.intended', $url);
    }

    public function intended(string $default = '/'): void
    {
        $path = Application::$app->session->get('url.intended');
        if (isset($path) && strlen($path) > 0) {
            Application::$app->response->redirect($path);
        }
        Application::$app->response->redirect($default);
    }

    public function resolve(): mixed
    {
        $path   = $this->request->getPath();
        $method = $this->request->getMethod();

        $match = $this->matchRoute($method, $path);
        if ($match !== null) {
            return $this->runRoute($match['route'], $match['params']);
        }

        if ($this->pathExistsForOtherMethod($method, $path)) {
            return $this->abort(405, '_404');
        }

        return $this->abort(404, '_404');
    }

    /**
     * @return array{route: array{path: string, callback: mixed, middleware: array<int, array{0: string, 1: array<int, string>}>}, params: array<string, string>}|null
     */
    private function matchRoute(string $method, string $path): ?array
    {
        foreach ($this->routes[$method] ?? [] as $route) {
            $params = $this->matchPath($route['path'], $path);
            if ($params !== null) {
                return ['route' => $route, 'params' => $params];
            }
        }
        return null;
    }

    private function pathExistsForOtherMethod(string $currentMethod, string $path): bool
    {
        foreach (self::METHODS as $method) {
            if ($method === $currentMethod) {
                continue;
            }
            if ($this->matchRoute($method, $path) !== null) {
                return true;
            }
        }
        return false;
    }

    /** @return array<string, string>|null */
    private function matchPath(string $pattern, string $path): ?array
    {
        if (!str_contains($pattern, '{')) {
            return rtrim($pattern, '/') === rtrim($path, '/') ? [] : null;
        }

        preg_match_all('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', $pattern, $nameMatches);
        $names = $nameMatches[1];

        $regex = preg_replace('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', '([^/]+)', $pattern);
        $regex = '#^' . rtrim((string) $regex, '/') . '/?$#';

        if (preg_match($regex, rtrim($path, '/'), $matches)) {
            array_shift($matches);
            return array_combine($names, array_values($matches));
        }

        return null;
    }

    /** @param array<string, string> $params */
    private function runRoute(array $route, array $params): mixed
    {
        $this->request->setRouteParams($params);

        $callback = $route['callback'];

        if (is_string($callback)) {
            return $this->renderView($callback);
        }

        [$controllerClass, $action] = $callback;

        Application::$app->controller->action = $action;
        $this->runMiddleware($route['middleware'], $action);

        $controller         = new $controllerClass();
        Application::$app->controller = $controller;
        $controller->action = $action;

        return call_user_func([$controller, $action], $this->request, ...array_values($params));
    }

    /** @param array<int, array{0: string, 1: array<int, string>}> $middleware */
    private function runMiddleware(array $middleware, string $action): void
    {
        foreach ($middleware as [$middlewareClass, $actions]) {
            $scoped   = empty($actions) ? [$action] : $actions;
            $instance = new $middlewareClass($scoped);
            $instance->execute();
        }
    }

    private function abort(int $status, string $view): string
    {
        $this->response->setStatusCode($status);
        Application::$app->controller->layout = 'auth';
        return $this->renderView($view);
    }

    /**
     * Render a view inside the active layout — delegates to View.
     *
     * @param array<string, mixed> $params
     */
    public function renderView(string $view, array $params = []): string
    {
        return Application::$app->view->renderView($view, $params);
    }

    /**
     * Render pre-built content inside the active layout — delegates to View.
     */
    public function renderContent(string $viewContent): string
    {
        return Application::$app->view->renderContent($viewContent);
    }
}
