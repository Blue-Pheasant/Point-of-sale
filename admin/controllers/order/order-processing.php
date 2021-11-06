<!--
    admin/controllers/order/order-processing.php
-->
<?php
permission_user();
$title = 'Orders are being processed';
$nav_order  = 'class="active open"';
require('admin/views/order/order-processing.php');
