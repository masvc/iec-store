<?php
// セッションチェック
function require_login()
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
}

// XSS対策
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// 価格のフォーマット
function format_price($price)
{
    return '¥' . number_format($price);
}

// エラーメッセージの表示
function show_error($message)
{
    return '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">'
        . h($message) .
        '</div>';
}

// 成功メッセージの表示
function show_success($message)
{
    return '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">'
        . h($message) .
        '</div>';
}
