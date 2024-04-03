<?php
/*
    controllers/category/index.php
*/

namespace app\controllers;

use app\core\Controller;
use app\core\Application;
use app\core\Session;
use app\models\Cart;
use app\models\CartItem;
use app\models\Order;
use app\models\OrderDetail;
use app\core\Request;
use app\core\Response;
use app\services\CartService;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct()
    {
        $this->cartService = new CartService();
    }

    public function deleteItem($cart_id, $id)
    {
        CartItem::deleteItem($id, $cart_id);
    }

    public function cart(Request $request)
    {
        $cartId = Application::$app->cart->id;
        $deletedItem = false;
        $updatedItem = false;

        if (isset($_GET['action'])) {
            $id = $request->getParam('id');
            if ($_GET['action'] == 'delete') {
                $this->deleteItem($cartId, $id);
                $deletedItem = true;
            } else if($_GET['action'] == 'deletemenu') {
                $this->deleteItem($cartId, $id);
                $deletedItem = true;
                $this->redirect('menu');
            }
        }

        $user = Application::$app->user;
        $items = $this->cartService->getCartItems($cartId);

        return $this->render('cart', [
            'items' => $items,
            'user' => $user,
            'deletedItem' => $deletedItem,
            'updatedItem' => $updatedItem
        ]);
    }

    public function update(Request $request)
    {
        $cartId = Application::$app->cart->id;
        $user = Application::$app->user;

        $id = $request->getParam('cart_item_id');
        $newNote = $request->getBody()['note'];
        $newQuantity = $request->getBody()['quantity'];

        $cartDetailModel = $this->cartService->getCartItem($id);
        $cartDetailModel->note = $newNote;
        $cartDetailModel->quantity = $newQuantity;

        if ($cartDetailModel->validate()) {
            $cartDetailModel->update();
        } else {
            Session::setFlash('fail', 'Số lượng đặt hàng phải lớn hơn 0');
        }

        $items = $this->cartService->getCartItems($cartId);

        return $this->render('cart', [
            'items' => $items,
            'user' => $user,
        ]);
    }

    public function placeOrder(Request $request)
    {
        $placedOrder = false;
        $updatedItem = false;

        $cartId = Application::$app->cart->id;
        $items = $this->cartService->getCartItems($cartId);

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
            $this->deleteItem($cartId, $item->cart_item_id);
        }

        $this->cartService->checkoutCart($cartId);

        Response::redirect('/cart/notice');
    }
}