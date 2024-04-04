<?php

use app\controllers\SiteController;
use app\core\Application;
use app\routes\ProductRoute;
use app\routes\CategoryRoute;
use app\routes\OrderRoute;
use app\routes\UserRoute;
use app\routes\StoreRoute;
use app\routes\AdminRoute;
use app\routes\AuthRoute;
use app\routes\CustomerRoute;

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