<?php
session_start();
require_once __DIR__ . '/../../bootstrap.php';

    if (isset($_POST['productId'])) {
        $productId = $_POST['productId'];
    
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }
    
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]++;
        } else {
            $_SESSION['cart'][$productId] = 1;
        }

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ']);
    }
    ?>