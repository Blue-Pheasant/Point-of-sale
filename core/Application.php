<?php

namespace app\core;


class Application
{
    const EVENT_BEFORE_REQUEST = 'beforeRequest';
    const EVENT_AFTER_REQUEST = 'afterRequest';

    protected array $eventListeners = [];

    public static Application $app;
    public static string $ROOT_DIR;
    public string $userClass;
    public string $layout = 'main';
    public Router $router;
    public Request $request;
    public Response $response;
    public Input $input;
    public ?Controller $controller = null;
    public Database $db;
    public Session $session;
    public View $view;
    public ?CustomerModel $customer;

    public function __construct($rootDir, $config)
    {

        $this->user = null;
        $this->userClass = $config['userClass'];
        self::$ROOT_DIR = $rootDir;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->input = new Input();
        $this->router = new Router($this->request, $this->response);
        $this->db = new Database($config['db']);
        $this->session = new Session();
        $this->view = new View();

        $userId = Application::$app->session->get('user');
        if ($userId) {
            $key = $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$key => $userId]);
        }
    }

    public static function isGuest()
    {
        return !self::$app->customer;
    }

    public function login(CustomerModel $customer)
    {
        $this->customer = $customer;
        $primaryKey = $customer->primaryKey();
        $value = $customer->{$primaryKey};
        Application::$app->session->set('customer', $value);

        return true;
    }

    public function logout()
    {
        $this->customer = null;
        self::$app->session->remove('customer');
    }

    public function run()
    {
        $this->triggerEvent(self::EVENT_BEFORE_REQUEST);
        try {
            echo $this->router->resolve();
        } catch (\Exception $e) {
            echo $this->router->renderView('_error', [
                'exception' => $e,
            ]);
        }
    }

    public function triggerEvent($eventName)
    {
        $callbacks = $this->eventListeners[$eventName] ?? [];
        foreach ($callbacks as $callback) {
            call_user_func($callback);
        }
    }

    public function on($eventName, $callback)
    {
        $this->eventListeners[$eventName][] = $callback;
    }
}