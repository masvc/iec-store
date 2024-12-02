<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';

$page_title = '商品一覧';

// カテゴリーと並び替えのパラメータを取得
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// 並び替えの条件を設定
$order_by = match ($sort) {
    'price_asc' => 'ORDER BY price ASC',
    'price_desc' => 'ORDER BY price DESC',
    'newest' => 'ORDER BY created_at DESC',
    default => 'ORDER BY created_at DESC'
};

// カテゴリーフィルターの条件を設定
$category_condition = $category_id > 0 ? 'AND p.category_id = :category_id' : '';

$query = "SELECT * FROM products WHERE 1";
if ($category_id) {
    $query .= " AND category_id = " . intval($category_id);
}
$query .= " ORDER BY created_at DESC";

// 商品を取得するSQLを構築
$sql = "
    SELECT p.*, u.username, c.name as category_name 
    FROM products p
    JOIN users u ON p.user_id = u.id
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE 1=1 
    {$category_condition}
    {$order_by}
";

try {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare($sql);
    if ($category_id > 0) {
        $stmt->bindParam(':category_id', $category_id);
    }
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // カテゴリー一覧を取得
    $categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
} catch (PDOException $e) {
    exit('データベース接続失敗: ' . $e->getMessage());
}

include 'includes/head.php';
?>

<body class="bg-gray-50 flex flex-col min-h-screen">
    <?php include 'includes/header.php'; ?>

    <!-- フィルターとソートのUI -->
    <div class="max-w-7xl mx-auto px-4 py-6 sm:py-8">
        <div class="flex flex-col sm:flex-row gap-4 mb-6 sm:mb-8">
            <!-- カテゴリー選択 -->
            <div class="w-full sm:w-1/3">
                <select id="category-select" class="w-full p-3 text-base border rounded-lg">
                    <option value="" class="py-2">すべてのカテゴリー</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>"
                            <?php echo $category_id == $category['id'] ? 'selected' : ''; ?>
                            class="py-2">
                            <?php echo h($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 並び替え選択 -->
            <div class="w-full sm:w-1/3">
                <select id="sort-select" class="w-full p-3 text-base border rounded-lg">
                    <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?> class="py-2">
                        新着順
                    </option>
                    <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?> class="py-2">
                        価格の安い順
                    </option>
                    <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?> class="py-2">
                        価格の高い順
                    </option>
                </select>
            </div>
        </div>
    </div>

    <!-- 商品一覧 -->
    <main class="max-w-7xl mx-auto px-4 py-6 sm:py-8">
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
                        <p class="text-xl font-bold mb-3">
                            <?php echo format_price($product['price']); ?>
                        </p>
                        <p class="text-sm text-gray-500 mb-4">
                            出品者: <?php echo h($product['username']); ?>
                        </p>
                        <button onclick='openModal(<?php echo json_encode($product); ?>)'
                            class="w-full bg-blue-600 text-white py-3 sm:py-2.5 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            詳細を見る
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- モーダル -->
    <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg max-w-2xl w-full mx-4">
            <div id="modal-content"></div>
            <div class="mt-4 flex justify-end">
                <button onclick="closeModal()"
                    class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    閉じる
                </button>
            </div>
        </div>
    </div>

    <script>
        // カテゴリー選択時の処理
        document.getElementById('category-select').addEventListener('change', function() {
            updateFilters();
        });

        // 並び替え選択時の処理
        document.getElementById('sort-select').addEventListener('change', function() {
            updateFilters();
        });

        // フィルターの更新処理
        function updateFilters() {
            const categoryId = document.getElementById('category-select').value;
            const sortOption = document.getElementById('sort-select').value;
            const params = new URLSearchParams(window.location.search);

            if (categoryId) {
                params.set('category_id', categoryId);
            } else {
                params.delete('category_id');
            }

            if (sortOption) {
                params.set('sort', sortOption);
            } else {
                params.delete('sort');
            }

            window.location.href = `${window.location.pathname}?${params.toString()}`;
        }

        function openModal(product) {
            const modal = document.getElementById('modal');
            const content = document.getElementById('modal-content');

            content.innerHTML = `
                <img src="${product.image_path}" alt="${product.name}" class="w-full h-64 object-cover rounded mb-4">
                <h3 class="text-xl font-bold mb-2">${product.name}</h3>
                <p class="text-gray-600 mb-4">${product.description}</p>
                <p class="text-2xl font-bold mb-4">¥${Number(product.price).toLocaleString()}</p>
                <form id="add-to-cart-form" onsubmit="addToCart(event, ${product.id})">
                    <input type="hidden" name="product_id" value="${product.id}">
                    <input type="number" name="quantity" value="1" min="1" 
                           class="w-20 p-2 border rounded mr-2">
                    <button type="submit" 
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        カートに追加
                    </button>
                </form>
            `;

            modal.classList.remove('hidden');
        }

        function closeModal() {
            const modal = document.getElementById('modal');
            modal.classList.add('hidden');
        }

        // モーダルの外側をクリックしたら閉じる
        document.getElementById('modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // カートに追加する処を追加
        async function addToCart(event, productId) {
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);

            try {
                const response = await fetch('add_to_cart.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    alert(data.message);
                    closeModal();
                    // カート数の更新があれば実行
                    if (data.cart_count) {
                        updateCartCount(data.cart_count);
                    }
                } else {
                    alert('エラー: ' + data.message);
                }
            } catch (error) {
                alert('エラーが発生しました');
                console.error('Error:', error);
            }
        }

        // カート数を更新する関数（必要に応じて実装）
        function updateCartCount(count) {
            const cartCount = document.getElementById('cart-count');
            if (cartCount) {
                cartCount.textContent = count;
            }
        }
    </script>

    <?php include 'includes/footer.php'; ?>
</body>


</html>

</html>