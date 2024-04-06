# Design patterns

Project follows several design patterns, including:

- Model-View-Controller (MVC): Project follows the MVC architecture pattern to separate the application logic into different layers. [Read more](architecture/architecture.md)  

- Builder pattern: Project uses Builder patterns because system is made up of many layers and structures. And wrapped with a class called `Application`. 

```php
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
    public ?Controller $controller = null;
    public Database $db;
    public Session $session;
    public View $view;
    public ?UserModel $user;
    public ?Cart $cart;
}
```

Try the object that a deprecated object requires a lot of effort, step-by-step initialization of many fields and nested objects. Such startup code is often buried in a function created with a lot of parameters. Or even worse: scattered throughout the client code. 

The Builder pattern provides an interface that is easy to upgrade and expand the system in the future. This is suitable for the current system as it only stops at tracking in some locations and the user file is quite small.

These are some example of convenient of builder pattern:

- Redirect page:
```php
    Application::$app->response->redirect('/');
```

- Check session of user:
```php
    if(!Application::$app->session::exists('user')) 
    {
        if(isset($_COOKIE["member_login"])) {
            $loginForm->userId = $_COOKIE["member_login"];
            $loginForm->login('userId');
            setcookie ("member_login", Application::$app->session->get('user'), time() + 3600 * 24 * 30);
        }
    }
```