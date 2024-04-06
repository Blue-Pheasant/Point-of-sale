<?php
/*
    controllers/category/index.php
*/

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Input;
use App\Core\Response;
use App\Core\Session;
use App\Core\Application;
use App\Core\CartSession;
use App\Core\Database;
use App\Core\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Order;
use App\Services\OrderService;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\AdminMiddleware;

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct()
    {
        $this->orderService = new OrderService();
        $this->registerMiddleware(AuthMiddleware::class, ['orderDetail', 'clear']);
        $this->registerMiddleware(AdminMiddleware::class, ['index', 'accept', 'reject', 'accepted', 'rejected', 'delete', 'details', 'orderDetails']);
    }

    public function index()
    {
        $orders = Order::getAllOrders('processing');

        $this->setLayout('admin');
        return $this->render('/admin/orders/orders',[
            'orders' => $orders
        ]);
    }

    public function orders()
    {
        $userId = Application::$app->user->id;
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

    public function accept(Request $request)
    {   
        $orderId = $request->getParam('id');
        $orderModel = $this->orderService->getOrderById($orderId);
        if($request->getMethod() === 'get') {
            $orderModel->setStatus('done');
            $orderModel->update();
            $this->back();
        } 
    }

    public function reject(Request $request)
    {
        $orderId = Application::$app->request->getParam('id');
        $orderModel = $this->orderService->getOrderById($orderId);
        if($request->getMethod() === 'get') {
            $orderModel->setStatus('cancel');
            $orderModel->update();
            $this->back();
        }
    }

    public function accepted()
    {   
        $orders = Order::getAllOrders('done');
        
        $this->setLayout('admin');
        return $this->render('/admin/orders/accept_orders',[
            'orders' => $orders
        ]);
    }

    public function rejected()
    {
        $orders = Order::getAllOrders('cancel');

        $this->setLayout('admin');
        return $this->render('/admin/orders/reject_orders',[
            'orders' => $orders
        ]);
    }

    public function delete(Request $request)
    {
        $path = Application::$app->request->getPath();
        $orderId = Application::$app->request->getParam('id');
        $orderModel = $this->orderService->getOrderById($orderId);
        if($request->getMethod() === 'get') {
            $orderModel->delete();
            $this->back();
        }
    }

    public function details()
    {
        $orderId = Application::$app->request->getParam('id');
        $orderModel = $this->orderService->getOrderById($orderId);

        $this->setLayout('admin');
        return $this->render('/admin/orders/details_order',[
            'orders' => $orderModel
        ]);
    }

    public function clear()
    {
        $userId = Application::$app->user->id;
        $orders = $this->orderService->getOrderByUserId($userId);
        
        foreach($orders as $order) {
            if($order->getStatus() == 'done'|| $order->getStatus() == 'cancel') {
                $order->setDisplay('none');
                $order->update($order);
            }
        }
        return $this->refresh();
    }

    public function orderDetail(Request $request)
    {
        $user = Application::$app->user;
        $orderId = $request->getParam('id');
        $order = $this->orderService->getOrderById($orderId);
        $items =$this->orderService->getOrderItemsByOrderId($orderId);

        return $this->render('order_detail', [
            'order' => $order,
            'user' => $user,
            'items' => $items
        ]);
    }

    public function orderDetails(Request $request)
    {
        $orderId = $request->getParam('id');
        $items = $this->orderService->getOrderItemsByOrderId($orderId);

        $this->setLayout('admin');
        return $this->render('/admin/orders/details', [
            'model' => $items
        ]);
    }
}