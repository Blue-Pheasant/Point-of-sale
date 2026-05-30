<?php

namespace app\Core;

use app\Core\Container\Container;
use app\Exception\ForbiddenException;
use app\Exception\ForLoginException;
use app\Exception\NotFoundException;
use app\Middlewares\CsrfMiddleware;
use PDO;
use Psr\Container\ContainerInterface;
use Throwable;

/**
 * Application bootstrap and central exception handler.
 *
 * Exception mapping:
 *   ForLoginException  → redirect to /login (stores intended URL)
 *   ForbiddenException → 403 + error view
 *   NotFoundException  → 404 + error view
 *   Any other          → 500 + error view (stack trace shown only when APP_DEBUG=true)
 */
class Application
{
    public const EVENT_BEFORE_REQUEST = 'beforeRequest';
    public const EVENT_AFTER_REQUEST  = 'afterRequest';

    protected array $eventListeners = [];

    public static Application $app;
    public static string      $ROOT_DIR;

    public Container   $container;
    public string      $userClass;
    public string      $layout = 'main';
    public Router      $router;
    public Request     $request;
    public Response    $response;
    public ?Controller $controller = null;
    public Database    $db;
    public Session     $session;
    public View        $view;

    public function __construct(string $rootDir, array $config)
    {
        $this->userClass = $config['userClass'];
        self::$ROOT_DIR  = $rootDir;
        self::$app       = $this;

        $this->container = new Container();
        $container       = $this->container;
        $container->instance(Container::class, $container);
        $container->instance(ContainerInterface::class, $container);
        $container->instance(self::class, $this);

        // Core services are shared singletons; the public properties below are
        // kept as backward-compatible aliases pointing at the SAME instances the
        // container holds, so `Application::$app->db` and `$container->get(...)`
        // never diverge.
        $this->request  = $container->instance(Request::class, new Request());
        $this->response = $container->instance(Response::class, new Response());
        $this->session  = $container->instance(Session::class, new Session());
        $this->view     = $container->instance(View::class, new View());
        $this->db       = $container->instance(Database::class, new Database($config['db']));
        $container->instance(PDO::class, $this->db->pdo);

        // Bind the payment gateway abstraction to the sandbox driver (roadmap
        // T22). Swapping to a real gateway is a single line here — the business
        // layer depends only on the interface, so nothing else changes.
        $container->bind(
            \app\Services\Payment\PaymentGatewayInterface::class,
            \app\Services\Payment\SandboxGateway::class
        );

        $this->router     = $container->instance(Router::class, new Router($this->request, $this->response));
        $this->controller = new Controller();
    }

    public function bootstrap(): void
    {
        $this->triggerEvent(self::EVENT_BEFORE_REQUEST);
        // CSRF check runs before routing so every mutation request is protected.
        $this->container->make(CsrfMiddleware::class)->execute();
        try {
            echo $this->router->resolve();
        } catch (Throwable $e) {
            echo $this->handleException($e);
        }
    }

    /**
     * Map an exception to an HTTP status + view and render the error page.
     *
     * @return string The rendered error HTML.
     */
    protected function handleException(Throwable $e): string
    {
        $debug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($e instanceof ForLoginException) {
            // Intended URL already stored in the constructor.
            $this->response->redirect('/login');
            return '';
        }

        if ($e instanceof ForbiddenException) {
            $this->response->setStatusCode(403);
            $this->controller->layout = 'auth';
            return $this->router->renderView('_error', [
                'exception' => $debug ? $e : null,
                'statusCode' => 403,
                'message'    => 'Bạn không có quyền truy cập trang này.',
            ]);
        }

        if ($e instanceof NotFoundException) {
            $this->response->setStatusCode(404);
            $this->controller->layout = 'auth';
            return $this->router->renderView('_404', []);
        }

        // Unexpected error → 500.
        $this->response->setStatusCode(500);
        $this->controller->layout = 'auth';

        if (!$debug) {
            error_log((string) $e);
        }

        return $this->router->renderView('_error', [
            'exception'  => $debug ? $e : null,
            'statusCode' => 500,
            'message'    => 'Đã xảy ra lỗi không mong muốn. Vui lòng thử lại sau.',
        ]);
    }

    public function triggerEvent(string $eventName): void
    {
        $callbacks = $this->eventListeners[$eventName] ?? [];
        foreach ($callbacks as $callback) {
            call_user_func($callback);
        }
    }

    public function on(string $eventName, callable $callback): void
    {
        $this->eventListeners[$eventName][] = $callback;
    }

    public function useRoute(string $routeClass): void
    {
        $route = new $routeClass();
        $this->router->register($route->routes());
    }
}
