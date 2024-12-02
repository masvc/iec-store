<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

    if ($product_id > 0 && $quantity > 0) {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

header('Location: cart.php');
exit();
