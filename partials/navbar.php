<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Pacifico">
<style>
    h1 {
    font-family: 'Pacifico', cursive; 
    font-size: 1.5em; 
    color: #3498db; 
    text-shadow: 2px 2px 4px rgba(52, 152, 219, 0.5); 
    padding-top: 5px;
}

h1:hover {
    color: #e74c3c;
    text-shadow: 3px 3px 6px rgba(231, 76, 60, 0.7); 
    transform: scale(1.1); 
    transition: all 0.3s ease; 
}

.navbar-nav .nav-link {
    font-family: 'Roboto', sans-serif;
    font-size: 1em;  
    color: #000;
}

.navbar-nav .nav-link:hover {
    color: #e74c3c;
    text-decoration: underline;
}

.navbar{
    background-color: #FFCC99;
}
</style>
<nav class="navbar navbar-expand-md sticky-top navbar-light ">
    <a class="navbar-brand" href="/index.php">
        <img src = "/image/logo.jpg" width="50" height="50"/> 
    </a>
    <h1>Ăn Vặt OSAKA</h1>


    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
                <?php if (isset($_SESSION['user']) && $_SESSION['user'] == '0') : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/">Trang Chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/../cart/cart.php">Giỏ Hàng</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="/../donhang/donhang.php">Đơn Hàng Của Bạn</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/../user/logout.php">Đăng Xuất</a>
                    </li>

                <?php elseif (isset($_SESSION['user']) && $_SESSION['user'] == '1') : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/../admin/admin.php">Quản Lý Tài khoản</a>
                    </li>   
                    <li class="nav-item">
                    <a class="nav-link" href="/../admin/sanpham.php">Quản Lý Sản Phẩm</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="/../admin/quanlydonhang.php">Quản Lý Đơn Hàng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../user/logout.php">Đăng Xuất</a>
                </li>
                    
                <?php else : ?>  
                <li class="nav-item">
                <a class="nav-link" href="/index.php">Trang Chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../user/login.php">Đăng Nhập</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../user/DangKy.php">Đăng Ký</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>