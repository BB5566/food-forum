<?php
// 管理員用來刪除貼文的 API（簡單、直接）
// 使用共用初始化檔，啟動 session 並取得 $conn
require_once __DIR__ . '/../inc/init.php';

// 檢查是否為管理員，否則回 403
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    http_response_code(403);
    echo '權限不足';
    exit();
}

$post_id = $_POST['post_id'] ?? '';
if (!$post_id || !is_numeric($post_id)) {
    echo '無效的文章編號';
    exit();
}

// 先刪除這篇貼文的所有留言
$stmt1 = $conn->prepare('DELETE FROM comments WHERE post_id = ?');
$stmt1->bind_param('i', $post_id);
$stmt1->execute();
$stmt1->close();

// 再刪除貼文本身
$stmt2 = $conn->prepare('DELETE FROM posts WHERE id = ?');
$stmt2->bind_param('i', $post_id);
if ($stmt2->execute()) {
    header('Location: ../index.php');
    exit();
} else {
    echo '刪除失敗';
}
$stmt2->close();
$conn->close();
