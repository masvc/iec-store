<?php
session_start();

// すでにログインしている場合はプロフィールページへリダイレクト
if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username)) {
        $errors[] = "ユーザー名を入力してください";
    }
    if (empty($password)) {
        $errors[] = "パスワードを入力してください";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: index.php');
                exit();
            } else {
                $errors[] = "ユーザー名またはパスワードが正しくありません";
            }
        } catch (PDOException $e) {
            $errors[] = 'ログインに失敗しました: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン - InstaEC</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>

<body class="bg-gray-50 flex flex-col min-h-screen">
    <?php include 'includes/header.php'; ?>

    <main class="max-w-md mx-auto mt-10 p-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold mb-6">ログイン</h2>

            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="mb-6">
                    <label class="block mb-2">ユーザー名</label>
                    <input
                        type="text"
                        name="username"
                        class="w-full p-3 rounded-lg border border-gray-200"
                        value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
                </div>

                <div class="mb-6">
                    <label class="block mb-2">パスワード</label>
                    <input
                        type="password"
                        name="password"
                        class="w-full p-3 rounded-lg border border-gray-200">
                </div>

                <button
                    type="submit"
                    class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700">
                    ログイン
                </button>
            </form>

            <p class="text-center mt-4 text-gray-600">
                アカウントをお持ちでない方は
                <a href="create.php" class="text-blue-600 hover:text-blue-700">アカウント作成</a>
                へ
            </p>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>

</html>