<!--
    admin/controllers/feedback/trash.php
-->
<?php
permission_user();
require_once('admin/models/comments.php');
$title = 'Trash';
$nav_comment = 'class="active open"';
require('admin/views/comment/trash.php');