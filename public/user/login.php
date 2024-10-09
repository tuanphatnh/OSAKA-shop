<?php
session_start();
define('TITLE', 'Login');
include_once __DIR__ . '/../../partials/header.php';
include_once __DIR__ . '/../../partials/navbar.php';

$error_message = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        require_once __DIR__ . '/../../bootstrap.php';
        $query = 'select * from user where email = :email';
        $statement = $PDO->prepare($query);
        $statement->bindParam(':email', $_POST['email']);
        $statement->execute();
        $user = $statement->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($_POST['password'], $user['password'])) {
            if ($user['role'] == '1') {
                $_SESSION['user'] = '1';
                $_SESSION['user_id'] = $user['id'];
                header("Location: /../admin/admin.php");
                exit();
            } else {
                $_SESSION['user'] = '0';
                $_SESSION['user_id'] = $user['id'];
                header("Location: /");
                exit();
            }
            } else {
                $error_message = 'Địa chỉ email và mật khẩu không khớp!';
            }
        } else {
            $error_message = 'Hãy đảm bảo rằng bạn cung cấp đầy đủ địa chỉ email và mật khẩu!';
        }
    }

if ($error_message) {
    include __DIR__ . '/../../partials/show_error.php';
}

if (isset($_SESSION['user'])) {
    echo '<br><h2 class="text-center animate__animated animate__bounce">Đăng Nhập</h2>
    <p class ="text-center">Bạn đã đăng nhập!</p>';
} else {
    ?>

    <div class="container">
    <br>
        <h2 class="text-center animate__animated animate__bounce">Đăng Nhập</h2>
        <p class="animate__animated animate__fadeInLeft text-center">Đăng nhập tài khoản của bạn</p>
    <div class="row">
            <div class="col-12 ">
                <form method="post" action="login.php" class="col-md-4 offset-md-4 border border-warning p-4 rounded rounded-3">
                
                <div class="form-group">
                <label for="email"><b>Email</b></label><br>
                <input type="email" name="email" placeholder="Nhập email" class="form-control">
            </div>

            <div class="form-group">
                <label for="email"><b>Mật Khẩu</b></label><br>
                <input type="password" name="password" placeholder="Nhập mật khẩu" class="form-control">
            </div>

            <div class="form-group">
                <input class="btn bg-primary" type="submit" name="submit" value="Đăng nhập!">
            </div>

                </form>
            </div>
        </div>
    </div><?php
}
include_once __DIR__ . '/../../partials/footer.php';
?>