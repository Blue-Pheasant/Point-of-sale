<!--
    admin/controllers/feedback/index.php
-->
<?php
permission_user();
require_once('admin/models/comments.php');
$title = 'All comments';
$nav_comment = 'class="active open"';
$option = array(
    'order_by' => 'id desc',
    'where' => 'status<>3 and status<>2'
);
$comments = get_all('comments', $option);
require('admin/views/comment/index.php');
