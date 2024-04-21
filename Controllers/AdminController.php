<?php

namespace app\Controllers;

use app\Core\Controller;
use app\Core\Request;
use app\Middlewares\AdminMiddleware;
use app\Services\ProductService;
use app\Services\UserService;
use app\Services\OrderService;
use app\Auth\AuthUser;

/**
 * Class AdminController
 *
 * This class is responsible for handling the administrative operations of the application.
 * It extends the base Controller class and uses services for products, users, and orders.
 * It also uses middleware for administrative tasks.
 *
 * @package app\Controllers
 */
class AdminController extends Controller
{
    /**
     * @var ProductService $productService An instance of ProductService to handle product-related operations.
     */
    private ProductService $productService;

    /**
     * @var UserService $userService An instance of UserService to handle user-related operations.
     */
    private UserService $userService;

    /**
     * @var OrderService $orderService An instance of OrderService to handle order-related operations.
     */
    private OrderService $orderService;

    /**
     * AdminController constructor.
     *
     * Registers the middleware, initializes the services.
     */
    public function __construct()
    {
        $this->registerMiddleware(AdminMiddleware::class, ['index', 'profile']);
        $this->productService = new ProductService();
        $this->userService = new UserService();
        $this->orderService = new OrderService();
    }


    /**
     * Method index
     *
     * Fetches the total number of orders, products, users, and total income.
     * Sets the layout to 'admin' and renders the 'admin/dashboard' view with the fetched data.
     *
     * @return array|bool|string
     */
    public function index(): array|bool|string
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


    /**
     * Method profile
     *
     * Handles the profile update operation for the admin.
     * If the request method is 'post', it loads the request data into the admin model,
     * validates the data, and if valid, updates the profile.
     * Sets the layout to 'admin' and renders the 'admin/profile' view with the admin model.
     *
     * @param Request $request The request object containing the request data.
     * @return array|bool|string
     */
    public function profile(Request $request): array|bool|string
    {
        $adminModel = AuthUser::authUser();
        if($request->getMethod() === 'post') {
            $adminModel->loadData($request->getBody());
            if ($adminModel->validateUpdateProfile()) {
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