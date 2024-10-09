function increaseQuantity(productId,event) {
    updateQuantityAjax(productId, '+');
    if (event && event.preventDefault) {
        event.preventDefault();
    }
}

function decreaseQuantity(productId,event) {
    updateQuantityAjax(productId, '-');
    if (event && event.preventDefault) {
        event.preventDefault();
    }
}

function updateQuantityAjax(productId, action) {
    $.ajax({
        url: '/../cart/update_cart.php',
        method: 'POST',
        data: { productId: productId, action: action },
        dataType: 'json',
        success: function(response) {
            console.log(response);
            if (response.success) {
                updateQuantityOnUI(productId, response.cart[productId]);
            } else {
                alert('Không thể cập nhật số lượng sản phẩm. Vui lòng thử lại.');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('Ajax Error:', textStatus, errorThrown);
    console.log('Response:', jqXHR.responseText);
            alert('Ajax Error: ' + textStatus);
        }
    });
}

function updateQuantityOnUI(productId, newQuantity) {
    $('#quantity-value-' + productId).text(newQuantity);
}