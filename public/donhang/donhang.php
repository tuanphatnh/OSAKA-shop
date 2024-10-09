<?php
include_once __DIR__ . '/../../bootstrap.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /../user/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$dh = new CT275\Project\Order($PDO);
if ($user_id) {
    $donhangs = $dh->paginateByUserId($user_id);
    $userInfo = $dh->getUserInfo();
} else {
    echo "User ID không hợp lệ";
    exit();
}

include_once __DIR__ . '/../../partials/header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn Hàng Của Bạn</title>
</head>

<body>
    <?php include_once __DIR__ . '/../../partials/navbar.php' ?>

    <section>
        <br>
        <h2 class="text-center animate__animated animate__bounce">Đơn Hàng Của Bạn</h2>
        <div class="cart-container">
            <?php if (!empty($donhangs)) : ?>
                <div class="col-12">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">Mã Đơn Hàng</th>
                                <th scope="col">Ngày Đặt</th>
                                <th scope="col"> Họ Tên</th>
                                <th scope="col">Số Điện Thoại</th>
                                <th scope="col">Địa Chỉ</th>
                                <th scope="col">Tên Sản Phẩm</th>
                                <th scope="col">Số Lượng</th>
                                <th scope="col">Giá Tiền</th>
                                <th scope="col">Tổng Tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($donhangs as $donhang) : ?>
                                <?php
                                $userId = $donhang->user_id;
                                $userStatement = $PDO->prepare('SELECT * FROM user WHERE id = :user_id');
                                $userStatement->bindParam(':user_id', $userId);
                                $userStatement->execute();
                                $userInfo = $userStatement->fetch(PDO::FETCH_ASSOC);
                                ?>
                                <?php
                                $orderDetails = $donhang->getOrderDetails();
                                foreach ($orderDetails as $index => $orderDetail) :
                                ?>
                                    <tr>
                                        <?php if ($index === 0) : ?>
                                            <td><?= $donhang->getId() ?></td>
                                            <td><?= $donhang->order_date ?></td>
                                            <td><?= isset($userInfo['fullname']) ? $userInfo['fullname'] : '' ?></td>
                                            <td><?= isset($userInfo['phone_number']) ? $userInfo['phone_number'] : '' ?></td>
                                            <td><?= isset($userInfo['address']) ? $userInfo['address'] : '' ?></td>
                                        <?php else : ?>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        <?php endif; ?>
                                        <td><?= $orderDetail->getProductName() ?></td>
                                        <td><?= $orderDetail->num ?></td>
                                        <td><?= number_format($orderDetail->total_money) ?> VND</td>
                                        <td><?= ($index === 0) ? number_format($donhang->getTotalMoney()) . ' VND' : '' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <p class="animate__animated animate__fadeInLeft text-center">Bạn chưa có đơn hàng nào.</p>
            <?php endif; ?>
        </div>
    </section>

    <?php include_once __DIR__ . '/../../partials/footer.php' ?>
</body>

</html>
