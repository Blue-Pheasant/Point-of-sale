<!--
    admin/controllers/product/update.php
-->
<?php
permission_user();
require_once('admin/models/products.php');
$title = 'Total edited products';
$nav_product  = 'class="active open"';
require('admin/views/product/update.php');