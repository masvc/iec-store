<?php
session_start();

// データベース接続
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=iec;charset=utf8mb4",
        "root",
        "",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    exit('データベース接続失敗: ' . $e->getMessage());
}

$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$products = [];

if ($search_query !== '') {
    try {
        $stmt = $pdo->prepare("
            SELECT p.*, u.username 
            FROM products p 
            JOIN users u ON p.user_id = u.id 
            WHERE p.name LIKE :search 
            OR p.description LIKE :search 
            ORDER BY p.created_at DESC
        ");

        $search_param = "%{$search_query}%";
        $stmt->execute([':search' => $search_param]);
        $products = $stmt->fetchAll();
    } catch (PDOException $e) {
        exit('検索に失敗しました: ' . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>検索結果 - InstaEC</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>

<body class="bg-gray-50 flex flex-col min-h-screen">
    <?php include 'includes/header.php'; ?>

    <main class="max-w-6xl mx-auto py-8">
        <h2 class="text-2xl font-bold mb-6 px-4">
            <?php if ($search_query): ?>
                "<?php echo htmlspecialchars($search_query); ?>" の検索結果
            <?php else: ?>
                検索キーワードを入力してください
            <?php endif; ?>
        </h2>

        <?php if (!empty($products)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-4">
                <?php foreach ($products as $product): ?>
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <?php if ($product['image_path']): ?>
                            <img
                                src="<?php echo htmlspecialchars($product['image_path']); ?>"
                                alt="<?php echo htmlspecialchars($product['name']); ?>"
                                class="w-full h-48 object-cover">
                        <?php endif; ?>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold mb-2">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </h3>
                            <p class="text-gray-600 mb-2">
                                <?php echo htmlspecialchars($product['description']); ?>
                            </p>
                            <p class="text-xl font-bold">
                                ¥<?php echo number_format($product['price']); ?>
                            </p>
                            <p class="text-sm text-gray-500 mt-2">
                                出品者: <?php echo htmlspecialchars($product['username']); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($search_query): ?>
            <p class="text-center text-gray-600 mt-8">
                検索結果が見つかりませんでした。
            </p>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>

</html>