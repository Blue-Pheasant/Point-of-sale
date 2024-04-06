<?php

namespace App\Controllers;

use App\Core\Application;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Middlewares\AdminMiddleware;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\ProductService;
use App\Services\UserService;
use App\Services\OrderService;


class AdminController extends Controller
{
    private ProductService $productService;
    private UserService $userService;
    private OrderService $orderService;

    public function __construct() 
    {
        Application::$app->controller->registerMiddleware(AdminMiddleware::class, ['index', 'profile']);
        $this->productService = new ProductService();
        $this->userService = new UserService();
        $this->orderService = new OrderService();
    }

    public function index()
    {
        $orders = $this->orderService->getTotalOrderNumber();
        $products = $this->productService->getProductNumber();
        $users = $this->userService->getTotalUserNumber();
        $income = $this->orderService->getTotalIncome(); 

        $this->setLayout('admin');
        return $this->render('/admin/dashboard', [
            'orders' => $orders,
            'products' => $products,
            'users' => $users,
            'income' => $income 
        ]);
    }

    public function profile(Request $request)
    {
        $adminId = Application::$app->user->id;
        $adminModel = $this->userService->getUserById($adminId);
        if($request->getMethod() === 'post') {
            $adminModel->loadData($request->getBody());
            if ($adminModel->validateUpdateProfile() && true) {
                if ($adminModel->updateProfile($adminModel)) {
                    $this->refresh();
                }
            }
        }

        $this->setLayout('admin');
        return $this->render('/admin/profile', [
            'user' => $adminModel
        ]);
    }
}