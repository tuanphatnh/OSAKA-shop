<!DOCTYPE html>
<html lang="en">
<?php include_once __DIR__ . '/../../partials/header.php';?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/../css/Cart.css"/>
    <title>Giỏ hàng</title>
</head>

<body>
    <?php session_start(); 
    include_once __DIR__ . '/../../partials/navbar.php' ?>

    <section>
        <br>
        <h2 class="text-center animate__animated animate__bounce">Giỏ Hàng Của Bạn</h2>
        <div class="cart-container">
            <?php
            
            require_once __DIR__ . '/../../bootstrap.php';

            function TotalPrice($cart, $PDO) {
                try{
                $totalPrice = 0;
                foreach ($cart as $productId => $quantity) {
                    $statement = $PDO->prepare("SELECT * FROM product WHERE id = :id");
                    $statement->bindParam(':id', $productId);
                    $statement->execute();
                    $product = $statement->fetch(PDO::FETCH_ASSOC);
            
                    // Tính tổng giá
                    if ($product) {
                        $totalPrice += $product['price'] * $quantity;
                    }
                }
                return number_format($totalPrice);
                }catch (Exception $e){
                    error_log('Error in TotalPrice function: ' . $e->getMessage());
                    return 'Error';
                }
            }

            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])){
                ?>

        <form action="./checkout.php" method="post">
            <div class="col-12">
                <table id="tb" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">Sản phẩm</th>
                            <th scope="col">Ảnh</th>
                            <th scope="col">Giá</th>
                            <th scope="col">Số lượng</th>
                            <th scope="col">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php        
                    foreach ($_SESSION['cart'] as $productId => $quantity) {
                    // Truy vấn CSDL để lấy thông tin sản phẩm theo $productId
                    $statement = $PDO->prepare("SELECT * FROM product WHERE id = :id");
                    $statement->bindParam(':id', $productId);
                    $statement->execute();
                    $product = $statement->fetch(PDO::FETCH_ASSOC);

                    // Hiển thị thông tin sản phẩm trong giỏ hàng
                    if ($product) {
                        ?>
                        <tr>
                            <td class="product-title"><?= $product['title'] ?></td>
                            <td class="product-thumbnail">
                                <img src="/../admin/<?=htmlspecialchars($product['thumbnail'])?>" width = "100px" alt="<?=htmlspecialchars($product['title'])?>">
                            </td>
                            <td class="product-price"><?= number_format($product['price']) ?> VND</td>
                            <td class="product-quantity">
                                <button class="quantity-btn" onclick="decreaseQuantity(<?= $product['id'] ?>,event)">-</button>
                                <span id="quantity-value-<?= $product['id'] ?>" class="quantity-value"><?= $quantity ?></span>
                                <button class="quantity-btn" onclick="increaseQuantity(<?= $product['id'] ?>, event)">+</button>
                            </td>
                            <td><button type="button" class="btn btn-danger" onclick="removeFromCart(<?= $product['id'] ?>)">Xóa</button></td>
                        
                        </tr>
                        <?php
                    }
                }
                ?>
                </table>
            
                <div class="cart-summary text-right">
                    <h3>Tổng giá: <?= TotalPrice($_SESSION['cart'], $PDO) ?> VND</h3>
                    <button type="submit" class="btn btn-success">Thanh toán</button>
                </div>
            </div>
        </form>
        </div>
            <?php
            }else {
                echo '<p class="animate__animated animate__fadeInLeft">Giỏ hàng của bạn trống.</p>';
            }
            ?>
    
    </section>

    <?php include_once __DIR__ . '/../../partials/footer.php' ?>

    

</body>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    
    <script src="/../js/update_cart.js"></script>
    <script src="/../js/remove_cart.js"></script>
    <!-- <script src = "/../js/checkout.js"></script> -->
</html>