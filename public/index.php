<?php

use app\Core\Application;
use app\Models\User;
use app\Routes\ProductRoute;
use app\Routes\CategoryRoute;
use app\Routes\OrderRoute;
use app\Routes\UserRoute;
use app\Routes\StoreRoute;
use app\Routes\AdminRoute;
use app\Routes\AuthRoute;
use app\Routes\CustomerRoute;

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$config = [
    'userClass' => User::class,
    'db' => [
        'dsn' => $_ENV['DB_DSN'],
        'user' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD'],
    ]
];

$app = new Application(dirname(__DIR__), $config);

// customer
$app->useRoute(CustomerRoute::class);

// authentication
$app->useRoute(AuthRoute::class);

// admin general
$app->useRoute(AdminRoute::class);

// product
$app->useRoute(ProductRoute::class);

// category
$app->useRoute(CategoryRoute::class);

// store
$app->useRoute(StoreRoute::class);

// user
$app->useRoute(UserRoute::class);

// order
$app->useRoute(OrderRoute::class);

// Bootstrap the application
$app->bootstrap();