<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    require_once __DIR__ . '/../../../bootstrap.php';

    use CT275\Project\User;

    $errors = [];
    $success_message = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = new User($PDO);
        $user->fill($_POST);

        $user->role = '1';
        
        if ($user->validate()){
            $user->password = password_hash($user->password, PASSWORD_DEFAULT);
            if ($user->save()) {
                $success_message = 'Thêm thành công!';
                redirect("./admin.php");
            } else {
                $errors[] = 'Đã xảy ra lỗi khi lưu thông tin người dùng.';
            }
        }else {
            $errors = $user->getValidationErrors();
        }
    }
    
        

    include_once __DIR__ . '/../../../partials/header.php';
    ?>
</head>

<body>
    <?php session_start();
    include_once __DIR__ . '/../../../partials/navbar.php' ?>

    <!-- Main Page Content -->
    <div class="container">
        <?php
        $subtitle = 'Thêm tài khoản mới tại đây.';
        ?>
        <br>
        <h2 class="text-center animate__animated animate__bounce">Thêm tài khoản Admin</h2>
        <div class="row">
            
            <div class="col-md-6 offset-md-3 text-center">
                <p class="animate__animated animate__fadeInLeft"><?= $subtitle ?></p>
            </div>
        </div>
        <?php if ($success_message) : ?>
            <div class="alert alert-success" role="alert">
                <?= $success_message ?>
            </div>
        <?php endif; ?>

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
                    <label for="fullname"><b>Họ tên</b></label>
                    <input type="text" name="fullname" class="form-control" id="fullname" placeholder="Nhập họ và tên" value="<?= isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : '' ?>" />

                    <?php if (isset($errors['fullname'])) : ?>
                        <span class="invalid-feedback">
                            <strong><?= $errors['fullname'] ?></strong>
                        </span>
                    <?php endif ?>
                </div>

                <!-- Phone Number -->
                <div class="form-group">
                    <label for="phone_number"><b>Số Điện Thoại</b></label>
                    <input type="text" name="phone_number" class="form-control<?= isset($errors['phone_number']) ? ' is-invalid' : '' ?>" maxlength="255" id="phone_number" placeholder="Nhập số điện thoại" value="<?= isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : '' ?>" />

                    <?php if (isset($errors['phone_number'])) : ?>
                        <span class="invalid-feedback">
                            <strong><?= $errors['phone_number'] ?></strong>
                        </span>
                    <?php endif ?>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email"><b>Email</b></label>
                    <input type="email" name="email" id="email" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>" placeholder="Nhập địa chỉ email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" />

                    <?php if (isset($errors['email'])) : ?>
                        <span class="invalid-feedback">
                            <strong><?= $errors['email'] ?></strong>
                        </span>
                    <?php endif ?>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password"><b>Mật Khẩu</b></label>
                    <input type="password" name="password" id="password" class="form-control<?= isset($errors['password']) ? ' is-invalid' : '' ?>" placeholder="Nhập mật khẩu (tối thiểu 8 ký tự bao gồm chữ thường, chữ in hoa, kí tự đặc biệt)" value="<?= isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '' ?>" />

                    <?php if (isset($errors['password'])) : ?>
                        <span class="invalid-feedback">
                            <strong><?= $errors['password'] ?></strong>
                        </span>
                    <?php endif ?>
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="confirm_password"><b>Nhập lại mật khẩu</b></label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control<?= isset($errors['confirm_password']) ? ' is-invalid' : '' ?>" placeholder="Nhập lại mật khẩu" value="<?= isset($_POST['confirm_password']) ? htmlspecialchars($_POST['confirm_password']) : '' ?>" />

                    <?php if (isset($errors['confirm_password'])) : ?>
                        <span class="invalid-feedback">
                            <strong><?= $errors['confirm_password'] ?></strong>
                        </span>
                    <?php endif ?>
                </div>

                <!-- Address -->
                <div class="form-group">
                    <label for="address"><b>Địa chỉ</b></label>
                    <textarea name="address" id="address" class="form-control<?= isset($errors['address']) ? ' is-invalid' : '' ?>" placeholder="Nhập địa chỉ (tối đa 255 ký tự)"><?= isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '' ?></textarea>

                    <?php if (isset($errors['address'])) : ?>
                        <span class="invalid-feedback">
                            <strong><?= $errors['address'] ?></strong>
                        </span>
                    <?php endif ?>
                </div>

                <!-- Submit Button -->
                <button type="submit" name="submit" class="btn btn-primary">Thêm</button>
            </form>

            </div>
        </div>

    </div>
    <?php include_once __DIR__ . '/../../../partials/footer.php' ?>
    </body>
</html>