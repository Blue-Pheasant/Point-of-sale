<?php

use app\controllers\SiteController;
use app\core\Application;
use app\controllers\ProductController;
use app\controllers\MenuController;
use app\controllers\AdminController;
use app\controllers\StoreController;
use app\controllers\UserController;
use app\controllers\CategoryController;
use app\controllers\SaleController;
use app\controllers\CartController;
use app\controllers\OrderController;
use app\controllers\AuthController;

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
$app->router->get('/register', [AuthController::class, 'register']);
$app->router->post('/register', [AuthController::class, 'register']);
$app->router->get('/login', [AuthController::class, 'login']);
$app->router->post('/login', [AuthController::class, 'login']);
$app->router->get('/logout', [AuthController::class, 'logout']);
$app->router->get('/contact', [SiteController::class, 'contact']);
$app->router->get('/about', [SiteController::class, 'about']);
$app->router->get('/stores', [StoreController::class, 'stores']);
$app->router->get('/menu', [MenuController::class, 'menu']);
$app->router->post('/menu', [MenuController::class, 'search']);
$app->router->get('/profile', [UserController::class, 'profile']);
$app->router->post('/profile', [UserController::class, 'updateProfile']);
$app->router->get('/stores', [SiteController::class, 'stores']);
$app->router->get('/cart/notice', [SiteController::class, 'notice']);

$app->router->get('/product', [ProductController::class, 'product']);
$app->router->post('/product', [ProductController::class, 'product']);

$app->router->get('/cart', [CartController::class, 'cart']);
$app->router->post('/cart', [CartController::class, 'placeOrder']);

$app->router->post('/update', [CartController::class, 'update']);

$app->router->get('/orders', [OrderController::class, 'orders']);
$app->router->post('/orders/clear', [OrderController::class, 'clear']);
$app->router->get('/error', [SiteController::class, 'error']);
$app->router->get('/order', [OrderController::class, 'orderDetail']);

// admin general
$app->router->get('/admin', [AdminController::class, 'index']);
$app->router->get('/admin/sales', [SaleController::class, 'index']);
$app->router->get('/admin/users', [UserController::class, 'index']);
$app->router->get('/admin/products', [ProductController::class, 'index']);
$app->router->get('/admin/stores', [StoreController::class, 'index']);
$app->router->get('/admin/categories', [CategoryController::class, 'index']);
$app->router->get('/admin/profile', [AdminController::class, 'profile']);
$app->router->post('/admin/profile', [AdminController::class, 'profile']);
$app->router->post('/admin/sales', [AdminController::class, 'index']);
$app->router->get('/admin/orders', [OrderController::class, 'index']);
// product
$app->router->get('/admin/products/delete', [ProductController::class, 'delete']);
$app->router->get('/admin/products/edit', [ProductController::class, 'update']);
$app->router->get('/admin/products/create', [ProductController::class, 'create']);
$app->router->get('/admin/products/details', [ProductController::class, 'details']);

$app->router->post('/admin/products/delete', [ProductController::class, 'delete']);
$app->router->post('/admin/products/edit', [ProductController::class, 'update']);
$app->router->post('/admin/products/create', [ProductController::class, 'create']);
$app->router->post('/admin/products/details', [ProductController::class, 'details']);
// category
$app->router->get('/admin/categories/delete', [CategoryController::class, 'delete']);
$app->router->get('/admin/categories/edit', [CategoryController::class, 'update']);
$app->router->get('/admin/categories/create', [CategoryController::class, 'create']);
$app->router->get('/admin/categories/details', [CategoryController::class, 'details']);

$app->router->post('/admin/categories/delete', [CategoryController::class, 'delete']);
$app->router->post('/admin/categories/edit', [CategoryController::class, 'update']);
$app->router->post('/admin/categories/create', [CategoryController::class, 'create']);
$app->router->post('/admin/categories/details', [CategoryController::class, 'details']);
// store
$app->router->get('/admin/stores/delete', [StoreController::class, 'delete']);
$app->router->get('/admin/stores/edit', [StoreController::class, 'update']);
$app->router->get('/admin/stores/add', [StoreController::class, 'add']);
$app->router->get('/admin/stores/details', [StoreController::class, 'details']);

$app->router->post('/admin/stores/delete', [StoreController::class, 'delete']);
$app->router->post('/admin/stores/edit', [StoreController::class, 'update']);
$app->router->post('/admin/stores/add', [StoreController::class, 'add']);
$app->router->post('/admin/stores/details', [StoreController::class, 'details']);
// user
$app->router->get('/admin/users/delete', [UserController::class, 'delete']);
$app->router->get('/admin/users/edit', [UserController::class, 'update']);
$app->router->get('/admin/users/create', [UserController::class, 'create']);
$app->router->get('/admin/users/details', [UserController::class, 'details']);
$app->router->get('/admin/users/edit/password', [UserController::class, 'password']);

$app->router->post('/admin/users/delete', [UserController::class, 'delete']);
$app->router->post('/admin/users/edit', [UserController::class, 'update']);
$app->router->post('/admin/users/create', [UserController::class, 'create']);
$app->router->post('/admin/users/details', [UserController::class, 'details']);
$app->router->post('/admin/users/edit/password', [UserController::class, 'password']);
// sale
$app->router->get('/admin/sales/delete', [SaleController::class, 'delete']);
$app->router->get('/admin/sales/details', [SaleController::class, 'details']);

$app->router->post('/admin/sales/delete', [SaleController::class, 'delete']);
$app->router->post('/admin/sales/details', [SaleController::class, 'details']);
// order
$app->router->get('/admin/orders/accept', [OrderController::class, 'accept']);
$app->router->get('/admin/orders/reject', [OrderController::class, 'reject']);
$app->router->get('/admin/orders/details', [OrderController::class, 'orderDetails']);

$app->router->get('/admin/orders/accepted', [OrderController::class, 'accepted']);
$app->router->get('/admin/orders/rejected', [OrderController::class, 'rejected']);

$app->router->get('/admin/orders/rejected/delete', [OrderController::class, 'delete']);
$app->router->get('/admin/orders/rejected/details', [OrderController::class, 'details']);

$app->router->get('/admin/orders/accepted/delete', [OrderController::class, 'delete']);
$app->router->get('/admin/orders/accepted/details', [OrderController::class, 'details']);

$app->router->post('/admin/orders/accepted', [OrderController::class, 'accepted']);
$app->router->post('/admin/orders/rejected', [OrderController::class, 'rejected']);
$app->run();