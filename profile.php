<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';

require_login();

$page_title = 'マイページ';

try {
    $pdo = get_db_connection();

    // ユーザーの商品を取得
    $stmt = $pdo->prepare("
        SELECT p.*, c.name as category_name 
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.user_id = :user_id 
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('データベースエラー: ' . $e->getMessage());
}

include 'includes/head.php';
?>

<body class="bg-gray-50 flex flex-col min-h-screen">
    <?php include 'includes/header.php'; ?>

    <main class="max-w-7xl mx-auto px-4 py-6 sm:py-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 sm:mb-8">
            <h2 class="text-2xl font-bold">出品した商品</h2>
            <a href="create_product.php"
                class="inline-flex items-center justify-center px-4 py-3 sm:py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                新規出品
            </a>
        </div>

        <?php if (empty($products)): ?>
            <div class="bg-white rounded-lg shadow-md p-6 sm:p-8 text-center">
                <p class="text-gray-600 mb-6">出品した商品はありません</p>
                <a href="create_product.php"
                    class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    商品を出品する
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                <?php foreach ($products as $product): ?>
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
                        <img src="<?php echo h($product['image_path']); ?>"
                            alt="<?php echo h($product['name']); ?>"
                            class="w-full h-48 sm:h-52 object-cover rounded-t-lg">

                        <div class="p-4 sm:p-5">
                            <div class="mb-3">
                                <span class="inline-block bg-gray-100 rounded-full px-3 py-1 text-sm text-gray-600">
                                    <?php echo h($product['category_name']); ?>
                                </span>
                            </div>
                            <h3 class="text-lg font-semibold mb-2 line-clamp-2">
                                <?php echo h($product['name']); ?>
                            </h3>
                            <p class="text-gray-600 mb-3 text-sm line-clamp-2">
                                <?php echo h($product['description']); ?>
                            </p>
                            <p class="text-xl font-bold mb-4">
                                <?php echo format_price($product['price']); ?>
                            </p>

                            <div class="flex gap-3">
                                <a href="edit_product.php?id=<?php echo $product['id']; ?>"
                                    class="flex-1 bg-blue-600 text-white py-3 sm:py-2.5 rounded-lg text-center hover:bg-blue-700 transition-colors duration-200">
                                    編集
                                </a>
                                <a href="delete_product.php?id=<?php echo $product['id']; ?>"
                                    onclick="return confirm('本当に削除しますか？')"
                                    class="flex-1 bg-red-600 text-white py-3 sm:py-2.5 rounded-lg text-center hover:bg-red-700 transition-colors duration-200">
                                    削除
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>

</html>