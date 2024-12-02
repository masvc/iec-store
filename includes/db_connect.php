<?php
function get_db_connection()
{
    try {
        $pdo = new PDO(
            "mysql:host=localhost;dbname=iec;charset=utf8mb4",
            "root",
            "",
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        return $pdo;
    } catch (PDOException $e) {
        exit('データベース接続失敗: ' . $e->getMessage());
    }
}
