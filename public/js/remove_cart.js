function removeFromCart(productId) {
    $.ajax({
        url: '/../cart/remove_cart.php',
        method: 'POST',
        data: { productId: productId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Sản phẩm đã được xóa khỏi giỏ hàng!');
                updateCartPage();
            } else {
                alert('Không thể xóa sản phẩm khỏi giỏ hàng. Vui lòng thử lại.');
            }
        },
        error: function() {
            alert('Có lỗi xảy ra khi gửi yêu cầu.');
        }
    });
}

function updateCartPage() {
    $.ajax({
        url: '/../cart/cart.php',
        method: 'GET',
        dataType: 'html',
        success: function(response) {
            $('.cart-container').html($(response).find('.cart-container').html());
        },
        error: function() {
            alert('Có lỗi xảy ra khi cập nhật trang giỏ hàng.');
        }
    });
}