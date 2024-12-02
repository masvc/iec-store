<?php
session_start();

// TODO: ログインチェック
// if (!isset($_SESSION['user_id'])) {
//     header('Location: login.php');
//     exit();
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: データベースからアカウントを削除する処理
    // $user_id = $_SESSION['user_id'];
    // $sql = "DELETE FROM users WHERE id = :user_id";

    // セッションを破棄
    session_destroy();

    // ホームページにリダイレクト
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>アカウント削除確認</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>

<body class="bg-gray-50 flex flex-col min-h-screen">
    <?php include 'includes/header.php'; ?>

    <div class="max-w-md mx-auto mt-20 p-6 bg-white rounded-lg shadow">
        <h2 class="text-2xl font-bold mb-6 text-red-600">アカウント削除の確認</h2>

        <p class="mb-6">本当にアカウントを削除しますか？この操作は取り消すことができません。</p>

        <form method="POST" class="flex gap-4">
            <button type="submit" class="flex-1 bg-red-600 text-white py-3 rounded-lg hover:bg-red-700">
                削除する
            </button>
            <a href="profile.php" class="flex-1 bg-gray-500 text-white py-3 rounded-lg hover:bg-gray-600 text-center">
                キャンセル
            </a>
        </form>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>

</html>