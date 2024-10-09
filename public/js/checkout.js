function checkout() {
    $.ajax({
        url: '/../cart/checkout.php',
        method: 'POST',
        data: { action: 'checkout' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                console.log('Thanh toán thành công!');
            } else {
                alert('Thanh Toán thất bại. Hãy thử lại');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('Ajax Error:', textStatus, errorThrown);
            alert('Ajax Error: ' + textStatus);
        }
    });
}