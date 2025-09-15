<?php
// 使用共用初始化檔以統一 session 與 DB
require_once __DIR__ . '/inc/init.php';
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}
// 以 mysqli 取得會員資料，這樣跟其他 API 的風格一致，會比較容易給新手理解
$member = null;
$sql = "SELECT * FROM members WHERE id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
if ($stmt) {
  $stmt->bind_param('i', $_SESSION['user_id']);
  $stmt->execute();
  $res = $stmt->get_result();
  $member = $res->fetch_assoc();
  $stmt->close();
} else {
  $member = null;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>美食論壇</title>
  <link rel="stylesheet" href="styles.css?v=<?= file_exists(__DIR__ . '/styles.css') ? filemtime(__DIR__ . '/styles.css') : time() ?>">
</head>

<body>
  <?php include 'header.php'; ?>
  <main class="forum-main container">
    <section class="forum-content">
      <div class="post-card" style="max-width:520px;margin:0 auto;">
        <div class="member-center-section">
          <h2>會員中心</h2>
          <?php if ($member): ?>
            <ul class="member-info">
              <li><strong>會員ID：</strong> <?= htmlspecialchars($member['id']) ?></li>
              <li><strong>帳號：</strong> <?= htmlspecialchars($member['username']) ?></li>
              <li><strong>暱稱：</strong> <?= htmlspecialchars($member['nickname']) ?></li>
              <li><strong>電子郵件：</strong> <?= htmlspecialchars($member['email']) ?></li>
              <li><strong>生日：</strong> <?= htmlspecialchars($member['birthday']) ?></li>
            </ul>
            <hr>
            <h3>我發表的文章</h3>
            <?php
            // 取得我發表的文章（mysqli）
            $stmt2 = $conn->prepare("SELECT id, title, created_at FROM posts WHERE author_id = ? ORDER BY created_at DESC");
            $stmt2->bind_param('i', $member['id']);
            $stmt2->execute();
            $res2 = $stmt2->get_result();
            $my_posts = $res2->fetch_all(MYSQLI_ASSOC);
            $stmt2->close();
            ?>
            <?php if ($my_posts): ?>
              <ul class="article-list">
                <?php foreach ($my_posts as $p): ?>
                  <li><a href="post_detail.php?id=<?= $p['id'] ?>">[<?= htmlspecialchars(date('Y-m-d', strtotime($p['created_at']))) ?>] <?= htmlspecialchars($p['title']) ?></a></li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <div class="empty-tip">尚未發表任何文章</div>
            <?php endif; ?>
            <hr>
            <h3>我留言過的文章</h3>
            <?php
            // 取得我留言過的文章（mysqli）
            $stmt3 = $conn->prepare("SELECT DISTINCT posts.id, posts.title, posts.created_at FROM comments JOIN posts ON comments.post_id = posts.id WHERE comments.member_id = ? ORDER BY posts.created_at DESC");
            $stmt3->bind_param('i', $member['id']);
            $stmt3->execute();
            $res3 = $stmt3->get_result();
            $commented_posts = $res3->fetch_all(MYSQLI_ASSOC);
            $stmt3->close();
            ?>
            <?php if ($commented_posts): ?>
              <ul class="article-list">
                <?php foreach ($commented_posts as $p): ?>
                  <li><a href="post_detail.php?id=<?= $p['id'] ?>">[<?= htmlspecialchars(date('Y-m-d', strtotime($p['created_at']))) ?>] <?= htmlspecialchars($p['title']) ?></a></li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <div class="empty-tip">尚未留言過任何文章</div>
            <?php endif; ?>
          <?php else: ?>
            <div style="color:#d32f2f;">無法取得會員資料，請稍後再試。</div>
          <?php endif; ?>
        </div>
      </div>
    </section>
  </main>
  <?php
  // Include footer
  include 'footer.php'; ?>
</body>

</html>
