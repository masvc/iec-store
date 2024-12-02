<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';

require_login();

$page_title = '注文完了';
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if (!$order_id) {
    header('Location: index.php');
    exit();
}

try {
    $pdo = get_db_connection();
    // 注文情報の取得
    $stmt = $pdo->prepare("
        SELECT o.*, 
               od.quantity,
               p.name as product_name,
               p.price,
               p.image_path
        FROM orders o
        JOIN order_details od ON o.id = od.order_id
        JOIN products p ON od.product_id = p.id
        WHERE o.id = :order_id AND o.user_id = :user_id
    ");
    $stmt->execute([
        ':order_id' => $order_id,
        ':user_id' => $_SESSION['user_id']
    ]);
    $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($order_items)) {
        header('Location: index.php');
        exit();
    }

    // 注文の基本情報は最初の行から取
    $order = [
        'id' => $order_items[0]['id'],
        'shipping_name' => $order_items[0]['shipping_name'],
        'shipping_postal_code' => $order_items[0]['shipping_postal_code'],
        'shipping_address' => $order_items[0]['shipping_address'],
        'shipping_phone' => $order_items[0]['shipping_phone'],
        'payment_method' => $order_items[0]['payment_method'],
        'total_amount' => $order_items[0]['total_amount'],
        'created_at' => $order_items[0]['created_at']
    ];
} catch (PDOException $e) {
    exit('データベース接続失敗: ' . $e->getMessage());
}

include 'includes/head.php';
?>

<body class="bg-gray-50 flex flex-col min-h-screen">
    <?php include 'includes/header.php'; ?>

    <main class="max-w-4xl mx-auto py-8 px-4">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-green-600 mb-4">ご注文ありがとうございます</h2>
                <p class="text-gray-600">
                    注文番号: <?php echo str_pad($order['id'], 8, '0', STR_PAD_LEFT); ?>
                </p>
                <p class="text-sm text-gray-500">
                    注文日時: <?php echo date('Y年m月d日 H:i', strtotime($order['created_at'])); ?>
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- 配送情報 -->
                <div>
                    <h3 class="font-semibold mb-4">配送情報</h3>
                    <div class="text-gray-600">
                        <p>お名前: <?php echo htmlspecialchars($order['shipping_name']); ?></p>
                        <p>郵便番号: <?php echo htmlspecialchars($order['shipping_postal_code']); ?></p>
                        <p>住所: <?php echo htmlspecialchars($order['shipping_address']); ?></p>
                        <p>電話番号: <?php echo htmlspecialchars($order['shipping_phone']); ?></p>
                    </div>
                </div>

                <!-- 支払い情報 -->
                <div>
                    <h3 class="font-semibold mb-4">支払い情報</h3>
                    <div class="text-gray-600">
                        <p>支払い方法:
                            <?php
                            $payment_methods = [
                                'credit_card' => 'クレジットカード',
                                'bank_transfer' => '銀行振込',
                                'convenience_store' => 'コンビニ支払い'
                            ];
                            echo $payment_methods[$order['payment_method']] ?? $order['payment_method'];
                            ?>
                        </p>
                        <p class="mt-2">合計金額: ¥<?php echo number_format($order['total_amount']); ?></p>
                    </div>
                </div>
            </div>

            <!-- 注文内容 -->
            <div>
                <h3 class="font-semibold mb-4">注文内容</h3>
                <div class="divide-y">
                    <?php foreach ($order_items as $item): ?>
                        <div class="py-4 flex gap-4">
                            <img
                                src="<?php echo htmlspecialchars($item['image_path']); ?>"
                                alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                class="w-20 h-20 object-cover rounded">
                            <div>
                                <h4 class="font-semibold">
                                    <?php echo htmlspecialchars($item['product_name']); ?>
                                </h4>
                                <p class="text-sm text-gray-600">
                                    数量: <?php echo $item['quantity']; ?>
                                </p>
                                <p class="font-bold">
                                    ¥<?php echo number_format($item['price'] * $item['quantity']); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="mt-8 text-center">
                <a href="index.php"
                    class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                    トップページに戻る
                </a>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>

</html>