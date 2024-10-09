<?php
require_once __DIR__ . '/../../bootstrap.php';
session_start();
use CT275\Project\Order;
use CT275\Project\Paginator;

$order = new Order($PDO);
$limit = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ? (int)$_GET['limit'] : 5;
$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

$paginator = new Paginator(
    totalRecords: $order->count(),
    recordsPerPage: $limit,
    currentPage: $page
);


$orders = $order->paginateByOrderDate($paginator->recordOffset, $paginator->recordsPerPage);

$pages = $paginator->getPages(length: 3);
function fetchUserInfo($PDO, $userId)
{
    $userStatement = $PDO->prepare('SELECT * FROM user WHERE id = :user_id AND role = 0');
    $userStatement->bindParam(':user_id', $userId);
    $userStatement->execute();

    return $userStatement->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<?php
include_once __DIR__ . '/../../partials/header.php';
?>

<body>
    <?php include_once __DIR__ . '/../../partials/navbar.php' ?>

    <!-- Main Page Content -->
    <div class="container">

        <?php
        $subtitle = 'Quản lý đơn hàng tại đây.';
        ?>
        <br>
        <h2 class="text-center animate__animated animate__bounce">Quản Lý Đơn Hàng</h2>
        <div class="row">
            <div class="col-12">
                <table id="orders" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">Mã Đơn Hàng</th>
                            <th scope="col">Ngày Đặt</th>
                            <th scope="col">Họ Tên Người Đặt</th>
                            <th scope="col">Số Điện Thoại Người Đặt</th>
                            <th scope="col">Địa Chỉ Người Đặt</th>
                            <th scope="col">Chi Tiết Đơn Hàng</th>
                            <th scope="col">Tổng Tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order) : ?>
                            <tr>
                                <td><?= $order->getId() ?></td>
                                <td><?= $order->order_date ?></td>
                                <?php
                                $userId = $order->user_id;
                                $userInfo = fetchUserInfo($PDO, $userId);
                                ?>
                                <td><?= isset($userInfo['fullname']) ? $userInfo['fullname'] : '' ?></td>
                                <td><?= isset($userInfo['phone_number']) ? $userInfo['phone_number'] : '' ?></td>
                                <td><?= isset($userInfo['address']) ? $userInfo['address'] : '' ?></td>
                                <td>
                                    <ul>
                                        <?php
                                        $orderDetails = $order->getOrderDetails();
                                        foreach ($orderDetails as $orderDetail) :
                                        ?>
                                            <li>
                                                <strong><?= $orderDetail->getProductName() ?></strong><br>
                                                Số lượng: <?= $orderDetail->num ?><br>
                                                Giá tiền: <?= number_format($orderDetail->price) ?> VND<br>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </td>
                                <td><?= number_format($order->getTotalMoney()) ?> VND</td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <nav class="d-flex justify-content-center">
                    <ul class="pagination">
                        <li class="page-item<?= $paginator->getPrevPage() ? '' : ' disabled' ?>">
                            <a role="button" href="/admin/quanlydonhang.php?page=<?= $paginator->getPrevPage() ?>&limit=<?= $limit ?>" class="page-link">
                                <span>&laquo;</span>
                            </a>
                        </li>
                        <?php foreach ($pages as $page) : ?>
                            <li class="page-item<?= $paginator->currentPage === $page ? ' active' : '' ?>">
                                <a role="button" href="/admin/quanlydonhang.php?page=<?= $page ?>&limit=<?= $limit ?>" class="page-link"><?= $page ?></a>
                            </li>
                        <?php endforeach ?>
                        <li class="page-item<?= $paginator->getNextPage() ? '' : ' disabled' ?>">
                            <a role="button" href="/admin/quanlydonhang.php?page=<?= $paginator->getNextPage() ?>&limit=<?= $limit ?>" class="page-link">
                                <span>&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <?php include_once __DIR__ . '/../../partials/footer.php' ?>
</body>

</html>
