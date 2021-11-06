<!--
    admin/controllers/feedback/pending.php
-->
<?php
permission_user();
require_once('admin/models/comments.php');
$title = 'Unapproved comments';
$nav_comment = 'class="active open"';
require('admin/views/comment/pending.php');
