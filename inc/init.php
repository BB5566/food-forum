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
