<!--
    models/card.php
-->
<?php
//Initiate cart
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = array();
//Add cart
function cart_add($product_id, $number)
{
    if (isset($_SESSION['cart'][$product_id])) {
        //If you already have products in your cart, add $number
        $_SESSION['cart'][$product_id]['number'] += $number;
    } else {
        //Get item's data from db and save it into product
        $product = get_a_record('products', $product_id);

        $_SESSION['cart'][$product_id] = array(
            'id' => $product_id,
            'name' => $product['product_name'],
            'type' => $product['product_type'],
            'price' => $product['product_price'],
            'number' => $number,
            'image' => $product['img']
        );
    }
}
//Update the cart to the user and from db and from the user to the cart
function update_cart()
{
    global $user_nav, $linkconnectDB;
    if (isset($user_nav)) {
        $option = array(
            'order_by' => 'id asc',
            'where' => 'user_id=' . $user_nav
        );
        $product_of_user = get_all('cart_user', $option);
        if (!empty($product_of_user)) {
            foreach ($product_of_user as $product) {
                if (isset($_SESSION['cart'][$product['product_id']]) && mysqli_num_rows(mysqli_query($linkconnectDB, "SELECT product_id FROM cart_user WHERE product_id=" . $product['product_id']  . "")) == 1) {
                    //If you already have products in your cart, add $number
                    $_SESSION['cart'][$product['product_id']]['number'] += $product['number'];
                } else {
                    //Get product information from database and save to cart
                    $info_product = get_a_record('products', $product['product_id']);
                    $_SESSION['cart'][$product['product_id']] = array(
                        'id' => $product['product_id'],
                        'name' => $info_product['product_name'],
                        'type' => $info_product['product_type'],
                        'price' => $info_product['product_price'],
                        'number' => $product['number'],
                        'image' => $info_product['img']
                    );
                }
            }
        }
        //use in file login.php
    }
}
//Sync products between session and db when user add to cart
function update_cart_user_db()
{
    global $user_nav, $linkconnectDB;
    //get products in cart
    $cart = cart_list();

    //if row > 0, ie user already has sp on db
    if (mysqli_num_rows(mysqli_query($linkconnectDB, "SELECT * FROM cart_user WHERE user_id=" . $user_nav . "")) > 0) {
        foreach ($cart as $product_cart) {
            $option_cart_user = array(
                'order_by' => 'id',
                'where' => 'user_id=' . $user_nav
            );
            //Loop the cart_user array
            $cart_users = get_all('cart_user', $option_cart_user);
            foreach ($cart_users as $cart_user) {
                if ($cart_user['product_id'] == $product_cart['id']) {
                    $status = 1;
                    break;
                } else $status = 0;
            }

            if ($status == 1) { //if the product in this cart is already on db -> edit
                $cart_user = array(
                    'id' => $cart_user['id'],
                    'product_id' => $product_cart['id'],
                    'number' => $product_cart['number'],
                );
                save('cart_user', $cart_user);
            } elseif ($status == 0) { //if the product in this cart is not on db -> add
                $cart_user = array(
                    'id' => 0,
                    'user_id' => $user_nav,
                    'product_id' => $product_cart['id'],
                    'number' => $product_cart['number'],
                );
                save('cart_user', $cart_user);
            }
        }
    } else {
        foreach ($cart as $product_cart) {
            $up_cart_user = array(
                'id' => 0,
                'user_id' => $user_nav,
                'product_id' => $product_cart['id'],
                'number' => $product_cart['number'],
            );
            save('cart_user', $up_cart_user);
        }
    }
    /*
    phân tích đồng bộ số lượng sản phẩm trong cart lên db:
    đầu tiên sẽ kểm tra người dùng hiện tại trên db
    có 2 trường hợp: 1 là người dùng hiện tại chưa có sản phẩm nào trên db, 2 là  đã có 1 số sản phẩm trên đó
    TH1:
        kiểm tra xem người dùng hiện tại có sp nào trên db không
        nếu chưa có sẽ tiến hành upload sản phẩm lên với id = 0 là mặc định (add sp)
    TH2: (đã có 1 số sản phẩm trên đó)
        2.0) kiểm tra xem người dùng hiện tại có sp nào trên db không
        2.1) nếu sản phẩm trong cart chưa tồn tại ở trên db, sẽ tiến hành add sp với id = 0
        2.2) Nếu 1 số sp trong cart đã có trên db -> tiến hành đổi số lượng với id = id sản phẩm trong cart (Phải kiểm tra có đúng là của người dùng đang đăng nhập)
    */
}
//sync delete products between user and db when the user has placed an order
function detroy_cart_user_db()
{
    global $user_nav, $linkconnectDB;
    $sql = "DELETE FROM cart_user WHERE user_id=" . $user_nav;
    mysqli_query($linkconnectDB, $sql) or die(mysqli_error($linkconnectDB));
}
//Sync the quantity between user cart and db
function delete_cart_user_db($product_id)
{
    global $user_nav, $linkconnectDB;
    $sql = "DELETE FROM cart_user WHERE user_id=" . $user_nav . " and product_id=" . $product_id;
    mysqli_query($linkconnectDB, $sql) or die(mysqli_error($linkconnectDB));
}
//Update quantity of item
function cart_update($product_id, $number)
{
    if ($number == 0) {
        //Remove item from cart
        unset($_SESSION['cart'][$product_id]);
    } else {
        $_SESSION['cart'][$product_id]['number'] = $number;
    }
}
//Remove item from cart
function cart_delete($product_id)
{
    unset($_SESSION['cart'][$product_id]);
}
//Total price
function cart_total()
{
    $total = 0;
    foreach ($_SESSION['cart'] as $product) {
            $total += $product['price'] * $product['number'];
    }
    return $total;
}
//Quantity of items in cart
function cart_number()
{
    $number = 0;
    foreach ($_SESSION['cart'] as $product) {
        $number += $product['number'];
    }
    return $number;
}
//List of items in cart
function cart_list()
{
    return $_SESSION['cart'];
}
// Delete cart
function cart_destroy()
{
    $_SESSION['cart'] = array();
}
