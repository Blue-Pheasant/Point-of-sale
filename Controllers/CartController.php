<?php

/*
    controllers/category/index.php
*/

namespace app\Controllers;

use app\Auth\AuthUser;
use app\Core\Controller;
use app\Core\Request;
use app\Core\Session;
use app\Middlewares\AuthMiddleware;
use app\Models\CartItem;
use app\Models\Order;
use app\Models\OrderDetail;
use app\Services\CartService;

/**
 * Class CartController
 *
 * This class is responsible for handling the cart operations of the application.
 * It extends the base Controller class and uses services for cart and authentication.
 *
 * @package app\Controllers
 */
class CartController extends Controller
{
    /**
     * @var CartService $cartService An instance of CartService to handle cart-related operations.
     */
    protected CartService $cartService;

    /**
     * CartController constructor.
     *
     * Initializes the services and registers the middleware.
     */
    public function __construct()
    {
        $this->cartService = new CartService();
        $this->registerMiddleware(AuthMiddleware::class, ['cart', 'update', 'placeOrder']);
    }

    /**
     * Method deleteItem
     *
     * Deletes an item from the cart.
     *
     * @param string $id The ID of the item to be deleted.
     * @return void
     */
    public function deleteItem(string $id): void
    {
        CartItem::deleteItem($id);
    }

    /**
     * Method cart
     *
     * Fetches the cart items and renders the 'cart' view with the fetched data.
     * If an action is provided in the request, it performs the action on the cart.
     *
     * @param Request $request The request object containing the request data.
     * @return array|bool|string
     */
    public function cart(Request $request): array|bool|string
    {
        $cartId = Session::get('cart_id');
        $deletedItem = false;
        $action = $request->getParam('action');
        if ($action) {
            $id = $request->getParam('id');
            if ($action == 'delete') {
                $this->deleteItem($cartId, $id);
                $deletedItem = true;
            } elseif ($action == 'deletemenu') {
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
            'updatedItem' => false,
        ]);
    }

    /**
     * Method update
     *
     * Updates the quantity and note of a cart item.
     * Renders the 'cart' view with the updated cart items.
     *
     * @param Request $request The request object containing the request data.
     * @return array|bool|string
     */
    public function update(Request $request): array|bool|string
    {
        $cartId = Session::get('cart_id');
        $user = AuthUser::authUser();

        // Get the order information
        $body = $request->getBody();
        $id = $request->getParam('cart_item_id');
        $newNote = $body['note'] ?? '';
        $newQuantity = (int) ($body['quantity'] ?? 0);

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

    /**
     * Method placeOrder
     *
     * Places an order with the items in the cart.
     * Creates an order and order details, and deletes the cart items.
     * Redirects to the 'cart/notice' view after placing the order.
     *
     * @param Request $request The request object containing the request data.
     */
    public function placeOrder(Request $request)
    {
        $cartId = Session::get('cart_id');
        $items = $this->cartService->getCartItems($cartId);

        $body = $request->getBody();
        $userId = AuthUser::authUser()->id;
        $deliveryName = $body['name'] ?? '';
        $deliveryPhone = $body['phone_number'] ?? '';
        $deliveryAddress = $body['address'] ?? '';
        $paymentMethod = $body['payment_method'] ?? '';

        // Create order
        $order = new Order([
            'user_id' => $userId,
            'payment_method' => $paymentMethod,
            'status' => Order::STATUS_PROCESSING,
            'delivery_name' => $deliveryName,
            'delivery_phone' => $deliveryPhone,
            'delivery_address' => $deliveryAddress,
        ]);

        // Reject incomplete delivery info instead of saving a broken order.
        if (!$order->validate()) {
            $this->setFlash('fail', 'Vui lòng nhập đầy đủ thông tin giao hàng hợp lệ.');
            return $this->redirect('/cart');
        }

        // Save order
        $order->save();

        // Create order details
        foreach ($items as $item) {
            $orderDetail = new OrderDetail([
                'product_id' => $item->product_id,
                'order_id' => $order->id,
                'quantity' => $item->quantity,
                'note' => $item->note,
                'size' => $item->size,
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
