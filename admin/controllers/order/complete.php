<!--
    admin/controllers/order/complete.php
-->
<?php
permission_user();
require_once('admin/models/order.php');
//submit form click
if (!empty($_POST)) {
    order_processed($_POST['order_id']);
}
header('location:admin.php?controller=order');