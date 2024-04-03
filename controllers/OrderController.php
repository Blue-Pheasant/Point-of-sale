<?php
/*
    controllers/category/index.php
*/

namespace app\controllers;

use app\core\Controller;
use app\core\Input;
use app\core\Response;
use app\core\Session;
use app\core\Application;
use app\core\CartSession;
use app\core\Database;
use app\core\Request;
use app\models\Cart;
use app\models\CartItem;
use app\models\Product;
use app\models\Order;
use app\services\OrderService;

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct()
    {
        $this->orderService = new OrderService();
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
            Application::$app->response->redirect('/admin/orders');
        } 
    }

    public function reject(Request $request)
    {
        $orderId = Application::$app->request->getParam('id');
        $orderModel = $this->orderService->getOrderById($orderId);
        if($request->getMethod() === 'get') {
            $orderModel->setStatus('cancel');
            $orderModel->update();
            Application::$app->response->redirect('/admin/orders');
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
            if (strpos($path, 'reject')) {
                Application::$app->response->redirect('/admin/orders/rejected');
            } else Application::$app->response->redirect('/admin/orders/accepted');
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
        Application::$app->response->redirect('/orders');
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