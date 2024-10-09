<!DOCTYPE html>
<html lang="en">

<head>
<?php
require_once __DIR__ . '/../../../bootstrap.php';
session_start();
use CT275\Project\User;
$user = new User($PDO);

$id = isset($_REQUEST['id']) ?
    filter_var($_REQUEST['id'], FILTER_SANITIZE_NUMBER_INT) : -1;
if ($id < 0 || !($user->find($id))) {
    redirect('./../admin.php');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($user->update($_POST)) {

        redirect('./../admin.php');
    }
    $errors = $user->getValidationErrors();
}
    include_once __DIR__ . '/../../../partials/header.php';
    ?>
</head>

<body>
    <?php 
    include_once __DIR__ . '/../../../partials/navbar.php' ?>

    <!-- Main Page Content -->
    <div class="container">
        <?php
        $subtitle = 'Điều chỉnh thông tin tài khoản tại đây.';
        ?>
        <br>
        <h2 class="text-center animate__animated animate__bounce">Chỉnh sửa thông tin tài khoản</h2>
        <div class="row">
            <div class="col-md-6 offset-md-3 text-center">
                <p class="animate__animated animate__fadeInLeft"><?= $subtitle ?></p>
            </div>
        </div>

        <?php if (!empty($errors)) : ?>
            <div class="alert alert-danger" role="alert">
                <ul>
                    <?php foreach ($errors as $error) : ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-12">

                <form method="post" class="col-md-6 offset-md-3" id="dangky-form">
                <!-- Full Name -->
                <div class="form-group">
                        <label for="fullname"><b>Họ Tên</b></label>
                        <input type="text" name="fullname" class="form-control<?= isset($errors['fullname']) ? ' is-invalid' : '' ?>" maxlen="255" id="fullname" placeholder="Enter Name Product" value="<?= $user->fullname ?>" />

                        <?php if (isset($errors['fullname'])) : ?>
                            <span class="invalid-feedback">
                                <strong><?= $errors['fullname'] ?></strong>
                            </span>
                        <?php endif ?>
                    </div>

                <!-- Phone Number -->
                <div class="form-group">
                    <label for="phone_number"><b>Số Điện Thoại</b></label>
                    <input type="text" name="phone_number" class="form-control<?= isset($errors['phone_number']) ? ' is-invalid' : '' ?>" maxlength="255" id="phone_number" placeholder="Nhập số điện thoại" value="<?= $user->phone_number ?>" />

                    <?php if (isset($errors['phone_number'])) : ?>
                        <span class="invalid-feedback">
                            <strong><?= $errors['phone_number'] ?></strong>
                        </span>
                    <?php endif ?>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email"><b>Email</b></label>
                    <input type="email" name="email" id="email" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>" placeholder="Nhập địa chỉ email" value="<?= $user->email ?>" />

                    <?php if (isset($errors['email'])) : ?>
                        <span class="invalid-feedback">
                            <strong><?= $errors['email'] ?></strong>
                        </span>
                    <?php endif ?>
                </div>

                <!-- Role -->
                <div class="form-group">
                    <label for="role"><b>Quyền truy cập</b></label>
                    <input name="role" id="role" class="form-control<?= isset($errors['role']) ? ' is-invalid' : '' ?>" placeholder="0 hoặc 1" value = "<?= $user->role ?>"/>

                    <?php if (isset($errors['role'])) : ?>
                        <span class="invalid-feedback">
                            <strong><?= $errors['role'] ?></strong>
                        </span>
                    <?php endif ?>
                </div>

                <!-- Submit Button -->
                <button type="submit" name="submit" class="btn btn-primary">Cập nhật</button>
            </form>

            </div>
        </div>

    </div>
    <?php include_once __DIR__ . '/../../../partials/footer.php' ?>
    </body>
</html>