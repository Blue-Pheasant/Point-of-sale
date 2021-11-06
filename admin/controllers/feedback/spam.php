<!--
    admin/controllers/feedback/spam.php
-->
<?php
permission_user();
require_once('admin/models/comments.php');
$title = 'Spam comments';
$nav_comment = 'class="active open"';
require('admin/views/comment/spam.php');