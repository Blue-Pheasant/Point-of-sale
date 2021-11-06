<!--
    controllers/comment/index.php
-->
<?php
if (!empty($_POST)) {
    global $user_nav;
    if (isset($user_nav)) {
        $comment_add = array(
            'id' => 0,
            'product_id' => intval($_POST['product_id']),
            'user_id' => intval($_POST['user_id']),
            'email' => escape($_POST['email']),
            'author' => escape($_POST['author']),
            'content' => escape($_POST['content']),
            'create_at' => gmdate('Y-m-d H:i:s', time() + 7 * 3600),
            'link_image' => escape($_POST['link_image'])
        );
    } else {
        $comment_add = array(
            'id' => 0,
            'product_id' => intval($_POST['product_id']),
            'user_id' => intval($_POST['user_id']),
            'email' => escape($_POST['email']),
            'author' => escape($_POST['author']),
            'content' => escape($_POST['content']),
            'create_at' => gmdate('Y-m-d H:i:s', time() + 7 * 3600)
        );
    }
    save('comments', $comment_add);
    echo "<div class='alert alert-success'><strong>Done!</strong> You have successfully recorded your comment!<br>Let <a href='javascript: history.go(-1)'>Back to product</a> Or <a href='index.php'>Go to homepage</a></div>";
}
require('views/comment/index.php');