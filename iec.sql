-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: localhost
-- 生成日時: 2024 年 12 月 02 日 16:33
-- サーバのバージョン： 10.4.28-MariaDB
-- PHP のバージョン: 8.2.4

-- 注意: このSQLファイルには開発・テスト用のダミーデータが含まれています。
-- 含まれるデータ：
-- - カテゴリーのサンプルデータ
-- - 注文のサンプルデータ
-- - その他テスト用データ
-- これらのデータは開発・テスト目的で使用し、実際の商品・取引とは関係ありません。

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `iec`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- テーブルのデータのダンプ `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'ファッション'),
(2, '電化製品'),
(3, '食品'),
(4, '本・雑誌'),
(5, 'スポーツ・レジャー'),
(6, 'インテリア'),
(7, 'その他');

-- --------------------------------------------------------

--
-- テーブルの構造 `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` int(11) NOT NULL,
  `shipping_name` varchar(100) NOT NULL,
  `shipping_postal_code` varchar(8) NOT NULL,
  `shipping_address` text NOT NULL,
  `shipping_phone` varchar(15) NOT NULL,
  `payment_method` varchar(20) NOT NULL,
  `status` enum('pending','paid','shipped','delivered','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- テーブルのデータのダンプ `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `shipping_name`, `shipping_postal_code`, `shipping_address`, `shipping_phone`, `payment_method`, `status`, `created_at`) VALUES
(1, 4, 2000, 'こ', 'ko', 's', 'ko', 'credit_card', 'pending', '2024-12-02 13:46:32'),
(2, 4, 1000, 'f', 'f', 'f', 'f', 'credit_card', 'pending', '2024-12-02 13:50:50'),
(3, 4, 100, 's', 's', 's', 's', 'credit_card', 'pending', '2024-12-02 13:52:05'),
(4, 4, 100, 'd', 'dd', 'd', 'd', 'convenience_store', 'pending', '2024-12-02 14:02:48'),
(5, 4, 3200, '魔', 'm', 's', 'あ', 'credit_card', 'pending', '2024-12-02 14:20:57'),
(6, 4, 23800, 's', 's', 's', 's', 'credit_card', 'pending', '2024-12-02 14:21:48'),
(7, 4, 9800, 'c', 'c', 'c', 'c', 'credit_card', 'pending', '2024-12-02 14:22:22'),
(8, 4, 52400, 'd', 'd', 'd', 'd', 'bank_transfer', 'pending', '2024-12-02 15:33:18');

-- --------------------------------------------------------

--
-- テーブルの構造 `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- テーブルのデータのダンプ `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`, `price`, `created_at`) VALUES
(1, 5, 10, 1, 3200, '2024-12-02 14:20:57'),
(2, 6, 17, 1, 23800, '2024-12-02 14:21:48'),
(3, 7, 3, 1, 9800, '2024-12-02 14:22:22'),
(4, 8, 12, 1, 4800, '2024-12-02 15:33:18'),
(5, 8, 17, 2, 23800, '2024-12-02 15:33:18');

-- --------------------------------------------------------

--
-- テーブルの構造 `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` int(11) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `status` enum('active','sold','hidden') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- テーブルのデータのダンプ `products`
--

INSERT INTO `products` (`id`, `user_id`, `name`, `description`, `price`, `image_path`, `status`, `created_at`, `updated_at`, `category_id`) VALUES
(1, 4, 'デニムジャケット', '上質な日本製デニム使用。カジュアルからきれいめまで幅広く活用可能。', 12800, 'https://picsum.photos/id/14/200', 'active', '2024-12-02 14:20:08', '2024-12-02 14:20:08', 1),
(2, 5, 'ニットセーター', '100%メリノウール使用の高級ニット。暖かく着心地抜群。', 8900, 'https://picsum.photos/id/15/200', 'active', '2024-12-02 14:20:08', '2024-12-02 14:20:08', 1),
(3, 6, 'スニーカー', 'クッション性抜群のランニングシューズ。普段使いにも最適。', 9800, 'https://picsum.photos/id/16/200', 'active', '2024-12-02 14:20:08', '2024-12-02 14:20:08', 1),
(4, 1, 'メカニカルキーボード', '人気のCherry MXスイッチを採用した高品質キーボード。タイピング音が心地よく、打鍵感も抜群です。', 12800, 'https://picsum.photos/id/1/200', 'active', '2024-12-02 14:20:08', '2024-12-02 14:20:08', 2),
(5, 2, 'ワイヤレスイヤホン', 'ノイズキャンセリング機能付きの最新型イヤホン。バッテリー持続時間は約8時間。', 19800, 'https://picsum.photos/id/2/200', 'active', '2024-12-02 14:20:08', '2024-12-02 14:20:08', 2),
(6, 3, '4Kモニター', '32インチの大画面で作業効率アップ。HDR対応で映像も美しく表示。', 45800, 'https://picsum.photos/id/4/200', 'active', '2024-12-02 14:20:08', '2024-12-02 14:20:08', 2),
(7, 7, '高級コーヒー豆', '希少な産地から直輸入した最高級コーヒー豆。深い香りと豊かな味わい。', 3800, 'https://picsum.photos/id/20/200', 'active', '2024-12-02 14:20:08', '2024-12-02 14:20:08', 3),
(8, 8, 'オーガニック野菜セット', '無農薬栽培の新鮮野菜の詰め合わせ。週替わりで旬の野菜をお届け。', 4200, 'https://picsum.photos/id/21/200', 'active', '2024-12-02 14:20:08', '2024-12-02 14:20:08', 3),
(9, 9, '手作りクッキーセット', '厳選した材料で作る本格的なクッキー。贈り物にも最適。', 2800, 'https://picsum.photos/id/22/200', 'active', '2024-12-02 14:20:08', '2024-12-02 14:20:08', 3),
(10, 10, 'プログラミング入門書', '初心者でも分かりやすい解説と実践的な例題で学べる。', 3200, 'https://picsum.photos/id/8/200', 'active', '2024-12-02 14:20:08', '2024-12-02 14:20:08', 4),
(11, 1, 'ビジネス戦略の教科書', 'ベストセラー著者による最新のビジネス戦略論。', 2800, 'https://picsum.photos/id/9/200', 'active', '2024-12-02 14:20:08', '2024-12-02 14:20:08', 4),
(12, 2, '写真集', '世界の絶景を収めた美しい写真集。', 4800, 'https://picsum.photos/id/11/200', 'active', '2024-12-02 14:20:08', '2024-12-02 14:20:08', 4),
(13, 3, 'ヨガマット', '厚さ10mmの高品質ヨガマット。滑り止め付きで安全な運動をサポート。', 3800, 'https://picsum.photos/id/23/200', 'active', '2024-12-02 14:20:08', '2024-12-02 14:20:08', 5),
(14, 4, 'テニスラケット', 'プロ仕様の軽量カーボンラケット。初中級者にも扱いやすい。', 15800, 'https://picsum.photos/id/24/200', 'active', '2024-12-02 14:20:08', '2024-12-02 14:20:08', 5),
(15, 5, 'キャンプテント', '3〜4人用の大型テント。防水性能が高く、設営も簡単。', 28000, 'https://picsum.photos/id/25/200', 'active', '2024-12-02 14:20:08', '2024-12-02 14:20:08', 5),
(16, 6, '観葉植物', '空気清浄効果のある人気の観葉植物。育てやすい品種を厳選。', 4800, 'https://picsum.photos/id/26/200', 'active', '2024-12-02 14:20:08', '2024-12-02 14:20:08', 6),
(17, 7, 'デザイナーズチェア', '北欧デザインの木製チェア。座り心地と見た目を両立。', 23800, 'https://picsum.photos/id/27/200', 'active', '2024-12-02 14:20:08', '2024-12-02 14:20:08', 6),
(18, 8, 'LEDフロアライト', '調光可能なLEDライト。インテリアとしても映える洗練されたデザイン。', 12800, 'https://picsum.photos/id/28/200', 'active', '2024-12-02 14:20:08', '2024-12-02 14:20:08', 6),
(19, 9, 'アロマディフューザー', '超音波式のアロマディフューザー。7色のLEDライト付き。', 5800, 'https://picsum.photos/id/29/200', 'active', '2024-12-02 14:20:08', '2024-12-02 14:20:08', 7),
(20, 10, 'ガーデニングツールセット', '初心者向けの園芸用具セット。必要な道具が一通り揃う。', 6800, 'https://picsum.photos/id/30/200', 'active', '2024-12-02 14:20:08', '2024-12-02 14:20:08', 7),
(21, 1, '手帳カバー', '本革使用の高級手帳カバー。A5サイズ対応。', 4800, 'https://picsum.photos/id/31/200', 'active', '2024-12-02 14:20:08', '2024-12-02 14:20:08', 7);

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- テーブルのデータのダンプ `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`, `updated_at`) VALUES
(1, 'tanaka_yuki', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', '2024-12-02 14:12:09', '2024-12-02 14:12:09'),
(2, 'suzuki_mai', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', '2024-12-02 14:12:09', '2024-12-02 14:12:09'),
(3, 'sato_ken', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', '2024-12-02 14:12:09', '2024-12-02 14:12:09'),
(4, 'yamamoto_rin', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', '2024-12-02 14:12:09', '2024-12-02 14:12:09'),
(5, 'nakamura_hiro', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', '2024-12-02 14:12:09', '2024-12-02 14:12:09'),
(6, 'kato_mika', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', '2024-12-02 14:12:09', '2024-12-02 14:12:09'),
(7, 'ito_daisuke', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', '2024-12-02 14:12:09', '2024-12-02 14:12:09'),
(8, 'watanabe_aki', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', '2024-12-02 14:12:09', '2024-12-02 14:12:09'),
(9, 'kimura_sho', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', '2024-12-02 14:12:09', '2024-12-02 14:12:09'),
(10, 'saito_yui', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', '2024-12-02 14:12:09', '2024-12-02 14:12:09');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- テーブルのインデックス `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- テーブルのインデックス `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- テーブルのインデックス `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- テーブルのインデックス `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- テーブルの AUTO_INCREMENT `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- テーブルの AUTO_INCREMENT `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- テーブルの AUTO_INCREMENT `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- テーブルの AUTO_INCREMENT `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- テーブルの AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- テーブルの制約 `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- テーブルの制約 `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `products_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
