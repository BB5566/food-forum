<?php
// 登入處理（使用 mysqli，保持簡單一致）
require_once __DIR__ . '/../inc/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit();
}
require_csrf_token();

$username_input = $_POST['username'] ?? '';
$password_input = $_POST['password'] ?? '';

if ($username_input === '' || $password_input === '') {
    header('Location: ../login.php?error=請輸入帳號密碼');
    exit();
}

// 以 PDO prepared statement 取得使用者
$sql = "SELECT id, username, password, nickname, is_admin FROM members WHERE username = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute([$username_input]);
$user = $stmt->fetch();

if ($user && password_verify($password_input, $user['password'])) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['nickname'] = $user['nickname'];
    $_SESSION['is_admin'] = $user['is_admin'] ?? 0;
    header('Location: ../member_center.php');
    exit();
} else {
    header('Location: ../login.php?error=帳號或密碼錯誤');
    exit();
}
