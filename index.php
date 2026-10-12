<!DOCTYPE html>
<html lang="zh-Hant-TW">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>美食論壇</title>
  <link rel="stylesheet" href="styles.css?v=<?= file_exists(__DIR__ . '/styles.css') ? filemtime(__DIR__ . '/styles.css') : time() ?>">
</head>

<body>
  <a href="#main-content" class="skip-link">跳至主要內容</a>

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
    $countStmt->execute([$category]);
  } else {
    $countStmt->execute();
  }
  $total = $countStmt->fetch()['c'] ?? 0;
  $totalPages = max(1, ceil($total / $pageSize));

  // Get posts for the current page
  $postsSql = "SELECT posts.*, members.nickname {$sqlBase} {$sqlWhere} ORDER BY created_at DESC LIMIT ?, ?";
  $postsStmt = $conn->prepare($postsSql);
  if ($isCategoryFiltered) {
    $postsStmt->execute([$category, $offset, $pageSize]);
  } else {
    $postsStmt->execute([$offset, $pageSize]);
  }
  $posts = $postsStmt->fetchAll();
  // -- End of Refactored Logic --
  ?>
  <main id="main-content" class="forum-main container">
    <aside class="forum-sidebar" aria-label="文章分類">
      <h2>分類</h2>
      <ul>
        <li><a href="index.php" class="<?= empty($category) ? 'active' : '' ?>"<?= empty($category) ? ' aria-current="page"' : '' ?>>全部</a></li>
        <?php foreach ($CONFIG->CATEGORIES as $cat): ?>
          <?php $isCatActive = ($category === $cat); ?>
          <li><a href="?category=<?= urlencode($cat) ?>" class="<?= $isCatActive ? 'active' : '' ?>"<?= $isCatActive ? ' aria-current="page"' : '' ?>><?= htmlspecialchars($cat) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </aside>
    <section class="forum-content">
      <?php if (count($posts) > 0): ?>
        <?php foreach ($posts as $row): ?>
          <?= render_post_card($row, $CONFIG) /* Pass $CONFIG object */ ?>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="post-card empty-card"><p class="empty-tip">目前尚無文章，歡迎發表第一篇！</p></div>
      <?php endif; ?>
      <nav class="pagination" aria-label="分頁導覽">
        <?php if ($page > 1): ?>
          <a href="?<?= $category ? 'category=' . urlencode($category) . '&' : '' ?>page=<?= $page - 1 ?>" aria-label="上一頁">上一頁</a>
        <?php endif; ?>
        <span class="pagination-info" aria-current="page">第 <?= htmlspecialchars((string)$page, ENT_QUOTES, 'UTF-8') ?> 頁 / 共 <?= htmlspecialchars((string)$totalPages, ENT_QUOTES, 'UTF-8') ?> 頁</span>
        <?php if ($page < $totalPages): ?>
          <a href="?<?= $category ? 'category=' . urlencode($category) . '&' : '' ?>page=<?= $page + 1 ?>" aria-label="下一頁">下一頁</a>
        <?php endif; ?>
      </nav>
    </section>
  </main>
  <?php include 'footer.php'; ?>
</body>

</html>
