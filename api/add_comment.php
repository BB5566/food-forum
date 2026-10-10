<?php
// 處理發表留言的請求（簡單版）
// 使用 inc/init.php 統一初始化（session + $conn）
require_once __DIR__ . '/../inc/init.php';

// 若沒登入就導回登入頁
if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
  exit();
}
require_csrf_token();

$post_id = (int)($_POST['post_id'] ?? 0);
$content = trim($_POST['content'] ?? '');
$member_id = (int)$_SESSION['user_id'];

// 檢查必要欄位
if ($post_id <= 0 || $content === '') {
  echo '請填寫留言內容';
  exit();
}

// 用 prepared statement 寫入留言
$sql = "INSERT INTO comments (post_id, member_id, content) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$post_id, $member_id, $content]);
if ($stmt->rowCount() > 0) {
  header('Location: ../post_detail.php?id=' . $post_id);
  exit();
} else {
  echo '留言失敗，請稍後再試。';
}
