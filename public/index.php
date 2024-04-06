<?php

use App\Controllers\SiteController;
use App\Core\Application;
use App\Routes\ProductRoute;
use App\Routes\CategoryRoute;
use App\Routes\OrderRoute;
use App\Routes\UserRoute;
use App\Routes\StoreRoute;
use App\Routes\AdminRoute;
use App\Routes\AuthRoute;
use App\Routes\CustomerRoute;

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$config = [
    'userClass' => \App\Models\User::class,
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