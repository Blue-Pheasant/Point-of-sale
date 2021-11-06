<!--
    controllers/cart/checkout.php
-->
<?php
//require('admin/views/shared/header.php');
if (!empty($_POST)) {
    $order = array(
        'id' => 0,
        'customer_id' => intval($_POST['customer_id']),
        'address' => escape($_POST['address']),
        'cart_total' => $_POST['cart_total'],
        'message' => escape($_POST['message']),
        'create_at' => gmdate('Y-m-d H:i:s', time() + 7 * 3600),
    );
    $order_id = save('orders', $order);

    $cart = cart_list();
    //Get item in session cart
    foreach ($cart as $product) {
        $order_detail = array(
            'id' => 0,
            'order_id' => $order_id,
            'product_id' => $product['id'],
            'quantity' => $product['quantity'],
            'price' => $product['price']
        );
        save('order_detail', $order_detail);
    }
    cart_destroy(); //delete cart after save order in db
    global $user_nav;
    if (isset($user_nav)) detroy_cart_user_db(); //delete cart synchronously on db after order
    $title = 'Order Success';
    header("refresh:15;url=" . PATH_URL . "home");
    echo '<div style="text-align: center;padding: 20px 10px;">Order Success</div><div style="text-align: center;padding: 20px 10px;">Thank you for ordering from our store.<br>
                    The browser will automatically return to the homepage after 15 seconds, or you can click <a href="' . PATH_URL . 'home">Click here</a>.</div>';
} else {
    header('location:.');
}
