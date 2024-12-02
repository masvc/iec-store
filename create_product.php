<?php
session_start();

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// データベース接続
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=iec;charset=utf8mb4",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    exit('データベース接続失敗: ' . $e->getMessage());
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $image_path = $_POST['image_path']; // 一時的に画像URLを直接入力する形式
    $category_id = $_POST['category_id'];

    // バリデーション
    if (empty($name)) {
        $errors[] = "商品名を入力してください";
    }
    if (empty($description)) {
        $errors[] = "商品の説明を入力してください";
    }
    if (empty($price)) {
        $errors[] = "価格を入力してください";
    } elseif (!is_numeric($price)) {
        $errors[] = "価格は数値で入力してください";
    }
    if (empty($image_path)) {
        $errors[] = "画像URLを入力してください";
    }

    // 商品登録処理
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO products (user_id, name, description, price, image_path, category_id) 
                VALUES (:user_id, :name, :description, :price, :image_path, :category_id)
            ");

            $stmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':name' => $name,
                ':description' => $description,
                ':price' => $price,
                ':image_path' => $image_path,
                ':category_id' => $category_id
            ]);

            // 商品登録成功後、プロフィールページへリダイレクト
            header('Location: profile.php');
            exit();
        } catch (PDOException $e) {
            $errors[] = '商品の登録に失敗しました: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品出品 - InstaEC</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>

<body class="bg-gray-50 flex flex-col min-h-screen">
    <?php include 'includes/header.php'; ?>

    <main class="max-w-7xl mx-auto px-4 py-6 sm:py-8">
        <h2 class="text-2xl font-bold mb-6 sm:mb-8">新規出品</h2>

        <form method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-md p-4 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium">商品名</label>
                        <input type="text" name="name" required
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium">価格</label>
                        <input type="number" name="price" required min="1"
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium">カテゴリー</label>
                        <select name="category_id" required
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            <?php
                            $categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
                            foreach ($categories as $category) {
                                echo '<option value="' . $category['id'] . '">' .
                                    htmlspecialchars($category['name']) .
                                    '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium">商品画像</label>
                        <input type="file" name="image" required accept="image/*"
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium">商品説明</label>
                    <textarea name="description" required rows="12"
                        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-4 mt-6">
                <a href="profile.php"
                    class="text-center py-3 px-6 border border-gray-300 rounded-lg hover:bg-gray-50">
                    キャンセル
                </a>
                <button type="submit"
                    class="bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700">
                    出品する
                </button>
            </div>
        </form>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>

</html>