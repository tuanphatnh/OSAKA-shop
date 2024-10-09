<?php
require_once __DIR__ . '/../../../bootstrap.php';
session_start();
use CT275\Project\Product;

$errors = [];
$product = new Product($PDO);

$id = isset($_REQUEST['id']) ?
    filter_var($_REQUEST['id'], FILTER_SANITIZE_NUMBER_INT) : -1;

if ($id < 0 || !($product->find($id))) {
    redirect('./../sanpham.php');
}

$existingThumbnail = $product->thumbnail;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image = $_FILES['thumbnail']['name'];
    $tmp_image = $_FILES['thumbnail']['tmp_name'];
    $target_dir = "uploads/";
    $target_par = "sanpham/";
    $target_file = $target_dir . basename($image);

    $thumbnail = file_exists($tmp_image) ? $target_par . $target_file : $existingThumbnail;

    if (file_exists($tmp_image)) {
        move_uploaded_file($tmp_image, $target_file);
    }

    $mang = [
        "title" => $_POST["title"],
        "thumbnail" => $thumbnail,
        "price" => $_POST["price"],
        "description" => $_POST["description"],
    ];

    var_dump($thumbnail);

    if ($product->update($mang)) {
        redirect('./../sanpham.php');
    }

    $errors = $product->getValidationErrors();
}
include_once __DIR__ . '/../../../partials/header.php';
?>

<body>
    <?php include_once __DIR__ . '/../../../partials/navbar.php' ?>

    <!-- Main Page Content -->
    <div class="container">

        <?php
        $subtitle = 'Cập nhật sản phẩm của bạn tại đây.';
        include_once __DIR__ . '/../../../partials/heading.php';
        ?>

        <div class="row">
            <div class="col-12">

                <form method="post" class="col-md-6 offset-md-3" enctype = "multipart/form-data">

                    <input type="hidden" name="id" value="<?= $product->getId() ?>">

                    <!-- Name Product -->
                    <div class="form-group">
                        <label for="title">Tên Sản Phẩm</label>
                        <input type="text" name="title" class="form-control<?= isset($errors['title']) ? ' is-invalid' : '' ?>" maxlenght="255" id="title" placeholder="Nhập tên sản phẩm" value="<?= $product->title ?>" />

                        <?php if (isset($errors['title'])) : ?>
                            <span class="invalid-feedback">
                                <strong><?= $errors['title'] ?></strong>
                            </span>
                        <?php endif ?>
                    </div>
                    
                    <!-- Image -->
                    <div class="form-group">
                        <label for="thumbnail">Chọn hình ảnh</label>
                        <input type="file" name="thumbnail" class="form-control<?= isset($errors['thumbnail']) ? ' is-invalid' : '' ?>"  id="thumbnail" placeholder="Thêm hình ảnh"/>

                        <?php if (isset($errors['thumbnail'])) : ?>
                            <span class="invalid-feedback">
                                <strong><?= $errors['thumbnail'] ?></strong>
                            </span>
                        <?php endif ?>
                        
                    </div>

                    <!-- Price -->
                    <div class="form-group">
                        <label for="Price">Price</label>
                        <input type="text" name="price" class="form-control<?= isset($errors['price']) ? ' is-invalid' : '' ?>" maxlenght="255" id="price" placeholder="Nhập giá bán" value="<?= $product->price ?>" />

                        <?php if (isset($errors['price'])) : ?>
                            <span class="invalid-feedback">
                                <strong><?= $errors['price'] ?></strong>
                            </span>
                        <?php endif ?>
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label for="description">Description </label>
                        <textarea name="description" id="description" class="form-control<?= isset($errors['description']) ? ' is-invalid' : '' ?>" placeholder="Nhập mô tả (tối đa 255 kí tự)"><?= $product->description ?></textarea>

                        <?php if (isset($errors['description'])) : ?>
                            <span class="invalid-feedback">
                                <strong><?= $errors['description'] ?></strong>
                            </span>
                        <?php endif ?>
                    </div>

                    <!-- Submit -->
                    <button type="submit" name="submit" class="btn btn-primary">Update Product</button>
                </form>

            </div>
        </div>

    </div>

    <?php include_once __DIR__ . '/../../../partials/footer.php' ?>
</body>

</html>