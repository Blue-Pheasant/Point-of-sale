<!--
    admin/controllers/product/index.php
-->
<?php
permission_user();
require_once('admin/models/products.php');
$title = 'Total List of Products';
$nav_product  = 'class="active open"';
require('admin/views/product/index.php');