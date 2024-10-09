<?php
require_once __DIR__ . '/../../../bootstrap.php';
session_start();
use CT275\Project\Product;
$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

$image = $_FILES['thumbnail']['name'];
$tmp_image = $_FILES['thumbnail']['tmp_name'];
$target_dir = "uploads/";
$target_par = "sanpham/";
$target_file = $target_dir . basename($image);

move_uploaded_file($tmp_image, $target_file);


$mang=[];
$thumbnail = $target_par . $target_file;
$mang["title"]=$_POST["title"];
$mang["thumbnail"]=$thumbnail;
$mang["price"]=$_POST["price"];
$mang["description"]=$_POST["description"];


// echo var_dump($mang);
$product = new Product($PDO);
$product->fill($mang);


if ($product->validate()) {
$product->save() && redirect("./../sanpham.php");
echo $thumbnail;
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
        $subtitle = 'Thêm sản phẩm của bạn tại đây.';
        include_once __DIR__ . '/../../../partials/heading.php';
        ?>

        <div class="row">
            <div class="col-12">

                <form action="add.php" method="post" class="col-md-6 offset-md-3" enctype = "multipart/form-data">

                    <!-- Name Product -->
                    <div class="form-group">
                        <label for="title">Tên sản phẩm</label>
                        <input type="text" name="title" class="form-control<?= isset($errors['title']) ? ' is-invalid' : '' ?>" maxlen="255" id="title" placeholder="Nhập tên sản phẩm" value="<?= isset($_POST['title']) ? $_POST['title'] : '' ?>" />

                        <?php if (isset($errors['title'])) : ?>
                            <span class="invalid-feedback">
                                <strong><?= $errors['title'] ?></strong>
                            </span>
                        <?php endif ?>
                    </div>
                    <!-- Image -->
                    <div class="form-group">
                        <label for="thumbnail">Chọn hình ảnh</label>
                        <input type="file" name="thumbnail" class="form-control<?= isset($errors['thumbnail']) ? ' is-invalid' : '' ?>" maxlen="255" id="thumbnail" placeholder="Thêm hifnha rnh" value="<?= isset($_POST['thumbnail']) ? $_POST['thumbnail'] : '' ?>" />

                        <?php if (isset($errors['thumbnail'])) : ?>
                            <span class="invalid-feedback">
                                <strong><?= $errors['thumbnail'] ?></strong>
                            </span>
                        <?php endif ?>
                    </div>

                    <!-- Price -->
                    <div class="form-group">
                        <label for="price">Giá</label>
                        <input type="text" name="price" class="form-control<?= isset($errors['price']) ? ' is-invalid' : '' ?>" maxlen="255" id="price" placeholder="Nhập giá bán" value="<?= isset($_POST['price']) ? $_POST['price'] : '' ?>" />

                        <?php if (isset($errors['price'])) : ?>
                            <span class="invalid-feedback">
                                <strong><?= $errors['price'] ?></strong>
                            </span>
                        <?php endif ?>
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label for="description">Mô tả sản phẩm</label>
                        <textarea name="description" id="description" class="form-control<?= isset($errors['description']) ? ' is-invalid' : '' ?>" placeholder="Thêm mô tả (tối đa 255 kí tự)"><?= isset($_POST['description']) ? $_POST['description'] : '' ?></textarea>

                        <?php if (isset($errors['description'])) : ?>
                            <span class="invalid-feedback">
                                <strong><?= $errors['description'] ?></strong>
                            </span>
                        <?php endif ?>
                    </div>

                    <!-- Submit -->
                    <button type="submit" name="submit" class="btn btn-primary">Thêm sản phẩm</button>
                </form>

            </div>
        </div>

    </div>

    <?php include_once __DIR__ . '/../../../partials/footer.php' ?>
</body>

</html>