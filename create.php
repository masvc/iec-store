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

// フォームが送信された場合の処理
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // バリデーション
    if (empty($username)) {
        $errors[] = "ユーザー名を入力してください";
    }
    if (empty($password)) {
        $errors[] = "パスワードを入力してください";
    }
    if ($password !== $confirm_password) {
        $errors[] = "パスワードが一致しません";
    }

    // ユーザー名の重複チェック
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = "このユーザー名は既に使用されています";
            }
        } catch (PDOException $e) {
            $errors[] = 'エラーが発生しました: ' . $e->getMessage();
        }
    }

    // アカウント作成処理
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO users (username, password) 
                VALUES (:username, :password)
            ");

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt->execute([
                ':username' => $username,
                ':password' => $hashed_password
            ]);

            // 作成したアカウントでログイン
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['username'] = $username;

            // トップページへリダイレクト
            header('Location: index.php');
            exit();
        } catch (PDOException $e) {
            $errors[] = 'アカウント作成に失敗しました: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>アカウント作成 - InstaEC</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>

<body class="bg-gray-50 flex flex-col min-h-screen">
    <?php include 'includes/header.php'; ?>

    <div class="max-w-md mx-auto mt-10 p-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold mb-6">アカウント作成</h2>

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

                <div class="mb-6">
                    <label class="block mb-2">パスワード（確認）</label>
                    <input
                        type="password"
                        name="confirm_password"
                        class="w-full p-3 rounded-lg border border-gray-200">
                </div>

                <button
                    type="submit"
                    class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700">
                    アカウントを作成
                </button>
            </form>

            <p class="text-center mt-4 text-gray-600">
                すでにアカウントをお持ちの方は
                <a href="login.php" class="text-blue-600 hover:text-blue-700">ログイン</a>
                へ
            </p>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>

</html>