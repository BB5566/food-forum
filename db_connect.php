<?php
// db_connect.php

// 載入 .env 檔案
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env');
    $servername = $env['DB_SERVER'] ?? '';
    $username = $env['DB_USERNAME'] ?? '';
    $password = $env['DB_PASSWORD'] ?? '';
    $dbname = $env['DB_NAME'] ?? '';
} else {
    die("找不到 .env 檔案");
}

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("連線失敗: " . $conn->connect_error);
}
