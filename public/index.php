<!DOCTYPE html>
<html lang="en">
<?php require("../partials/header.php")?>
<?php
    session_start();
    require_once __DIR__ . '/../bootstrap.php';
    $loggedin = isset($_SESSION['user']) && $_SESSION['user'] == '0';
    $statement = $PDO->prepare("SELECT * FROM product");
    $statement->execute();
    $products = $statement->fetchAll(PDO::FETCH_ASSOC);
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/Trangchu.css" />
    <style>
        .main-container {
            max-width: 1200px;
            margin: auto;
            border: 5px orange solid;
            border-radius: 10px;
        }

        .slideshow-container {
            max-width: 100%;
            position: relative;
            margin: auto;
        }

        .mySlides {
            display: none;
        }

        .mySlides img {
            width: 100%;
        }

        .text {
            position: absolute;
            bottom: 8px;
            left: 8px;
            color: black;
            font-size: 18px;
            font-weight: bold;
        }

        .prev,
        .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            width: auto;
            padding: 16px;
            margin-top: -22px;
            color: white;
            font-weight: bold;
            font-size: 18px;
            transition: 0.6s ease;
            border-radius: 0 3px 3px 0;
        }

        .next {
            right: 0;
            border-radius: 3px 0 0 3px;
        }

        .prev:hover,
        .next:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }
    </style>
    <title>Đồ Ăn Vặt OSAKA</title>
</head>

<body>
    <?php include_once __DIR__ . '/../partials/navbar.php' ?>
    
    <!-- Main Container -->
    <div class="main-container">
        <!-- Slideshow Container -->
        <div class="slideshow-container">
            <!-- Slides -->
            <div class="mySlides">
                <img src="/image/bg.jpg" alt="Image 1">
                <!-- <div class="text">Liên hệ ngay!</div> -->
            </div>
            <div class="mySlides">
                <img src="/image/sale.jpg" alt="Image 2">
                <!-- <div class="text">Sale khủng!</div> -->
            </div>
            <!-- Navigation Buttons -->
            <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
            <a class="next" onclick="plusSlides(1)">&#10095;</a>
        </div>

        <!-- Product Section -->
        <section>
            <br>
            <h2 class="text-center animate__animated animate__bounce">Sản phẩm nổi bật</h2>
            <div class="product-container">
                <?php foreach ($products as $product) : ?>
                    <div class="product">
                        <h3 class="product-title"><?= $product['title'] ?></h3>
                        <p class="product-thumbnail">
                            <img src="/admin/<?=htmlspecialchars($product['thumbnail'])?>" alt="<?=htmlspecialchars($product['title'])?>">
                        </p>
                        <p class="product-price">Giá: <?= number_format($product['price']) ?> VND</p>
                        <button type="button" class="btn btn-warning" onclick="addToCart(<?= $product['id'] ?>)">Thêm vào giỏ hàng</button>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($products)) : ?>
                    <p class="animate__animated animate__fadeInLeft">Không có sản phẩm nào được tìm thấy.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>

    
    <?php include_once __DIR__ . '/../partials/footer.php' ?>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="./js/add_cart.js"></script>
    <script>
        var slideIndex = 0;
        var timeout;

        function showSlides() {
            var i;
            var slides = document.getElementsByClassName("mySlides");
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            slideIndex++;
            if (slideIndex > slides.length) {
                slideIndex = 1;
            }
            slides[slideIndex - 1].style.display = "block";
            timeout = setTimeout(showSlides, 2000);
        }

        function plusSlides(n) {
            clearTimeout(timeout);
            showSlides(slideIndex += n);
        }

        $(document).ready(function () {
            showSlides();
        });
    </script>
</body>
</html>
