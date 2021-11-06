<!--
    admin/controllers/order/index.php
-->
<?php
permission_user();
if (isset($_POST['order_id'])) {
    foreach ($_POST['order_id'] as $order_id) {
        $order_id = intval($order_id);
    }
}
$options = array(
    'order_by' => 'status ASC, id DESC'
);
$url = 'admin.php?controller=order';
$total_rows = get_total('orders', $options);
$title = 'Order';
$nav_order  = 'class="active open"';
$orders = get_all('orders', $options);
$status = array(
    0 => 'Order`s not been processed',
    1 => 'Order Processed',
    2 => 'Order Processing',
    3 => 'Order Canceled'
);
require('admin/views/order/index.php');