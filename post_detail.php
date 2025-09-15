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
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();
if (!$post) {
  echo '<h2>找不到此文章</h2>';
  exit;
}
// 取得留言
$comments = [];
$sql_c = "SELECT comments.*, members.nickname FROM comments JOIN members ON comments.member_id = members.id WHERE comments.post_id = ? ORDER BY comments.created_at ASC";
$stmt_c = $conn->prepare($sql_c);
$stmt_c->bind_param('i', $id);
$stmt_c->execute();
$result_c = $stmt_c->get_result();
while ($row = $result_c->fetch_assoc()) {
  $comments[] = $row;
}
// 使用共用的 sanitize_html()（位於 inc/sanitize.php）

?>
<!DOCTYPE html>
<html lang="zh-TW">

<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($post['title']) ?> - 美食論壇</title>
  <link rel="stylesheet" href="styles.css?v=<?= file_exists(__DIR__ . '/styles.css') ? filemtime(__DIR__ . '/styles.css') : time() ?>">
</head>

<body>
  <?php include 'header.php'; ?>
  <main class="container">
    <div class="post-detail">
      <h2><?= htmlspecialchars($post['title']) ?></h2>
      <div style="color:#ffd54f;font-size:0.98rem;margin-bottom:6px;">[<?= htmlspecialchars($post['category']) ?>]</div>
      <div class="post-meta">by <?= htmlspecialchars($post['nickname']) ?> | <?= htmlspecialchars(date('Y-m-d', strtotime($post['created_at']))) ?></div>
      <?php if (!empty($post['image'])): ?>
        <?php if (strpos($post['image'], 'data:image/') === 0): ?>
          <img src="<?= htmlspecialchars($post['image']) ?>" alt="文章圖片" class="post-image">
        <?php else:
          $uploadUrl = $CONFIG->UPLOAD_URL ?? 'uploads';
          $thumbName = ($CONFIG->THUMB_PREFIX ?? 'thumb_') . $post['image'];
          $thumbPath = $uploadUrl . '/' . $thumbName;
          $origPath = $uploadUrl . '/' . $post['image'];
          // 如果縮圖存在則優先顯示（簡單檢查），否則顯示原圖
          $displaySrc = file_exists(__DIR__ . '/' . $thumbPath) ? $thumbPath : $origPath;
        ?>
          <img loading="lazy" src="<?= htmlspecialchars($displaySrc) ?>" alt="文章圖片" class="post-image">
        <?php endif; ?>
      <?php endif; ?>

      <div class="post-content"><?= sanitize_html($post['content']) ?></div>
      <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
        <div style="margin-top:12px;padding:8px;border:1px dashed #ccc;background:#fff;">
          <strong>DEBUG (admin only)</strong>
          <div>原始內容（raw from DB, escaped for safety）:</div>
          <pre style="white-space:pre-wrap;"><?= htmlspecialchars($post['content']) ?></pre>
          <div>sanitize_html 處理後（escaped show）:</div>
          <pre style="white-space:pre-wrap;"><?= htmlspecialchars(sanitize_html($post['content'])) ?></pre>
        </div>
      <?php endif; ?>
      <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
        <form action="api/delete_post.php" method="post" onsubmit="return confirm('確定要刪除這篇貼文嗎？此動作無法復原！');" style="margin-top:16px;">
          <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
          <button type="submit" style="background:#d32f2f;color:#fff;padding:8px 18px;border:none;border-radius:6px;font-weight:700;cursor:pointer;">刪除貼文（管理員）</button>
        </form>
      <?php endif; ?>
    </div>
    <hr>
    <section class="comments">
      <h3>討論區</h3>
      <?php if (count($comments) > 0): ?>
        <?php foreach ($comments as $c): ?>
          <div class="comment">
            <div class="comment-meta"><?= htmlspecialchars($c['nickname']) ?> | <?= htmlspecialchars($c['created_at']) ?></div>
            <div class="comment-content"><?= nl2br(htmlspecialchars($c['content'])) ?></div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="empty-tip">目前尚無留言，歡迎搶頭香！</div>
      <?php endif; ?>
      <hr>
      <h4 style="color:#ffd54f;">發表回覆</h4>
      <form action="api/add_comment.php" method="post">
        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
        <textarea name="content" rows="3" required placeholder="輸入留言內容..."></textarea><br>
        <button type="submit">送出留言</button>
      </form>
    </section>
  </main>
  <?php $conn->close(); ?>
  <?php include 'footer.php'; ?>
  <script>
    // Debug: log delete form action and capture submit attempts
    (function() {
      try {
        const delForm = document.querySelector('form[action="api/delete_post.php"]');
        if (delForm) {
          console.log('Delete form action (raw):', delForm.getAttribute('action'));
          delForm.addEventListener('submit', function(e) {
            console.log('Delete form submit triggered. action=', delForm.action, 'method=', delForm.method);
          });
        } else {
          console.log('No admin delete form found on this page.');
        }
      } catch (err) {
        console.error('post_detail debug error:', err);
      }
    })();
  </script>
</body>

</html>
