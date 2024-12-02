<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';

$page_title = 'カート';

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
    }
} catch (PDOException $e) {
    exit('データベースエラー: ' . $e->getMessage());
}

include 'includes/head.php';
?>

<body class="bg-gray-50 flex flex-col min-h-screen">
    <?php include 'includes/header.php'; ?>

    <main class="max-w-7xl mx-auto px-4 py-6 sm:py-8">
        <h2 class="text-2xl font-bold mb-6 sm:mb-8">ショッピングカート</h2>

        <?php if (empty($cart_items)): ?>
            <div class="bg-white rounded-lg shadow-md p-6 sm:p-8 text-center">
                <p class="text-gray-600 mb-6">カートに商品がありません</p>
                <a href="index.php"
                    class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    商品一覧に戻る
                </a>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-md">
                <?php foreach ($cart_items as $item): ?>
                    <div class="p-4 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center gap-4 border-b">
                        <img src="<?php echo h($item['image_path']); ?>"
                            alt="<?php echo h($item['name']); ?>"
                            class="w-24 h-24 object-cover rounded-lg">

                        <div class="flex-grow">
                            <h3 class="font-semibold mb-2"><?php echo h($item['name']); ?></h3>
                            <p class="text-gray-600 mb-2">
                                <?php echo format_price($item['price']); ?> × <?php echo $item['quantity']; ?>個
                            </p>
                            <p class="font-bold">
                                小計: <?php echo format_price($item['subtotal']); ?>
                            </p>
                        </div>

                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
                            <form action="update_cart.php" method="POST" class="flex gap-2">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>"
                                    min="1" class="w-20 p-2 border rounded-lg">
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    更新
                                </button>
                            </form>

                            <form action="remove_from_cart.php" method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                    削除
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="p-4 sm:p-6">
                    <div class="text-right mb-4">
                        <p class="text-xl font-bold">
                            合計: <?php echo format_price($total); ?>
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-between gap-4">
                        <a href="index.php" class="text-center text-blue-600 hover:text-blue-700">
                            買い物を続ける
                        </a>
                        <a href="checkout.php"
                            class="w-full sm:w-auto bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 text-center">
                            レジに進む
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>

</html>