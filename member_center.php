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
$stmt->execute([(int)$_SESSION['user_id']]);
$member = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>會員中心 - <?= htmlspecialchars($CONFIG->SITE_NAME ?? '美食論壇') ?></title>
  <link rel="stylesheet" href="styles.css?v=<?= file_exists(__DIR__ . '/styles.css') ? filemtime(__DIR__ . '/styles.css') : time() ?>">
</head>

<body>
  <a href="#main-content" class="skip-link">跳至主要內容</a>
  <?php include 'header.php'; ?>
  <main id="main-content" class="simple-main container">
    <div class="post-card member-card">
      <div class="member-center-section">
        <h2 class="form-title">會員中心</h2>
        <?php if ($member): ?>
          <ul class="member-info">
            <li><span class="info-label">會員編號</span><span class="info-value"><?= htmlspecialchars($member['id']) ?></span></li>
            <li><span class="info-label">帳號</span><span class="info-value"><?= htmlspecialchars($member['username']) ?></span></li>
            <li><span class="info-label">暱稱</span><span class="info-value"><?= htmlspecialchars($member['nickname']) ?></span></li>
            <li><span class="info-label">電子郵件</span><span class="info-value"><?= htmlspecialchars($member['email']) ?></span></li>
            <li><span class="info-label">生日</span><span class="info-value"><?= htmlspecialchars($member['birthday']) ?></span></li>
          </ul>

          <div class="member-sub-section">
            <h3 class="member-section-title">我發表的文章</h3>
            <?php
            // 取得我發表的文章
            $stmt2 = $conn->prepare("SELECT id, title, created_at FROM posts WHERE author_id = ? ORDER BY created_at DESC");
            $stmt2->execute([(int)$member['id']]);
            $my_posts = $stmt2->fetchAll();
            ?>
            <?php if ($my_posts): ?>
              <ul class="article-list">
                <?php foreach ($my_posts as $p): ?>
                  <li>
                    <a href="post_detail.php?id=<?= htmlspecialchars((string)$p['id'], ENT_QUOTES, 'UTF-8') ?>">
                      <span class="article-date"><?= htmlspecialchars(date('Y-m-d', strtotime($p['created_at']))) ?></span>
                      <span class="article-title"><?= htmlspecialchars($p['title']) ?></span>
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <div class="empty-tip">尚未發表任何文章</div>
            <?php endif; ?>
          </div>

          <div class="member-sub-section">
            <h3 class="member-section-title">我留言過的文章</h3>
            <?php
            // 取得我留言過的文章
            $stmt3 = $conn->prepare("SELECT DISTINCT posts.id, posts.title, posts.created_at FROM comments JOIN posts ON comments.post_id = posts.id WHERE comments.member_id = ? ORDER BY posts.created_at DESC");
            $stmt3->execute([(int)$member['id']]);
            $commented_posts = $stmt3->fetchAll();
            ?>
            <?php if ($commented_posts): ?>
              <ul class="article-list">
                <?php foreach ($commented_posts as $p): ?>
                  <li>
                    <a href="post_detail.php?id=<?= htmlspecialchars((string)$p['id'], ENT_QUOTES, 'UTF-8') ?>">
                      <span class="article-date"><?= htmlspecialchars(date('Y-m-d', strtotime($p['created_at']))) ?></span>
                      <span class="article-title"><?= htmlspecialchars($p['title']) ?></span>
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <div class="empty-tip">尚未留言過任何文章</div>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <div class="error-message" role="alert">無法取得會員資料，請稍後再試。</div>
        <?php endif; ?>
      </div>
    </div>
  </main>
  <?php include 'footer.php'; ?>
</body>

</html>
