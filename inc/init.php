<?php
// init.php：統一的初始化檔，用來啟動 session 並載入 DB 連線
// 注意路徑：這個檔放在 project_root/inc，透過 __DIR__ 取得正確位置
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// 載入 project root 的 db_connect.php
require_once dirname(__DIR__) . '/db_connect.php';

// 載入 config
$CONFIG = require __DIR__ . '/config.php';

// 在開發模式下，確保 uploads 資料夾存在
if (!empty($CONFIG->DEV_MODE)) {
  $uploadDir = $CONFIG->UPLOAD_DIR;
  if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
  }
}

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!function_exists('csrf_token')) {
  function csrf_token()
  {
    return $_SESSION['csrf_token'] ?? '';
  }
}

if (!function_exists('verify_csrf_token')) {
  function verify_csrf_token($token)
  {
    if (!is_string($token) || $token === '') return false;
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
  }
}

if (!function_exists('require_csrf_token')) {
  function require_csrf_token()
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? '')) {
      http_response_code(403);
      exit('CSRF 驗證失敗，請重新操作。');
    }
  }
}
