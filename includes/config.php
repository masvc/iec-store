// 実際は.git ignoreをする

<?php
// データベース設定
define('DB_HOST', 'localhost');
define('DB_NAME', 'iec');
define('DB_USER', 'root');
define('DB_PASS', '');

// アプリケーション設定
define('SITE_NAME', 'InstaEC');
define('UPLOAD_DIR', 'uploads/');

// 支払い方法
const PAYMENT_METHODS = [
    'credit_card' => 'クレジットカード',
    'bank_transfer' => '銀行振込',
    'convenience_store' => 'コンビニ支払い'
];
