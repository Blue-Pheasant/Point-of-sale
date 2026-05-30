<?php

namespace app\Core;

/**
 * Class Route
 *
 * Base class for route definitions. Subclasses implement {@see self::register()}
 * and use the verb helpers ({@see self::get()}, {@see self::post()}, ...) to
 * declare routes. Each declared route supports:
 *   - dynamic parameters: `/products/{id}` → passed to the action.
 *   - per-route middleware via the chained {@see self::middleware()}.
 *   - grouping (shared prefix + middleware) via {@see self::group()}.
 *
 * The collected routes are returned by {@see self::routes()} and consumed by
 * {@see Router::register()}.
 *
 * @package app\Core
 */
abstract class Route
{
    /**
     * @var array<string, array<int, array{path: string, callback: mixed, middleware: array<int, array{0: string, 1: array<int, string>}>}>>
     *     The routes of the application, keyed by HTTP method (lowercase).
     */
    protected array $routes = [];

    /**
     * @var string The current group path prefix (empty when not grouping).
     */
    private string $groupPrefix = '';

    /**
     * @var array<int, array{0: string, 1: array<int, string>}> The current
     *     group middleware stack.
     */
    private array $groupMiddleware = [];

    /**
     * @var array{method: string, index: int}|null A pointer to the most
     *     recently declared route, so {@see self::middleware()} can attach to it.
     */
    private ?array $lastRoute = null;

    /**
     * Route constructor.
     */
    public function __construct()
    {
        $this->register();
    }

    /**
     * Registers the routes of the application.
     */
    abstract public function register();

    /**
     * Gets the collected routes.
     *
     * @return array<string, array<int, array{path: string, callback: mixed, middleware: array<int, array{0: string, 1: array<int, string>}>}>>
     */
    public function routes(): array
    {
        return $this->routes;
    }

    /**
     * Adds a GET route.
     *
     * @param string $path The path of the route (may contain `{param}`).
     * @param mixed $callback The `[Controller::class, 'action']` pair or a view name.
     * @return self
     */
    public function get(string $path, mixed $callback): self
    {
        return $this->addRoute('get', $path, $callback);
    }

    /**
     * Adds a POST route.
     *
     * @param string $path The path of the route (may contain `{param}`).
     * @param mixed $callback The `[Controller::class, 'action']` pair or a view name.
     * @return self
     */
    public function post(string $path, mixed $callback): self
    {
        return $this->addRoute('post', $path, $callback);
    }

    /**
     * Adds a PUT route.
     *
     * @param string $path The path of the route (may contain `{param}`).
     * @param mixed $callback The `[Controller::class, 'action']` pair.
     * @return self
     */
    public function put(string $path, mixed $callback): self
    {
        return $this->addRoute('put', $path, $callback);
    }

    /**
     * Adds a PATCH route.
     *
     * @param string $path The path of the route (may contain `{param}`).
     * @param mixed $callback The `[Controller::class, 'action']` pair.
     * @return self
     */
    public function patch(string $path, mixed $callback): self
    {
        return $this->addRoute('patch', $path, $callback);
    }

    /**
     * Adds a DELETE route.
     *
     * @param string $path The path of the route (may contain `{param}`).
     * @param mixed $callback The `[Controller::class, 'action']` pair.
     * @return self
     */
    public function delete(string $path, mixed $callback): self
    {
        return $this->addRoute('delete', $path, $callback);
    }

    /**
     * Attaches middleware to the most recently declared route.
     *
     * @param string $middleware The middleware class name.
     * @param array<int, string> $actions Optional action names the middleware
     *     applies to (kept for compatibility with the action-scoped middleware).
     * @return self
     */
    public function middleware(string $middleware, array $actions = []): self
    {
        if ($this->lastRoute === null) {
            return $this;
        }

        $method = $this->lastRoute['method'];
        $index = $this->lastRoute['index'];
        $this->routes[$method][$index]['middleware'][] = [$middleware, $actions];

        return $this;
    }

    /**
     * Declares a group of routes sharing a path prefix and/or middleware.
     *
     * @param array{prefix?: string, middleware?: array<int, array{0: string, 1: array<int, string>}|string>} $attributes
     *     Group attributes: `prefix` and/or `middleware`.
     * @param callable $callback Receives this Route instance to declare routes.
     * @return void
     */
    public function group(array $attributes, callable $callback): void
    {
        $previousPrefix = $this->groupPrefix;
        $previousMiddleware = $this->groupMiddleware;

        if (isset($attributes['prefix'])) {
            $this->groupPrefix = $previousPrefix . $attributes['prefix'];
        }

        if (isset($attributes['middleware'])) {
            foreach ($attributes['middleware'] as $mw) {
                $this->groupMiddleware[] = is_array($mw) ? $mw : [$mw, []];
            }
        }

        $callback($this);

        // Restore the enclosing group's state when leaving the group.
        $this->groupPrefix = $previousPrefix;
        $this->groupMiddleware = $previousMiddleware;
    }

    /**
     * Registers a route for the given method, applying any active group prefix
     * and middleware, and records it as the most recent route.
     *
     * @param string $method The lowercase HTTP method.
     * @param string $path The route path.
     * @param mixed $callback The route callback.
     * @return self
     */
    private function addRoute(string $method, string $path, mixed $callback): self
    {
        $fullPath = $this->groupPrefix . $path;

        $this->routes[$method] ??= [];
        $this->routes[$method][] = [
            'path' => $fullPath,
            'callback' => $callback,
            'middleware' => $this->groupMiddleware,
        ];

        $this->lastRoute = ['method' => $method, 'index' => array_key_last($this->routes[$method])];

        return $this;
    }
}
