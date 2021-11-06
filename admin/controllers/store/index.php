<!--
    admin/controllers/store/index.php
-->
<?php
permission_user();
permission_moderator();
require_once('admin/models/shop.php');
$options = array(
    'order_by' => 'id'
);
$title = 'Product Category Group';
$categories = get_all('categories', $options);
$nav_category = 'class="active open"';
//load view
require('admin/views/shop/index.php');