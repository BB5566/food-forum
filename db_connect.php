<?php
// db_connect.php
// 這個檔案負責建立 MySQL 連線
// 會先嘗試讀取專案根目錄的 .env（如果有的話）
// 要建立 .env，我通常放：
// DB_SERVER=localhost
// DB_USERNAME=root
// DB_PASSWORD=yourpassword
// DB_NAME=food_forum
// 如果沒有 .env，我就直接在下面填入測試用的預設值，方便我本地跑
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env');
    $servername = $env['DB_SERVER'] ?? '';
    $username = $env['DB_USERNAME'] ?? '';
    $password = $env['DB_PASSWORD'] ?? '';
    $dbname = $env['DB_NAME'] ?? '';
} else {
    // 先用這些預設值，測試時再改成自己的設定或建立 .env
    $servername = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'food_forum';
}

// 建立 mysqli 連線
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    // 如果連不上，印出錯誤並停程式，方便修設定
    die("資料庫連線失敗，請檢查設定（DB_SERVER/DB_USERNAME/DB_PASSWORD/DB_NAME）：" . $conn->connect_error);
}
