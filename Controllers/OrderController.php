<?php
/*
    controllers/category/index.php
*/

namespace app\Controllers;

use app\Core\Controller;
use app\Core\Input;
use app\Core\Response;
use app\Core\Session;
use app\Core\Application;
use app\Core\CartSession;
use app\Core\Database;
use app\Core\Request;
use app\Models\Cart;
use app\Models\CartItem;
use app\Models\Product;
use app\Models\Order;
use app\Services\OrderService;
use app\Middlewares\AuthMiddleware;
use app\Middlewares\AdminMiddleware;
use app\Auth\AuthUser;

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct()
    {
        $this->orderService = new OrderService();
        $this->registerMiddleware(AuthMiddleware::class, ['orderDetail', 'clear']);
        $this->registerMiddleware(
            AdminMiddleware::class, 
            ['index', 'accept', 'reject', 'accepted', 'rejected', 'delete', 'details', 'orderDetails'], 
            AuthRequest::class
        );
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
        $orderId = $request->getParam('id');
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
        $orderId = $request->getParam('id');
        $orderModel = $this->orderService->getOrderById($orderId);
        if($request->getMethod() === 'get') {
            $orderModel->delete();
            $this->back();
        }
    }

    public function details()
    {
        $orderId = $request->getParam('id');
        $orderModel = $this->orderService->getOrderById($orderId);

        $this->setLayout('admin');
        return $this->render('/admin/orders/details_order',[
            'orders' => $orderModel
        ]);
    }

    public function clear()
    {
        $userId = AuthUser::authUser()->id;
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