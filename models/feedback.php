<!--
    models/Feedback.php
-->
<?php
function feedback_add()
{
    $feedback_add = array(
        'id' => intval($_POST['feedback_id']),
        'user_id' => intval($_POST['user_id']),
        'product_id' => intval($_POST['product_id']),
        'name' => escape($_POST['name']),
        'start' => intval($_POST['starts']),
        'comment' => escape($_POST['message']),
        'createTime' => gmdate('Y-m-d H:i:s', time() + 7 * 3600)
    );

    save('feedbacks', $feedback_add);
    echo "<div style='padding-top: 200' class='container'><div style='text-align: center;' class='alert alert-success'><strong>Done!</strong> Your feedback has been sent to our system. Thank you for sending it back to us.<br><br>Come here<a href='index.php'>Homepage</a></div></div>";
    require('views/feedback/result.php');
    exit;
}
