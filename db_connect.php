<?php
/**
 * Food Forum 資料庫連線
 * 支援 SQLite 和 MySQL 雙模式
 * 一律使用 PDO 與 prepared statements
 */

// 載入環境變數
$envFile = __DIR__ . '/.env';
$env = [];
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, '"\'');
            $env[$key] = $value;
        }
    }
}

// 讀取資料庫類型，預設使用 SQLite
$dbType = strtolower($env['DB_TYPE'] ?? 'sqlite');

try {
    if ($dbType === 'sqlite') {
        $dbPath = $env['DB_PATH'] ?? __DIR__ . '/database/food_forum.sqlite';
        $dbDir = dirname($dbPath);
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0755, true);
        }

        $dbExists = file_exists($dbPath);
        $pdo = new PDO("sqlite:$dbPath");
        $pdo->exec('PRAGMA foreign_keys = ON;');

        if (!$dbExists) {
            $schemaFile = __DIR__ . '/database/schema.sqlite.sql';
            if (file_exists($schemaFile)) {
                $sql = file_get_contents($schemaFile);
                $pdo->exec($sql);
            }
        }
    } else {
        $servername = $env['DB_SERVER'] ?? 'localhost';
        $username = $env['DB_USERNAME'] ?? 'root';
        $password = $env['DB_PASSWORD'] ?? '';
        $dbname = $env['DB_NAME'] ?? 'food_forum';
        $charset = $env['DB_CHARSET'] ?? 'utf8mb4';

        $dsn = "mysql:host={$servername};dbname={$dbname};charset={$charset}";
        $pdo = new PDO($dsn, $username, $password);
    }

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $conn = $pdo; // 保留既有變數名稱
} catch (PDOException $e) {
    die('資料庫連線失敗，請檢查設定：' . $e->getMessage());
}
