<?php

use app\controllers\SiteController;
use app\core\Application;
use app\controllers\AuthController;
use app\controllers\AboutController;

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$config = [
    'userClass' => \app\models\User::class,
    'db' => [
        'dsn' => $_ENV['DB_DSN'],
        'user' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD'],
    ]
];

$app = new Application(dirname(__DIR__), $config);

$app->router->get('/', [SiteController::class, 'home']);
$app->router->get('/register', [SiteController::class, 'register']);
$app->router->post('/register', [SiteController::class, 'register']);
$app->router->get('/login', [SiteController::class, 'login']);
$app->router->post('/login', [SiteController::class, 'login']);
$app->router->get('/logout', [SiteController::class, 'logout']);
$app->router->get('/contact', [SiteController::class, 'contact']);
$app->router->get('/about', [AboutController::class, 'index']);
$app->router->get('/stores', [SiteController::class, 'stores']);
$app->router->get('/menu', [SiteController::class, 'menu']);
$app->router->get('/collection', [SiteController::class, 'collection']);
$app->router->get('/profile', [SiteController::class, 'profile']);
$app->router->get('/product', [SiteController::class, 'product']);
$app->router->get('/cart', [SiteController::class, 'cart']);

$app->run();