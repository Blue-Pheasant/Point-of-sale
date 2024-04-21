<?php
/*
    controllers/category/index.php
*/

namespace app\Controllers;

use app\Core\Controller;
use app\Core\Request;
use app\Models\Order;
use app\Services\OrderService;
use app\Middlewares\AuthMiddleware;
use app\Middlewares\AdminMiddleware;
use app\Auth\AuthUser;

/**
 * Class OrderController
 *
 * This class is responsible for handling the order operations of the application.
 * It extends the base Controller class and uses services for orders.
 * It also uses middleware for authentication and administrative tasks.
 *
 * @package app\Controllers
 */
class OrderController extends Controller
{
    /**
     * @var OrderService $orderService An instance of OrderService to handle order-related operations.
     */
    private OrderService $orderService;

    /**
     * OrderController constructor.
     *
     * Initializes the services and registers the middleware.
     */
    public function __construct()
    {
        $this->orderService = new OrderService();
        $this->registerMiddleware(AuthMiddleware::class, ['orderDetail', 'clear']);
        $this->registerMiddleware(
            AdminMiddleware::class, 
            ['index', 'accept', 'reject', 'accepted', 'rejected', 'delete', 'details', 'orderDetails']
        );
    }

    /**
     * Method index
     *
     * Fetches all orders with the status 'processing' and renders the 'orders' view with the fetched data.
     *
     * @return array|bool|string
     */
    public function index(): array|bool|string
    {
        $orders = Order::getAllOrders('processing');

        $this->setLayout('admin');
        return $this->render('/admin/orders/orders', [
            'orders' => $orders
        ]);
    }

    /**
     * Method orders
     *
     * Fetches the orders of the authenticated user and renders the 'orders' view with the fetched data.
     *
     * @return array|bool|string
     */
    public function orders(): array|bool|string
    {
        $userId = AuthUser::authUser()->id;
        $orders = $this->orderService->getOrderByUserId($userId);

        foreach($orders as $key => $order) {
            if($order->display == 'none') {
                unset($orders[$key]);
            }
        }

        return $this->render('orders', [
            'orders' => $orders,
        ]);
    }

    /**
     * Method accept
     *
     * Accepts an order by changing its status to 'done'.
     *
     * @param Request $request The request object containing the request data.
     * @return void
     */
    public function accept(Request $request): void
    {   
        $orderId = $request->getParam('id');
        $orderModel = $this->orderService->getOrderById($orderId);
        if($request->getMethod() === 'get') {
            $orderModel->setStatus('done');
            $orderModel->update();
            $this->back();
        } 
    }

    /**
     * Method reject
     *
     * Rejects an order by changing its status to 'cancel'.
     *
     * @param Request $request The request object containing the request data.
     * @return void
     */
    public function reject(Request $request): void
    {
        $orderId = $request->getParam('id');
        $orderModel = $this->orderService->getOrderById($orderId);
        if($request->getMethod() === 'get') {
            $orderModel->setStatus('cancel');
            $orderModel->update();
            $this->back();
        }
    }

    /**
     * Method accepted
     *
     * Fetches all orders with the status 'done' and renders the 'accept_orders' view with the fetched data.
     *
     * @return array|bool|string
     */
    public function accepted(): array|bool|string
    {   
        $orders = Order::getAllOrders('done');
        
        $this->setLayout('admin');
        return $this->render('/admin/orders/accept_orders', [
            'orders' => $orders
        ]);
    }

    /**
     * Method rejected
     *
     * Fetches all orders with the status 'cancel' and renders the 'reject_orders' view with the fetched data.
     *
     * @return array|bool|string
     */
    public function rejected(): array|bool|string
    {
        $orders = Order::getAllOrders('cancel');

        $this->setLayout('admin');
        return $this->render('/admin/orders/reject_orders', [
            'orders' => $orders
        ]);
    }

    /**
     * Method delete
     *
     * Deletes an order by changing its display status to 'none'.
     *
     * @param Request $request The request object containing the request data.
     * @return void
     */
    public function delete(Request $request): void
    {
        $orderId = $request->getParam('id');
        $orderModel = $this->orderService->getOrderById($orderId);
        if($request->getMethod() === 'get') {
            $orderModel->delete();
            $this->back();
        }
    }

    /**
     * Method details
     *
     * Fetches the order by ID and renders the 'details_order' view with the fetched data.
     *
     * @param Request $request The request object containing the request data.
     * @return array|bool|string
     */
    public function details(Request $request): array|bool|string
    {
        $orderId = $request->getParam('id');
        $orderModel = $this->orderService->getOrderById($orderId);

        $this->setLayout('admin');
        return $this->render('/admin/orders/details_order', [
            'orders' => $orderModel
        ]);
    }

    /**
     * Method clear
     *
     * Clears the orders with the status 'done' or 'cancel' from the user's order history.
     *
     * @return void
     */
    public function clear(): void
    {
        $userId = AuthUser::authUser()->id;
        $orders = $this->orderService->getOrderByUserId($userId);
        
        foreach($orders as $order) {
            if($order->getStatus() == 'done'|| $order->getStatus() == 'cancel') {
                $order->setDisplay('none');
                $order->update($order);
            }
        }

        // Refresh the page
        $this->refresh();
    }

    /**
     * Method orderDetail
     *
     * Fetches the order by ID and renders the 'order_detail' view with the fetched data.
     *
     * @param Request $request The request object containing the request data.
     * @return array|bool|string
     */
    public function orderDetail(Request $request): array|bool|string
    {
        $user = AuthUser::authUser();
        $orderId = $request->getParam('id');
        $order = $this->orderService->getOrderById($orderId);
        $items =$this->orderService->getOrderItemsByOrderId($orderId);

        return $this->render('order_detail', [
            'order' => $order,
            'user' => $user,
            'items' => $items
        ]);
    }

    /**
     * Method orderDetails
     *
     * Fetches the order items by order ID and renders the 'details' view with the fetched data.
     *
     * @param Request $request The request object containing the request data.
     * @return array|bool|string
     */
    public function orderDetails(Request $request): array|bool|string
    {
        $orderId = $request->getParam('id');
        $items = $this->orderService->getOrderItemsByOrderId($orderId);

        $this->setLayout('admin');
        return $this->render('/admin/orders/details', [
            'model' => $items
        ]);
    }
}