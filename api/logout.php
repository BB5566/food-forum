<?php
// 使用共用初始化檔來確保 session 已啟動
require_once __DIR__ . '/../inc/init.php';

// 清空 session 變數
$_SESSION = array();
// 如果使用 cookie 存 session，就把 cookie 刪掉
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}
// 真正銷毀 session
session_destroy();
// 登出後導回登入頁
header("Location: ../login.php");
exit();
