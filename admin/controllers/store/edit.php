<!--
    admin/controllers/store/edit.php
-->
<?php
permission_user();
permission_moderator();
require_once('admin/models/shop.php');
if (!empty($_POST)) {
    categories_update();
}
if (isset($_GET['cate_id'])) $cate_id = intval($_GET['cate_id']);
else $cate_id = 0;
$title = ($cate_id == 0) ? 'Add category' : 'Edit category';
$category = get_a_record('categories', $cate_id);
$nav_category = 'class="active open"';
require('admin/views/shop/edit.php');
