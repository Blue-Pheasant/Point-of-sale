<?php

namespace app\Core;

use Exception;

/**
 * Class Application
 *
 * This class is responsible for handling the application operations.
 * It uses the Router, Request, Response, Controller, Database, Session, and View classes.
 *
 * @package app\Core
 */
class Application
{
    /**
     * @var string $EVENT_BEFORE_REQUEST The event before the request.
     */
    const EVENT_BEFORE_REQUEST = 'beforeRequest';

    /**
     * @var string $EVENT_AFTER_REQUEST The event after the request.
     */
    const EVENT_AFTER_REQUEST = 'afterRequest';

    /**
     * @var array $eventListeners The event listeners.
     */
    protected array $eventListeners = [];

    /**
     * @var Application $app The application instance.
     */
    public static Application $app;

    /**
     * @var string $ROOT_DIR The root directory of the application.
     */
    public static string $ROOT_DIR;

    /**
     * @var string $userClass The user class.
     */
    public string $userClass;

    /**
     * @var string $layout The layout of the application.
     * Default is 'main'.
     */
    public string $layout = 'main';

    /**
     * @var Router $router The router instance.
     */
    public Router $router;

    /**
     * @var Request $request The request instance.
     */
    public Request $request;

    /**
     * @var Response $response The response instance.
     */
    public Response $response;

    /**
     * @var ?Controller $controller The controller instance.
     */
    public ?Controller $controller = null;

    /**
     * @var Database $db The database instance.
     */
    public Database $db;

    /**
     * @var Session $session The session instance.
     */
    public Session $session;

    /**
     * @var View $view The view instance.
     */
    public View $view;

    /**
     * Application constructor.
     *
     * Initializes the application with the root directory and configuration.
     *
     * @param string $rootDir The root directory of the application.
     * @param array $config The configuration of the application.
     */
    public function __construct(string $rootDir, array $config)
    {
        $this->userClass = $config['userClass'];
        self::$ROOT_DIR = $rootDir;
        self::$app = $this;
        $this->controller = new Controller();
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        $this->db = new Database($config['db']);
        $this->session = new Session();
        $this->view = new View();
    }

    /**
     * Bootstraps the application.
     *
     * This method triggers the EVENT_BEFORE_REQUEST event, then tries to resolve the current route.
     * If the route is resolved successfully, it echoes the result. If an exception is thrown during
     * route resolution, it catches the exception and echoes an error view, passing the exception to the view.
     *
     * @return void
     */
    public function bootstrap(): void
    {
        $this->triggerEvent(self::EVENT_BEFORE_REQUEST);
        try {
            echo $this->router->resolve();
        } catch (Exception $e) {
            echo $this->router->renderView('_error', [
                'exception' => $e,
            ]);
        }
    }

    /**
     * Triggers an event.
     *
     * This method triggers the specified event by calling all the callbacks registered for this event.
     * If no callbacks are registered for the event, it does nothing.
     *
     * @param string $eventName The name of the event to trigger.
     * @return void
     */
    public function triggerEvent(string $eventName): void
    {
        $callbacks = $this->eventListeners[$eventName] ?? [];
        foreach ($callbacks as $callback) {
            call_user_func($callback);
        }
    }

    /**
     * Registers an event listener.
     *
     * This method registers a callback for the specified event.
     *
     * @param string $eventName The name of the event to listen for.
     * @param callable $callback The callback to call when the event is triggered.
     * @return void
     */
    public function on(string $eventName, callable $callback): void
    {
        $this->eventListeners[$eventName][] = $callback;
    }

    /**
     * Triggers an event.
     *
     * This method triggers the specified event by calling all the callbacks registered for this event.
     * If no callbacks are registered for the event, it does nothing.
     *
     * @param string $routeClass
     * @return void
     */
    public function useRoute(string $routeClass): void
    {
        $route = new $routeClass();
        $this->router->register($route->routes());
    }
}
