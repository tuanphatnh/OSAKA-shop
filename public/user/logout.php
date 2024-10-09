<!DOCTYPE html>
<html lang="en">

<head>
    
    <script src="/../js/logout_message.js"></script>
</head>

<?php
session_start();

define('TITLE', 'Logout');
include_once __DIR__ . '/../../partials/header.php';
include_once __DIR__ . '/../../partials/navbar.php';

if (isset($_SESSION['user'])) {
    unset($_SESSION['user']);
    session_destroy();

} else {
    echo '<p>Bạn chưa đăng nhập, không có phiên đăng nhập để hủy.</p>';
}

include_once __DIR__ . '/../../partials/footer.php';
?>

</html>
