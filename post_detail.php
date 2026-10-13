<?php
// 使用共用初始化檔，確保 session 與 $conn 可用
require_once __DIR__ . '/inc/init.php';
require_once __DIR__ . '/inc/sanitize.php';
$id = $_GET['id'] ?? '';
if (!$id || !is_numeric($id)) {
  echo '<h2>無效的文章編號</h2>';
  exit;
}
// 取得文章內容
$sql = "SELECT posts.*, members.nickname FROM posts JOIN members ON posts.author_id = members.id WHERE posts.id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([(int)$id]);
$post = $stmt->fetch();
if (!$post) {
  echo '<h2>找不到此文章</h2>';
  exit;
}
// 取得留言
$comments = [];
$sql_c = "SELECT comments.*, members.nickname FROM comments JOIN members ON comments.member_id = members.id WHERE comments.post_id = ? ORDER BY comments.created_at ASC";
$stmt_c = $conn->prepare($sql_c);
$stmt_c->execute([(int)$id]);
$comments = $stmt_c->fetchAll();
// 使用共用的 sanitize_html()（位於 inc/sanitize.php）

?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($post['title']) ?> - 美食論壇</title>
  <link rel="stylesheet" href="styles.css?v=<?= file_exists(__DIR__ . '/styles.css') ? filemtime(__DIR__ . '/styles.css') : time() ?>">
</head>

<body>
  <a href="#main-content" class="skip-link">跳至主要內容</a>
  <?php include 'header.php'; ?>
  <main id="main-content" class="simple-main container">
    <div class="post-detail-wrap">
      <div class="post-back">
        <a href="index.php" class="back-link">← 返回文章列表</a>
      </div>
      <article class="post-detail">
        <h2><?= htmlspecialchars($post['title']) ?></h2>
        <div class="post-category"><?= htmlspecialchars($post['category']) ?></div>
        <div class="post-meta">by <?= htmlspecialchars($post['nickname']) ?> | <?= htmlspecialchars(date('Y-m-d', strtotime($post['created_at']))) ?></div>
        <?php if (!empty($post['image'])): ?>
          <?php if (strpos($post['image'], 'data:image/') === 0): ?>
            <img src="<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="post-image">
          <?php else:
            $uploadUrl = $CONFIG->UPLOAD_URL ?? 'uploads';
            $thumbName = ($CONFIG->THUMB_PREFIX ?? 'thumb_') . $post['image'];
            $thumbPath = $uploadUrl . '/' . $thumbName;
            $origPath = $uploadUrl . '/' . $post['image'];
            // 如果縮圖存在則優先顯示（簡單檢查），否則顯示原圖
            $displaySrc = file_exists(__DIR__ . '/' . $thumbPath) ? $thumbPath : $origPath;
          ?>
            <img loading="lazy" src="<?= htmlspecialchars($displaySrc) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="post-image">
          <?php endif; ?>
        <?php endif; ?>

        <div class="post-content"><?= sanitize_html($post['content']) ?></div>
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
          <form action="api/delete_post.php" method="post" onsubmit="return confirm('確定要刪除這篇貼文嗎？此動作無法復原！');" class="admin-action-form">
            <input type="hidden" name="post_id" value="<?= htmlspecialchars((string)$post['id'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="btn btn-danger">刪除貼文（管理員）</button>
          </form>
        <?php endif; ?>
      </article>

      <section class="comments post-card" aria-label="文章留言討論區">
        <h3 class="comments-title">討論區</h3>
        <?php if (count($comments) > 0): ?>
          <div class="comment-list">
            <?php foreach ($comments as $c): ?>
              <div class="comment">
                <div class="comment-meta">
                  <span class="comment-author">💬 <?= htmlspecialchars($c['nickname']) ?></span>
                  <span class="comment-date"><?= htmlspecialchars($c['created_at']) ?></span>
                </div>
                <div class="comment-content"><?= nl2br(htmlspecialchars($c['content'])) ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="empty-tip">目前尚無留言，歡迎搶頭香！</div>
        <?php endif; ?>

        <div class="comment-reply-box">
          <h4 class="comment-reply-title">發表回覆</h4>
          <form action="api/add_comment.php" method="post" class="comment-form">
            <input type="hidden" name="post_id" value="<?= htmlspecialchars((string)$post['id'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
            <div class="form-group">
              <label for="comment-content" class="form-label">回覆內容：</label>
              <textarea id="comment-content" name="content" rows="3" required placeholder="輸入留言內容..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary">送出留言</button>
          </form>
        </div>
      </section>
    </div>
  </main>
  <?php include 'footer.php'; ?>
</body>

</html>