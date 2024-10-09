<?php
require_once __DIR__ . '/../../bootstrap.php';

use CT275\Project\Product;
use CT275\Project\Paginator;

$product = new Product($PDO);
$limit = (isset($_GET['limit']) && is_numeric($_GET['limit'])) ?
    (int)$_GET['limit'] : 5;
$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ?
    (int)$_GET['page'] : 1;
$paginator = new Paginator(
    totalRecords: $product->count(),
    recordsPerPage: $limit,
    currentPage: $page
);
$products = $product->paginate($paginator->recordOffset, $paginator->recordsPerPage);
$pages = $paginator->getPages(length: 3);

?>
<!DOCTYPE html>
<html lang="en">
<?php session_start();
    include_once __DIR__ . '/../../partials/header.php';?>
<body>
    <?php 
     include_once __DIR__ . '/../../partials/navbar.php' ?>

    <!-- Main Page Content -->
    <div class="container">

        <?php
        $subtitle = 'Xem tất cả sản phẩm của bạn tại đây.';
        include_once __DIR__ . '/../../partials/heading.php';
        ?>

        <div class="row">
            <div class="col-12">

                <a href="./sanpham/add.php" class="btn btn-primary mb-3">
                    <i class="fa fa-plus"></i> Thêm Sản Phẩm
                </a>
                <!-- Table Starts Here -->
                <table id="products" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">Tên Sản Phẩm</th>
                            <th scope="col">Hình ảnh</th>
                            <th scope="col">Giá</th>
                            <th scope="col">Mô tả sản phẩm</th>
                            <th scope="col">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($products as $product): ?>
                        <tr>
                        <td><?=htmlspecialchars($product->title)?></td>
                        <td><img src="<?=htmlspecialchars($product->thumbnail)?>" width="150px" alt="<?=htmlspecialchars($product->title)?>" /></td>
                        <td><?=htmlspecialchars($product->price)?></td>
                        <td><?=htmlspecialchars($product->description)?></td>
                        <td class="d-flex justify-content-center">
                            <a href="<?='./sanpham/edit.php?id=' . $product->getId()?>"
                                class="btn btn-xs btn-warning">
                                <i alt="Edit" class="fa fa-pencil"></i> Sửa</a>
                                <form class="form-inline ml-1"
                                    action="./sanpham/delete.php" method="POST">
                                    <input type="hidden" name="id"
                                        value="<?= $product->getId() ?>">
                                    <button type="submit"
                                        class="btn btn-xs btn-danger"
                                        name="delete-product">
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
                            <a role="button" href="/admin/sanpham.php?page=<?= $paginator->getPrevPage() ?>&limit=5" 
                            class="page-link">
                                <span>&laquo;</span>
                            </a>
                        </li>
                    <?php foreach ($pages as $page) : ?>
                        <li class="page-item<?= $paginator->currentPage === $page ? ' active' : '' ?>">
                        <a role="button" href="/admin/sanpham.php?page=<?= $page ?>&limit=5" class="page-link"><?= $page ?></a>
                        </li>
                        <?php endforeach ?>
                        <li class="page-item<?= $paginator->getNextPage() ? '' : ' disabled' ?>">
                            <a role="button" href="/admin/sanpham.php?page=<?= $paginator->getNextPage() ?>&limit=5" 
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
                    <h4 class="modal-title">Xác nhận</h4>
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
        $('button[name="delete-product"]').on('click', function(e){
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