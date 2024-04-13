<?php
/*
    controllers/category/index.php
*/

namespace app\Controllers;

use app\Core\Controller;
use app\Core\Application;
use app\Core\Session;
use app\Models\Cart;
use app\Models\CartItem;
use app\Models\Order;
use app\Models\OrderDetail;
use app\Core\Request;
use app\Core\Response;
use app\Services\CartService;
use app\Middlewares\AuthMiddleware;
use app\Auth\AuthUser;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct()
    {
        $this->cartService = new CartService();
        $this->registerMiddleware(AuthMiddleware::class, ['cart', 'update', 'placeOrder']);
    }

    public function deleteItem($cartId, $id)
    {
        CartItem::deleteItem($id, $cartId);
    }

    public function cart(Request $request)
    {
        $cartId = Session::get('cart_id');
        $deletedItem = false;
        $updatedItem = false;

        $action = $request->getParam('action');
        if ($action) {
            $id = $request->getParam('id');
            if ($action == 'delete') {
                $this->deleteItem($cartId, $id);
                $deletedItem = true;
            } else if($action == 'deletemenu') {
                $this->deleteItem($cartId, $id);
                $deletedItem = true;
                $this->redirect('menu');
            }
        }

        $user = AuthUser::authUser();
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
        $cartId = Session::get('cart_id');
        $user = AuthUser::authUser();

        // Get the order information
        $id = $request->getParam('cart_item_id');
        $newNote = $request->getBody()['note'];
        $newQuantity = $request->getBody()['quantity'];

        $cartDetailModel = $this->cartService->getCartItem($id);
        $cartDetailModel->note = $newNote;
        $cartDetailModel->quantity = $newQuantity;

        if ($cartDetailModel->validate()) {
            $cartDetailModel->update();
        } else {
            $this->setFlash('fail', 'Số lượng đặt hàng phải lớn hơn 0');
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

        $cartId = Session::get('cart_id');
        $items = $this->cartService->getCartItems($cartId);

        $userId = AuthUser::authUser()->id;
        $deliveryName = $request->getBody()['name'];
        $deliveryPhone = $request->getBody()['phone_number'];
        $deliveryAddress = $request->getBody()['address'];
        $paymentMethod = $request->getBody()['payment_method'];

        // Create order
        $order = new Order([
            'id' => uniqid(), 
            'user_id' => $userId, 
            'payment_method' => $paymentMethod, 
            'status' => 'processing', 
            'delivery_name' => $deliveryName, 
            'delivery_phone' => $deliveryPhone, 
            'delivery_address' => $deliveryAddress
        ]);

        // Save order
        $order->save();

        // Create order details
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

        // Delete cart items
        foreach ($items as $item) {
            $this->deleteItem($cartId, $item->cart_item_id);
        }

        // Checkout cart
        $this->cartService->checkoutCart($cartId);

        return $this->redirect('/cart/notice');
    }
}