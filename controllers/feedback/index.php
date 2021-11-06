<!--
    controllers/feedback/index.php
-->
<?php
require_once('models/feedback.php');
if (!empty($_POST)) {
    feedback_add();
}
if (isset($_GET['product_id'])) $product_id = intval($_GET['product_id']);
else $product_id = 0;
$product = get_a_record('products', $product_id);
if (isset($user_nav)) {
    $user_action = get_a_record('users', $user_nav);
}
$title = 'Send your feedback to our system';
require('views/feedback/index.php');
