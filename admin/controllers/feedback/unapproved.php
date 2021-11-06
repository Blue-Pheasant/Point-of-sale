<!--
    admin/controllers/feedback/unapproved.php
-->
<?php
permission_user();
require_once('admin/models/feedback.php');
if (isset($_GET['comment_id'])) {
    $comment_id = intval($_GET['comment_id']);
    comment_unApproved($comment_id);
    header('location:admin.php?controller=comment&action=pending');
}
