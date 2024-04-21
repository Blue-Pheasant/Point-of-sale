<?php
/*
    controllers/category/index.php
*/

namespace app\Controllers;

use app\Core\Controller;
use app\Core\Session;
use app\Models\CartItem;
use app\Models\Order;
use app\Models\OrderDetail;
use app\Core\Request;
use app\Services\CartService;
use app\Middlewares\AuthMiddleware;
use app\Auth\AuthUser;

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
            'updatedItem' => false
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