<?php
// 處理發表留言的請求（簡單版）
// 使用 inc/init.php 統一初始化（session + $conn）
require_once __DIR__ . '/../inc/init.php';

// 若沒登入就導回登入頁
if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
  exit();
}

$post_id = $_POST['post_id'] ?? '';
$content = trim($_POST['content'] ?? '');
$member_id = $_SESSION['user_id'];

// 檢查必要欄位
if (!$post_id || !$content) {
  echo '請填寫留言內容';
  exit();
}

// 用 prepared statement 寫入留言
$sql = "INSERT INTO comments (post_id, member_id, content) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iis', $post_id, $member_id, $content);
if ($stmt->execute()) {
  header('Location: ../post_detail.php?id=' . $post_id);
  exit();
} else {
  echo '留言失敗，請稍後再試。';
}
$stmt->close();
$conn->close();
