<?php
// env_check.php
// 簡單的環境檢查腳本，用於確認 PHP 與檔案系統設定是否符合本專案需求。
// 使用方法：把這個檔案上傳到專案根目錄下的 scripts/，用瀏覽器開啟或在 CLI 下執行 php scripts/env_check.php

// 輸出工具：支援 CLI 與 HTML
$isCli = (php_sapi_name() === 'cli');

function out_ok($msg)
{
  global $isCli;
  if ($isCli) echo "[OK]    " . $msg . "\n";
  else echo "<div class=\"row ok\">" . htmlspecialchars($msg) . "</div>\n";
}

function out_warn($msg)
{
  global $isCli;
  if ($isCli) echo "[WARN]  " . $msg . "\n";
  else echo "<div class=\"row warn\">" . htmlspecialchars($msg) . "</div>\n";
}

function out_fail($msg)
{
  global $isCli;
  if ($isCli) echo "[FAIL]  " . $msg . "\n";
  else echo "<div class=\"row fail\">" . htmlspecialchars($msg) . "</div>\n";
}

$exitCode = 0;

// 1) PHP 設定
$upload_max = ini_get('upload_max_filesize');
$post_max = ini_get('post_max_size');
$memory_limit = ini_get('memory_limit');

if ($upload_max) out_ok("upload_max_filesize = {$upload_max}");
else {
  out_fail('upload_max_filesize not set');
  $exitCode = 1;
}
if ($post_max) out_ok("post_max_size = {$post_max}");
else {
  out_fail('post_max_size not set');
  $exitCode = 1;
}
if ($memory_limit) out_ok("memory_limit = {$memory_limit}");
else {
  out_warn('memory_limit not set');
}

// 2) GD / Imagick
$gd = extension_loaded('gd');
$imagick = extension_loaded('imagick');
if ($gd) out_ok('GD extension is available');
else out_warn('GD extension is not available');
if ($imagick) out_ok('Imagick extension is available');

// 3) uploads/ 可寫入性
$uploadsDir = __DIR__ . '/../uploads';
if (file_exists($uploadsDir)) {
  if (is_writable($uploadsDir)) {
    out_ok("uploads dir exists and is writable: {$uploadsDir}");
  } else {
    out_fail("uploads dir exists but is NOT writable: {$uploadsDir}");
    $exitCode = 2;
  }
} else {
  out_warn("uploads dir does not exist: {$uploadsDir} (the app may create it in DEV_MODE)");
}

// 4) .env 檔案檢查
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
  out_ok('.env file found');
} else {
  out_warn('.env file not found in project root');
}

// 5) DB connection quick check (only if .env exists)
if (file_exists($envFile)) {
  $cfg = parse_ini_file($envFile) ?: [];
  $server = $cfg['DB_SERVER'] ?? 'localhost';
  $user = $cfg['DB_USERNAME'] ?? null;
  $pass = $cfg['DB_PASSWORD'] ?? null;
  $name = $cfg['DB_NAME'] ?? null;
  if ($user && $name) {
    // try a mysqli connect with short timeout
    mysqli_options($link = mysqli_init(), MYSQLI_OPT_CONNECT_TIMEOUT, 5);
    if (@mysqli_real_connect($link, $server, $user, $pass, $name)) {
      out_ok('DB connection successful (using .env credentials)');
      mysqli_close($link);
    } else {
      out_fail('DB connection failed with .env credentials: ' . mysqli_connect_error());
      $exitCode = 3;
    }
  } else {
    out_warn('Incomplete DB credentials in .env (skipping DB connect test)');
  }
}

// 結果
if ($isCli) {
  if ($exitCode === 0) out_ok('Environment check passed');
  else out_fail('Environment check found issues (exit code: ' . $exitCode . ')');
  // CLI: 使用 exit code
  exit($exitCode);
} else {
  // 瀏覽器：包 HTML 並顯示結果區塊
  echo "<!doctype html><html><head><meta charset=\"utf-8\"><title>Env Check</title>";
  echo "<style>body{font-family:Arial,Helvetica,sans-serif;padding:16px} .row{padding:8px;border-radius:4px;margin:6px 0} .ok{background:#e6ffed;border:1px solid #b7f0c8} .warn{background:#fff7e6;border:1px solid #ffecb8} .fail{background:#ffecec;border:1px solid #f5b6b6} .note{color:#666;margin-top:12px;font-size:90%}</style>";
  echo "</head><body><h2>Environment check</h2>\n";
  // 重新執行輸出呼叫區塊：腳本上方已經直接 echo 出來的 row，這裡只顯示 exit code 訊息
  if ($exitCode === 0) echo '<div class="row ok">Environment check passed</div>';
  else echo '<div class="row fail">Environment check found issues (exit code: ' . htmlspecialchars((string)$exitCode) . ')</div>';
  echo "<div class=\"note\">This page is a quick check for PHP configuration (CLI output available if you run <code>php scripts/env_check.php</code>).</div>";
  echo "<!-- Exit code: {$exitCode} -->";
  echo "</body></html>";
}
