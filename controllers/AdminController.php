<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\middlewares\AdminMiddleware;
use app\middlewares\AuthMiddleware;
use app\models\Order;
use app\models\Product;
use app\models\User;


class AdminController extends Controller
{
    public function __construct() 
    {
        Application::$app->controller->registerMiddleware(new AdminMiddleware(['index']));
    }

    public function index()
    {
        $orders = Order::getAllOrders('done');
        $products = Product::getAllProducts();
        $users = User::getAllUsers();
        $list = Order::getTotalPrice();

        $this->setLayout('admin');
        return $this->render('/admin/dashboard', [
            'orders' => $orders,
            'products' => $products,
            'users' => $users,
            'list' => $list 
        ]);
    }

    public function profile(Request $request)
    {
        $adminId = Application::$app->user->id;
        $adminModel = User::getUserInfo($adminId);
        if($request->getMethod() === 'post') {
            $adminModel->loadData($request->getBody());
            if ($adminModel->validateUpdateProfile() && true) {
                if ($adminModel->updateProfile($adminModel)) {
                    Application::$app->response->redirect('/admin/profile');
                    return 'Show success page';
                }
            }
        }

        $this->setLayout('admin');
        return $this->render('/admin/profile', [
            'user' => $adminModel
        ]);
    }
}