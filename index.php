<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>美食論壇</title>
  <link rel="stylesheet" href="styles.css?v=<?= file_exists(__DIR__ . '/styles.css') ? filemtime(__DIR__ . '/styles.css') : time() ?>">
</head>

<body>

  <?php
  // 初始化並載入共用 template
  require_once __DIR__ . '/inc/init.php';
  require_once __DIR__ . '/inc/template.php';

  include 'header.php';

  $category = $_GET['category'] ?? '';
  $page = max(1, intval($_GET['page'] ?? 1));
  $pageSize = $CONFIG->PAGE_SIZE ?? 10;
  $offset = ($page - 1) * $pageSize;

  // -- Refactored Data Fetching Logic --
  $allowedCategories = $CONFIG->CATEGORIES ?? [];
  $isCategoryFiltered = $category && in_array($category, $allowedCategories);

  // Base SQL queries
  $sqlBase = "FROM posts JOIN members ON posts.author_id = members.id";
  $sqlWhere = $isCategoryFiltered ? "WHERE posts.category = ?" : "";

  // Get total count for pagination
  $countSql = "SELECT COUNT(*) as c {$sqlBase} {$sqlWhere}";
  $countStmt = $conn->prepare($countSql);
  if ($isCategoryFiltered) {
    $countStmt->bind_param('s', $category);
  }
  $countStmt->execute();
  $total = $countStmt->get_result()->fetch_assoc()['c'] ?? 0;
  $countStmt->close();
  $totalPages = max(1, ceil($total / $pageSize));

  // Get posts for the current page
  $postsSql = "SELECT posts.*, members.nickname {$sqlBase} {$sqlWhere} ORDER BY created_at DESC LIMIT ?, ?";
  $postsStmt = $conn->prepare($postsSql);
  if ($isCategoryFiltered) {
    $postsStmt->bind_param('sii', $category, $offset, $pageSize);
  } else {
    $postsStmt->bind_param('ii', $offset, $pageSize);
  }
  $postsStmt->execute();
  $result = $postsStmt->get_result();
  // -- End of Refactored Logic --
  ?>
  <main class="forum-main container">
    <aside class="forum-sidebar">
      <h2>分類</h2>
      <ul>
        <li><a href="index.php">全部</a></li>
        <?php foreach ($CONFIG->CATEGORIES as $cat): ?>
          <li><a href="?category=<?= urlencode($cat) ?>"><?= htmlspecialchars($cat) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </aside>
    <section class="forum-content">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <?= render_post_card($row, $CONFIG) /* Pass $CONFIG object */ ?>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="post-card">目前尚無文章，歡迎發表第一篇！</div>
      <?php endif; ?>
      <div class="pagination">
        <?php if ($page > 1): ?>
          <a href="?<?= $category ? 'category=' . urlencode($category) . '&' : '' ?>page=<?= $page - 1 ?>">上一頁</a>
        <?php endif; ?>
        <span>第 <?= $page ?> 頁 / 共 <?= $totalPages ?> 頁</span>
        <?php if ($page < $totalPages): ?>
          <a href="?<?= $category ? 'category=' . urlencode($category) . '&' : '' ?>page=<?= $page + 1 ?>">下一頁</a>
        <?php endif; ?>
      </div>
    </section>
  </main>
  <?php $conn->close(); ?>
  <?php include 'footer.php'; ?>
</body>

</html>
