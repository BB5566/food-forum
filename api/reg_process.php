
<?php
// 註冊處理（使用 mysqli，風格與其他 API 一致）
require_once __DIR__ . '/../inc/init.php';
require_csrf_token();

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$nickname = trim($_POST['nickname'] ?? '');
$email = trim($_POST['email'] ?? '');
$birthday = trim($_POST['birthday'] ?? '');

if ($username === '' || $password === '') {
  echo "<script>alert('請填寫帳號與密碼');history.back();</script>";
  exit();
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO members (username, password, nickname, email, birthday) VALUES (?, ?, ?, ?, ?)";
try {
  $stmt = $conn->prepare($sql);
  $stmt->execute([$username, $hash, $nickname, $email, $birthday]);
  echo "<script>alert('註冊成功！');window.location.href='../login.php';</script>";
  exit();
} catch (PDOException $e) {
  $sqlState = $e->errorInfo[0] ?? '';
  if ($sqlState === '23000') {
    echo "<script>alert('此帳號已被註冊，請換一個帳號。');history.back();</script>";
  } else {
    echo "<script>alert('註冊失敗，請稍後再試。');history.back();</script>";
  }
}
