function addToCart(productId) {
    $.ajax({
        url: '/../user/checkin.php',
        method: 'GET',
        success: function(response) {
            if (response.authenticated) {
                addToCartAjax(productId);
                exit();
            } else {
                alert('Bạn cần đăng nhập để đặt hàng.');
                window.location.href = '/../user/login.php';
            }
        },
        error: function(xhr, status, error) {
            console.error('Lỗi kiểm tra đăng nhập');
            console.log('Error:', status, error);
        }
    });
}

function addToCartAjax(productId) {
    $.ajax({
        url: '/../cart/add_cart.php',
        method: 'POST',
        data: { productId: productId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Sản phẩm đã được thêm vào giỏ hàng!');
            } else {
                alert('Không thể thêm sản phẩm vào giỏ hàng. Vui lòng thử lại.');
            }
        },
        error: function() {
            alert('Có lỗi xảy ra. Vui lòng thử lại sau.');
        }
    });
}