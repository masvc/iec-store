<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';

require_login();

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    $pdo = get_db_connection();

    // 商品の所有者確認
    $stmt = $pdo->prepare("
        SELECT user_id 
        FROM products 
        WHERE id = :product_id
    ");
    $stmt->execute([':product_id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product || $product['user_id'] !== $_SESSION['user_id']) {
        $_SESSION['error'] = '削除権限がありません';
        header('Location: profile.php');
        exit();
    }

    // 商品の削除
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :product_id");
    $stmt->execute([':product_id' => $product_id]);

    $_SESSION['success'] = '商品を削除しました';
    header('Location: profile.php');
    exit();
} catch (PDOException $e) {
    $_SESSION['error'] = 'データベースエラー: ' . $e->getMessage();
    header('Location: profile.php');
    exit();
}
