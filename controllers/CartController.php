<?php
/*
    controllers/category/index.php
*/

namespace app\controllers;

use app\core\Controller;
use app\core\Application;
use app\models\Cart;
use app\models\CartItem;
use app\models\Order;
use app\models\OrderDetail;
use app\core\Request;

class CartController extends Controller
{
    public function deleteItem($cart_id, $id)
    {
        CartItem::deleteItem($id, $cart_id);
    }

    public function cart()
    {
        $cart_id = Application::$app->cart->id;
        $deletedItem = false;
        $updatedItem = false;

        if (isset($_GET['action'])) {
            $id = Application::$app->request->getParam('id');
            if ($_GET['action'] == 'delete') {
                $this->deleteItem($cart_id, $id);
                $deletedItem = true;
            } else if($_GET['action'] == 'deletemenu') {
                $this->deleteItem($cart_id, $id);
                $deletedItem = true;
                Application::$app->response->redirect('menu');
            }
        }

        $user = Application::$app->user;
        $items = CartItem::getCartItems($cart_id);

        return $this->render('cart', [
            'items' => $items,
            'user' => $user,
            'deletedItem' => $deletedItem,
            'updatedItem' => $updatedItem
        ]);
    }

    public function update()
    {
        $cart_id = Application::$app->cart->id;
        $user = Application::$app->user;

        $id = Application::$app->request->getParam('cart_item_id');
        $newNote = Application::$app->request->getBody()['note'];
        $newQuantity = Application::$app->request->getBody()['quantity'];

        $cartDetailModel = CartItem::getCartItem($id);
        $cartDetailModel->note = $newNote;
        $cartDetailModel->quantity = $newQuantity;

        if ($cartDetailModel->validate()) {
            $cartDetailModel->update();
        } else {
            Application::$app->session->setFlash('fail', 'Số lượng đặt hàng phải lớn hơn 0');
        }

        $items = CartItem::getCartItems($cart_id);

        return $this->render('cart', [
            'items' => $items,
            'user' => $user,
        ]);
    }

    public function placeOrder(Request $request)
    {
        $placedOrder = false;
        $updatedItem = false;

        $cart_id = Application::$app->cart->id;
        $items = CartItem::getCartItems($cart_id);

        $user_id = Application::$app->user->id;
        $delivery_name = $request->getBody()['name'];
        $delivery_phone = $request->getBody()['phone_number'];
        $delivery_address = $request->getBody()['address'];
        $payment_method = $request->getBody()['payment_method'];
        $order = new Order([
            'id' => uniqid(), 
            'user_id' => $user_id, 
            'payment_method' => $payment_method, 
            'status' => 'processing', 
            'delivery_name' => $delivery_name, 
            'delivery_phone' => $delivery_phone, 
            'delivery_address' => $delivery_address
        ]);
        $order->save();

        foreach ($items as $item) {
            $orderDetail = new OrderDetail([
                'id' => uniqid(),
                'product_id' => $item->product_id,
                'order_id' => $order->id,
                'quantity' => $item->quantity,
                'note' => $item->note,
                'size' => $item->size
            ]);
            $orderDetail->save();
        }

        foreach ($items as $item) {
            $this->deleteItem($cart_id, $item->cart_item_id);
        }

        Cart::checkoutCart($cart_id);

        Application::$app->response->redirect('/cart/notice');
    }
}