<header class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <a href="index.php" class="flex items-center">
                <span class="text-xl font-bold text-gray-900">IEC Store.</span>
            </a>
            <div class="flex items-center gap-4">
                <form action="search.php" method="GET" class="relative">
                    <input
                        type="text"
                        name="q"
                        placeholder="商品を検索..."
                        class="pl-3 pr-8 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2">
                        🔍
                    </button>
                </form>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="cart.php" class="relative">
                        <span class="text-2xl">🛒</span>
                        <?php
                        try {
                            $cart_stmt = $pdo->prepare("
                                SELECT COUNT(*) as count 
                                FROM cart_items 
                                WHERE user_id = :user_id
                            ");
                            $cart_stmt->execute([':user_id' => $_SESSION['user_id']]);
                            $cart_count = $cart_stmt->fetch(PDO::FETCH_ASSOC)['count'];

                            if ($cart_count > 0): ?>
                                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                    <?php echo $cart_count; ?>
                                </span>
                        <?php endif;
                        } catch (PDOException $e) {
                            // エラー時は数字を表示しない
                        }
                        ?>
                    </a>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="flex items-center gap-2">
                        <a href="profile.php" class="text-gray-600 hover:text-gray-800">
                            マイページ
                        </a>
                        <span class="text-gray-300">|</span>
                        <a href="logout.php" class="text-gray-600 hover:text-gray-800">
                            ログアウト
                        </a>
                    </div>
                <?php else: ?>
                    <div class="flex gap-2">
                        <a href="login.php" class="text-blue-600 hover:text-blue-700">
                            ログイン
                        </a>
                        <span class="text-gray-300">|</span>
                        <a href="create.php" class="text-blue-600 hover:text-blue-700">
                            アカウント作成
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>