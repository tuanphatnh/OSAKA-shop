<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['productId'];
    $action = $_POST['action'];
    
    if ($action === '+' || $action === '-') {
        $newQuantity = isset($_SESSION['cart'][$productId]) ? $_SESSION['cart'][$productId] + ($action === '+' ? 1 : -1) : 1;

        if ($newQuantity < 1) {
            $newQuantity = 1;
        }

        $_SESSION['cart'][$productId] = $newQuantity;
        
        error_log('Đã cập nhật số lượng ' . $productId . ': ' . $newQuantity);
        echo json_encode(['success' => true, 'cart' => $_SESSION['cart']]);
    } else {
        echo json_encode(['error' => 'Hành động không hợp lệ']);
    }
}
?>