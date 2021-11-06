<!--
    controllers/forgot-password/change-password.php
-->
<?php
if (isset($_GET['id'])) $user_id = $_GET['id'];
$user_info = get_a_record('users', $user_id);
$title = 'Change Password - Forgot Password';
require('contents/views/forgot-password/change-password.php');