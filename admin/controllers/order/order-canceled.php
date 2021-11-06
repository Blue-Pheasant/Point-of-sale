<!--
    admin/controllers/order/order-cancel.php
-->
<?php
permission_user();
$options = array(
    'where' => 'status = 3',
    'order_by' => 'create_at DESC'
);
$order_processed  = get_all('orders', $options);

$title = 'Đơn hàng đã bị hủy';
$nav_order  = 'class="active open"';
$status = array(
    0 => 'Order`s not been processed',
    1 => 'Order Processed',
    2 => 'Order Processing',
    3 => 'Order Canceled'
);
require('admin/views/order/order-cancel.php');