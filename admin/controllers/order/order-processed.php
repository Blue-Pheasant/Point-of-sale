<!--
    admin/controllers/order/order-processed.php
<?php
permission_user();
$options = array(
    'where' => 'status = 1',
    'order_by' => 'create_at DESC'
);
$order_processed  = get_all('orders', $options);

$title = 'Order processed';
$nav_order  = 'class="active open"';
$status = array(
    0 => 'Order`s not been processed',
    1 => 'Order Processed',
    2 => 'Order Processing'
);
require('admin/views/order/order-complete.php');