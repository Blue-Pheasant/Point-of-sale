<?php

namespace app\Controllers;

use app\Core\Application;
use app\Core\Controller;
use app\Core\Request;
use app\Core\Response;
use app\Middlewares\AdminMiddleware;
use app\Models\Order;
use app\Models\Product;
use app\Models\User;
use app\Services\ProductService;
use app\Services\UserService;
use app\Services\OrderService;
use app\Auth\AuthUser;


class AdminController extends Controller
{
    private ProductService $productService;
    private UserService $userService;
    private OrderService $orderService;

    public function __construct() 
    {
        $this->registerMiddleware(AdminMiddleware::class, ['index', 'profile']);
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
        $adminModel = AuthUser::authUser();
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