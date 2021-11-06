<!--
    admin/controllers/order/view.php
-->
<?php
permission_user();
require_once('admin/models/order.php');
if (isset($_GET['order_id'])) $order_id = intval($_GET['order_id']); else $order_id=0;
$order = get_a_record('orders', $order_id);
if (!$order) {
    show_404();
}
$title = 'Order details';
$nav_order  = 'class="active open"';
$order_detail = order_detail($order_id);
$status = array(
    0 => 'Order confirmed',
    2 => 'Delivery in progress',
    1 => 'Delivered',
    3 => 'Order canceled'
);
require('admin/views/order/view.php');