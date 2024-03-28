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
use app\models\Record;

class OrdersController extends Controller
{
    public function orders()
    {
        $userId = Application::$app->user->id;
        $orders = Order::getOrders($userId);

        foreach($orders as $key => $order) {
            if($order->getDisplay() == 'none') {
                unset($orders[$key]);
            }
        }

        return $this->render('orders', [
            'orders' => $orders,
        ]);
    }

    public function index()
    {
        $orders = Order::getAllOrders('processing');

        $this->setLayout('admin');
        return $this->render('/admin/orders/orders',[
            'orders' => $orders
        ]);
    }

    public function accept(Request $request)
    {   
        $orderId = Application::$app->request->getParam('id');
        $orderModel = Order::getOrderById($orderId);
        if($request->getMethod() === 'get') {
            $orderModel->setStatus('done');
            $orderModel->update($orderModel);
            Application::$app->response->redirect('/admin/orders');
        } 
    }

    public function reject(Request $request)
    {
        $orderId = Application::$app->request->getParam('id');
        $orderModel = Order::getOrderById($orderId);
        if($request->getMethod() === 'get') {
            $orderModel->setStatus('cancel');
            $orderModel->update($orderModel);
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
        $orderModel = Order::getOrderById($orderId);
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
        $orderModel = Order::getOrderById($orderId);

        $this->setLayout('admin');
        return $this->render('/admin/orders/details_order',[
            'orders' => $orderModel
        ]);
    }

    public function clear()
    {
        $userId = Application::$app->user->id;
        $orders = Order::getOrders($userId);
        
        foreach($orders as $order) {
            if($order->getStatus() == 'done'|| $order->getStatus() == 'cancel') {
                $order->setDisplay('none');
                $order->update($order);
            }
        }
        Application::$app->response->redirect('orders');
    }
}