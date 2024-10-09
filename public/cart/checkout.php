<?php
require_once __DIR__ . '/../../bootstrap.php';
session_start();

function TotalPrice($cart, $PDO) {
    try {
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
    } catch (Exception $e) {
        error_log('Error in TotalPrice function: ' . $e->getMessage());
        return 'Error';
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    

    $userStatement = $PDO->prepare('SELECT * FROM user WHERE id = :user_id');
    $userStatement->bindParam(':user_id', $userId);
    $userStatement->execute();
    $userInfo = $userStatement->fetch(PDO::FETCH_ASSOC);
    error_log(print_r($userInfo, true));

    $fullname = $userInfo['fullname'] ?? '';
    $email = $userInfo['email'] ?? '';
    $phone_number = $userInfo['phone_number'] ?? '';
    $address = $userInfo['address'] ?? '';
    $order_date = date('Y-m-d H:i:s'); 
    $total_money = TotalPrice($_SESSION['cart'], $PDO);
    
    $order = new CT275\Project\Order($PDO);
    $order->fill([
        'user_id' => $userId,
        'fullname' => $fullname,
        'email' => $email,
        'phone_number' => $phone_number,
        'address' => $address,
        'order_date' => $order_date,
        'total_money' => $total_money,
    ]);
    
    if ($order->save()) {
        foreach ($_SESSION['cart'] as $productId => $quantity) {

            $statement = $PDO->prepare("SELECT * FROM product WHERE id = :id");
            $statement->bindParam(':id', $productId);
            $statement->execute();
            $product = $statement->fetch(PDO::FETCH_ASSOC);

            $orderDetail = new CT275\Project\OrderDetail($PDO);
            $orderDetail->fill([
                'order_id' => $order->getId(),
                'product_id' => $productId,
                'price' => $product['price'],
                'num' => $quantity,
                'total_money' => $product['price'] * $quantity,
            ]);
            $orderDetail->save();
        }
        unset($_SESSION['cart']);
        header('Location: /../donhang/donhang.php');
        exit();
    } else {
        error_log('Error saving order: ' . print_r($order->getValidationErrors(), true));
        echo 'Lỗi khi lưu đơn hàng.';
    }
} else {
    header('Location: ./cart.php');
    exit();
}
?>