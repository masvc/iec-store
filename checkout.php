<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';

require_login();

$page_title = 'チェックアウト';

try {
    $pdo = get_db_connection();
    $cart_items = [];
    $total = 0;

    // カートに商品がある場合
    if (!empty($_SESSION['cart'])) {
        // カート内の商品情報を取得
        $product_ids = array_keys($_SESSION['cart']);
        $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';

        $stmt = $pdo->prepare("
            SELECT * FROM products 
            WHERE id IN ($placeholders)
        ");
        $stmt->execute($product_ids);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // カート表示用のデータを作成
        foreach ($products as $product) {
            $quantity = $_SESSION['cart'][$product['id']];
            $subtotal = $product['price'] * $quantity;
            $total += $subtotal;

            $cart_items[] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'image_path' => $product['image_path'],
                'quantity' => $quantity,
                'subtotal' => $subtotal
            ];
        }
    } else {
        header('Location: cart.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 注文情報の保存処理
        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, total_amount, payment_method, shipping_name, shipping_postal_code, shipping_address, shipping_phone)
            VALUES (:user_id, :total_amount, :payment_method, :shipping_name, :shipping_postal_code, :shipping_address, :shipping_phone)
        ");

        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':total_amount' => $total,
            ':payment_method' => $_POST['payment_method'],
            ':shipping_name' => $_POST['shipping_name'],
            ':shipping_postal_code' => $_POST['shipping_postal_code'],
            ':shipping_address' => $_POST['shipping_address'],
            ':shipping_phone' => $_POST['shipping_phone']
        ]);

        $order_id = $pdo->lastInsertId();

        // 注文詳細の保存
        $stmt = $pdo->prepare("
            INSERT INTO order_details (order_id, product_id, quantity, price)
            VALUES (:order_id, :product_id, :quantity, :price)
        ");

        foreach ($cart_items as $item) {
            $stmt->execute([
                ':order_id' => $order_id,
                ':product_id' => $item['id'],
                ':quantity' => $item['quantity'],
                ':price' => $item['price']
            ]);
        }

        // カートをクリア
        unset($_SESSION['cart']);

        // 注文完了ページにリダイレクト
        header('Location: order_complete.php?order_id=' . $order_id);
        exit();
    }
} catch (PDOException $e) {
    exit('データベースエラー: ' . $e->getMessage());
}

include 'includes/head.php';
?>

<body class="bg-gray-50 flex flex-col min-h-screen">
    <?php include 'includes/header.php'; ?>

    <main class="max-w-7xl mx-auto px-4 py-6 sm:py-8">
        <h2 class="text-2xl font-bold mb-6 sm:mb-8">チェックアウト</h2>

        <form method="POST" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- 左側: 配送・支払い情報 -->
            <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
                <h3 class="text-xl font-semibold mb-6">配送・支払い情報</h3>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium">お名前</label>
                            <input type="text" name="shipping_name" required
                                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium">電話番号</label>
                            <input type="tel" name="shipping_phone" required
                                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium">郵便番号</label>
                        <input type="text" name="shipping_postal_code" required
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium">住所</label>
                        <input type="text" name="shipping_address" required
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium">支払い方法</label>
                        <select name="payment_method" required
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            <?php foreach (PAYMENT_METHODS as $key => $method): ?>
                                <option value="<?php echo $key; ?>"><?php echo $method; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- 右側: 注文内容確認 -->
            <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
                <h3 class="text-xl font-semibold mb-6">注文内容</h3>

                <div class="space-y-4">
                    <div class="divide-y">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="py-4 flex items-center gap-4">
                                <img src="<?php echo h($item['image_path']); ?>"
                                    alt="<?php echo h($item['name']); ?>"
                                    class="w-20 h-20 object-cover rounded-lg">

                                <div class="flex-grow">
                                    <h4 class="font-medium"><?php echo h($item['name']); ?></h4>
                                    <p class="text-sm text-gray-600">
                                        <?php echo format_price($item['price']); ?> × <?php echo $item['quantity']; ?>個
                                    </p>
                                    <p class="font-bold mt-1">
                                        <?php echo format_price($item['subtotal']); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center text-lg font-bold">
                            <span>合計</span>
                            <span><?php echo format_price($total); ?></span>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-4 rounded-lg hover:bg-blue-700 transition-colors duration-200 font-medium text-lg">
                        注文を確定する
                    </button>

                    <a href="cart.php" class="block text-center text-blue-600 hover:text-blue-700">
                        カートに戻る
                    </a>
                </div>
            </div>
        </form>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>

</html>