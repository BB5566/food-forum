
<?php
// 註冊處理（使用 mysqli，風格與其他 API 一致）
require_once __DIR__ . '/../inc/init.php';

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
$stmt = $conn->prepare($sql);
if (!$stmt) {
  echo "<script>alert('資料庫錯誤，請稍後再試');history.back();</script>";
  exit();
}
$stmt->bind_param('sssss', $username, $hash, $nickname, $email, $birthday);
if ($stmt->execute()) {
  echo "<script>alert('註冊成功！');window.location.href='../login.php';</script>";
  exit();
} else {
  if ($conn->errno === 1062) {
    echo "<script>alert('此帳號已被註冊，請換一個帳號。');history.back();</script>";
  } else {
    echo "<script>alert('註冊失敗，請稍後再試。');history.back();</script>";
  }
}
$stmt->close();
$conn->close();
