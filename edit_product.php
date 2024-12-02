<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';

require_login();

$page_title = '商品編集';
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    $pdo = get_db_connection();

    // 商品情報の取得
    $stmt = $pdo->prepare("
        SELECT * FROM products 
        WHERE id = :product_id AND user_id = :user_id
    ");
    $stmt->execute([
        ':product_id' => $product_id,
        ':user_id' => $_SESSION['user_id']
    ]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        $_SESSION['error'] = '商品が見つかりません';
        header('Location: profile.php');
        exit();
    }

    // カテゴリー一覧の取得
    $categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 商品情報の更新処理
        $stmt = $pdo->prepare("
            UPDATE products 
            SET name = :name,
                description = :description,
                price = :price,
                category_id = :category_id
            WHERE id = :id AND user_id = :user_id
        ");

        $stmt->execute([
            ':name' => $_POST['name'],
            ':description' => $_POST['description'],
            ':price' => $_POST['price'],
            ':category_id' => $_POST['category_id'],
            ':id' => $product_id,
            ':user_id' => $_SESSION['user_id']
        ]);

        $_SESSION['success'] = '商品情報を更新しました';
        header('Location: profile.php');
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = 'データベースエラー: ' . $e->getMessage();
    header('Location: profile.php');
    exit();
}

include 'includes/head.php';
?>

<body class="bg-gray-50 flex flex-col min-h-screen">
    <?php include 'includes/header.php'; ?>

    <main class="max-w-7xl mx-auto px-4 py-6 sm:py-8">
        <h2 class="text-2xl font-bold mb-6 sm:mb-8">商品編集</h2>

        <form method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-md p-4 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium">商品名</label>
                        <input type="text" name="name" required
                            value="<?php echo h($product['name']); ?>"
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium">価格</label>
                        <input type="number" name="price" required min="1"
                            value="<?php echo h($product['price']); ?>"
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium">カテゴリー</label>
                        <select name="category_id" required
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>"
                                    <?php echo $product['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo h($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium">現在の画像</label>
                        <img src="<?php echo h($product['image_path']); ?>"
                            alt="現在の商品画像"
                            class="w-32 h-32 object-cover rounded-lg mb-2">
                        <input type="file" name="image" accept="image/*"
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="text-sm text-gray-500 mt-1">画像を変更する場合のみ選択してください</p>
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium">商品説明</label>
                    <textarea name="description" required rows="12"
                        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500"><?php echo h($product['description']); ?></textarea>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-4 mt-6">
                <a href="profile.php"
                    class="text-center py-3 px-6 border border-gray-300 rounded-lg hover:bg-gray-50">
                    キャンセル
                </a>
                <button type="submit"
                    class="bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700">
                    更新する
                </button>
            </div>
        </form>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>

</html>