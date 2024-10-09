<?php
require_once __DIR__ . '/../../bootstrap.php';

use CT275\Project\User;
use CT275\Project\Paginator;

$user = new User($PDO);
$limit = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ?
    (int)$_GET['limit'] : 5;
$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ?
    (int)$_GET['page'] : 1;
$paginator = new Paginator(
    totalRecords: $user->count(),
    recordsPerPage: $limit,
    currentPage: $page
);
$users = $user->paginate($paginator->recordOffset, $paginator->recordsPerPage);
$pages = $paginator->getPages(length: 3);

?>
<!DOCTYPE html>
<html lang="en">

<?php  session_start();
    include_once __DIR__ . '/../../partials/header.php'; ?>
<body>
    <?php include_once __DIR__ . '/../../partials/navbar.php' ?>

    <!-- Main Page Content -->
    <div class="container">

        <?php
        $subtitle = 'Quản lý tài khoản tại đây.';
        ?>
        <br>
        <h2 class="text-center animate__animated animate__bounce">Quản Lý Tài Khoản</h2>
        <div class="row">
            <div class="col-12">
                <a href="./admined/add_admin.php" class="btn btn-primary mb-3">
                    <i class="fa fa-plus"></i> Thêm Admin
                </a>
                <table id="users" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">Họ Tên</th>
                            <th scope="col">Email</th>
                            <th scope="col">Số Điện Thoại</th>
                            <th scope="col">Quyền truy cập</th>
                            <th scope="col">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($users as $user): ?>
                        <tr>
                        <td><?=htmlspecialchars($user->fullname)?></td>
                        <td><?=htmlspecialchars($user->email)?></td>
                        <td><?=htmlspecialchars($user->phone_number)?></td>
                        <td><?=htmlspecialchars($user->role)?></td>
                        <td class="d-flex justify-content-center">
                            <a href="<?='./admined/edit_admin.php?id=' . $user->getId()?>"
                                class="btn btn-xs btn-warning">
                                <i alt="Edit" class="fa fa-pencil"></i> Sửa</a>
                                <form class="form-inline ml-1"
                                    action="./admined/delete_admin.php" method="POST">
                                    <input type="hidden" name="id"
                                        value="<?= $user->getId() ?>">
                                    <button type="submit"
                                        class="btn btn-xs btn-danger"
                                        name="delete-user">
                                    <i alt="Delete" class="fa fa-trash"></i> Xóa
                                    </button>
                                </form>
                            </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
                <!-- Table Ends Here -->

		<!-- Pagination -->
        <nav class="d-flex justify-content-center">
                    <ul class="pagination">
                    <li class="page-item<?= $paginator->getPrevPage() ? '' : ' disabled' ?>">
                            <a role="button" href="/admin/admin.php?page=<?= $paginator->getPrevPage() ?>&limit=5" 
                            class="page-link">
                                <span>&laquo;</span>
                            </a>
                        </li>
                    <?php foreach ($pages as $page) : ?>
                        <li class="page-item<?= $paginator->currentPage === $page ? ' active' : '' ?>">
                        <a role="button" href="/admin/admin.php?page=<?= $page ?>&limit=5" class="page-link"><?= $page ?></a>
                        </li>
                        <?php endforeach ?>
                        <li class="page-item<?= $paginator->getNextPage() ? '' : ' disabled' ?>">
                            <a role="button" href="/admin/admin.php?page=<?= $paginator->getNextPage() ?>&limit=5" 
                            class="page-link">
                                <span>&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <div id="delete-confirm" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-fullname">Xác nhận</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">Bạn có muốn xóa sản phẩm?</div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-danger" id="delete">Xóa</button>
                    <button type="button" data-dismiss="modal" class="btn btn-default">Hủy</button>
                </div>
            </div>
        </div>
    </div>

    <?php include_once __DIR__ . '/../../partials/footer.php' ?>

    <script>
    $(document).ready(function(){
        $('button[name="delete-user"]').on('click', function(e){
            e.preventDefault();
            const form = $(this).closest('form');
            const nameTd = $(this).closest('tr').find('td:first');
            if (nameTd.length > 0) {
            $('.modal-body').html(
            `Bạn chắc chắn muốn xóa "${nameTd.text()}"?`
            );
            }
            $('#delete-confirm').modal({
            backdrop: 'static', keyboard: false
            })
            .one('click', '#delete', function() {
            form.trigger('submit');
            });
        });
    });
    </script>
</body>

</html>