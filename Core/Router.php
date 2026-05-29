<?php

namespace app\Core;

/**
 * Class Router
 *
 * Resolves the incoming request to a route. Supports dynamic path parameters
 * (`/{id}`), the GET/POST/PUT/PATCH/DELETE verbs (with `_method` spoofing for
 * HTML forms), and a per-route middleware pipeline that runs BEFORE the action.
 *
 * @package app\Core
 */
class Router
{
    /**
     * @var array<string, array<int, array{path: string, callback: mixed, middleware: array<int, array{0: string, 1: array<int, string>}>}>>
     *     The registered routes keyed by HTTP method (lowercase).
     */
    protected array $routes = [];

    /**
     * @var array<int, string> The HTTP methods supported by the router.
     */
    private const METHODS = ['get', 'post', 'put', 'patch', 'delete'];

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
     * @param Request $request The request instance.
     * @param Response $response The response instance.
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
        foreach (self::METHODS as $method) {
            $this->routes[$method] = [];
        }
    }

    /**
     * Merges the given route table into the router.
     *
     * @param array<string, array<int, mixed>> $routes The routes to register.
     */
    public function register(array $routes): void
    {
        foreach (self::METHODS as $method) {
            if (!empty($routes[$method])) {
                $this->routes[$method] = array_merge($this->routes[$method], $routes[$method]);
            }
        }
    }

    /**
     * Stores the URL the user intended to visit before being redirected.
     *
     * @param string $url The URL to remember.
     */
    public function setIntendedUrl(string $url): void
    {
        Application::$app->session->set('url.intended', $url);
    }

    /**
     * Redirects to the previously intended URL, or to a default.
     *
     * @param string $default The default path to fall back to.
     */
    public function intended(string $default = '/'): void
    {
        $path = Application::$app->session->get('url.intended');
        if (isset($path) && strlen($path) > 0) {
            Application::$app->response->redirect($path);
        }
        Application::$app->response->redirect($default);
    }

    /**
     * Resolves the current request to a route and executes it.
     *
     * @return mixed The rendered response.
     */
    public function resolve(): mixed
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();

        // Try to match within the requested method first.
        $match = $this->matchRoute($method, $path);
        if ($match !== null) {
            return $this->runRoute($match['route'], $match['params']);
        }

        // The path exists under a different verb → 405 Method Not Allowed.
        if ($this->pathExistsForOtherMethod($method, $path)) {
            return $this->abort(405, '_404');
        }

        // No route matches the path at all → 404 Not Found.
        return $this->abort(404, '_404');
    }

    /**
     * Finds a route matching the method and path, extracting any parameters.
     *
     * @param string $method The lowercase HTTP method.
     * @param string $path The request path.
     * @return array{route: array{path: string, callback: mixed, middleware: array<int, array{0: string, 1: array<int, string>}>}, params: array<int, string>}|null
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

    /**
     * Tests whether the same path is registered under any other method.
     *
     * @param string $currentMethod The method already tried.
     * @param string $path The request path.
     * @return bool True when the path matches under a different verb.
     */
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

    /**
     * Matches a route pattern against a concrete path.
     *
     * A pattern such as `/products/{id}` is compiled to a regex; on a match the
     * captured parameter values are returned in declaration order. A static
     * pattern returns an empty array on a match. Returns null when there is no
     * match.
     *
     * @param string $pattern The route pattern (may contain `{param}`).
     * @param string $path The concrete request path.
     * @return array<int, string>|null The captured parameters, or null.
     */
    private function matchPath(string $pattern, string $path): ?array
    {
        // Fast path: a static pattern with no parameters.
        if (!str_contains($pattern, '{')) {
            return rtrim($pattern, '/') === rtrim($path, '/') ? [] : null;
        }

        $regex = preg_replace('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', '([^/]+)', $pattern);
        $regex = '#^' . rtrim((string) $regex, '/') . '/?$#';

        if (preg_match($regex, rtrim($path, '/'), $matches)) {
            array_shift($matches);
            return array_values($matches);
        }

        return null;
    }

    /**
     * Runs a matched route: its middleware pipeline, then its action.
     *
     * @param array{path: string, callback: mixed, middleware: array<int, array{0: string, 1: array<int, string>}>} $route
     * @param array<int, string> $params The extracted route parameters.
     * @return mixed The rendered response.
     */
    private function runRoute(array $route, array $params): mixed
    {
        $callback = $route['callback'];

        // A plain view name → render it directly.
        if (is_string($callback)) {
            return $this->renderView($callback);
        }

        [$controllerClass, $action] = $callback;

        // Make the resolved action visible to the application context BEFORE the
        // controller is constructed, so constructor-registered (action-scoped)
        // middleware reads the correct action. This fixes the previous ordering
        // bug where the action was assigned to the outgoing controller instance.
        Application::$app->controller->action = $action;

        // Run route-level middleware before instantiating the controller/action.
        $this->runMiddleware($route['middleware'], $action);

        $controller = new $controllerClass();
        Application::$app->controller = $controller;
        // Preserve the action on the freshly built controller too.
        $controller->action = $action;

        return call_user_func([$controller, $action], $this->request, ...$params);
    }

    /**
     * Executes the route-level middleware pipeline.
     *
     * @param array<int, array{0: string, 1: array<int, string>}> $middleware
     * @param string $action The resolved action (for action-scoped middleware).
     * @return void
     */
    private function runMiddleware(array $middleware, string $action): void
    {
        foreach ($middleware as [$middlewareClass, $actions]) {
            // When no actions are listed, the middleware applies to every action
            // on the route; otherwise reuse the action-scoped contract.
            $scoped = empty($actions) ? [$action] : $actions;
            $instance = new $middlewareClass($scoped);
            $instance->execute();
        }
    }

    /**
     * Sends an error status and renders the matching error view.
     *
     * @param int $status The HTTP status code.
     * @param string $view The error view to render.
     * @return string The rendered error page.
     */
    private function abort(int $status, string $view): string
    {
        // NOTE: setStateCode is renamed to setStatusCode in T08 (alias kept).
        $this->response->setStateCode($status);
        Application::$app->controller->layout = 'auth';
        return $this->renderView($view);
    }

    /**
     * Renders a view inside the active layout.
     *
     * @param string $view The view name.
     * @param array<string, mixed> $params The view parameters.
     * @return array|bool|string The rendered page.
     */
    public function renderView($view, array $params = []): array|bool|string
    {
        $layoutContent = $this->layoutContent();
        $viewContent = $this->renderViewContent($view, $params);
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    /**
     * Renders the active layout.
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
     * Renders a view's body content.
     *
     * @param string $view The view name.
     * @param array<string, mixed> $params The view parameters.
     * @return array|bool|string The rendered content.
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
     * Renders pre-built content inside the active layout.
     *
     * @param string $viewContent The body content.
     * @return array|bool|string The rendered page.
     */
    public function renderContent($viewContent): array|bool|string
    {
        $layoutContent = $this->layoutContent();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }
}
