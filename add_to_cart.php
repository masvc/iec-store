<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => '無効なリクエストです']);
    exit;
}

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => '商品IDが必要です']);
    exit;
}

if ($quantity <= 0) {
    echo json_encode(['success' => false, 'message' => '数量が無効です']);
    exit;
}

try {
    $pdo = get_db_connection();

    // 商品の存在確認
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :product_id");
    $stmt->execute([':product_id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo json_encode(['success' => false, 'message' => '商品が見つかりません']);
        exit;
    }

    // カートに追加（セッションを使用）
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }

    echo json_encode([
        'success' => true,
        'message' => 'カートに追加しました',
        'cart_count' => array_sum($_SESSION['cart'])
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'データベースエラー: ' . $e->getMessage()]);
}
