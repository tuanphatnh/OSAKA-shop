<?php
require_once __DIR__ . '/../../../bootstrap.php';
session_start();
use CT275\Project\Product;
$product = new Product($PDO);
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['id'])
    && ($product->find($_POST['id'])) !== null
) {
    $product->delete();
}
redirect('./../sanpham.php');