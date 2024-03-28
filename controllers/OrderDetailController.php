<?php
/*
    controllers/category/index.php
*/

namespace app\controllers;

use app\core\Controller;
use app\core\Application;
use app\models\CartDetail;
use app\models\CartItem;
use app\models\OrderItem;
use app\models\Order;
use app\models\Record;

class OrderDetailController extends Controller
{
    public function orderDetail()
    {
        $user = Application::$app->user;
        $order_id = Application::$app->request->getParam('id');
        $order = Order::getOrderById($order_id);
        $items = OrderItem::getOrderItem($order_id);

        return $this->render('order_detail', [
            'order' => $order,
            'user' => $user,
            'items' => $items
        ]);
    }

    public function details()
    {
        $order_id = Application::$app->request->getParam('id');
        $items = OrderItem::getOrderItem($order_id);

        $this->setLayout('admin');
        return $this->render('/admin/orders/details', [
            'model' => $items
        ]);
    }
}