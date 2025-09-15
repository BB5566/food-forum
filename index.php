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

  // 只在 category 有值且為允許清單時才加上 WHERE 條件，使用 prepared statement
  $allowed = $CONFIG->CATEGORIES;
  if ($category && in_array($category, $allowed)) {
    $sql = "SELECT posts.*, members.nickname FROM posts JOIN members ON posts.author_id = members.id WHERE posts.category = ? ORDER BY created_at DESC LIMIT ?, ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sii', $category, $offset, $pageSize);
    $stmt->execute();
    $result = $stmt->get_result();
    // 取得總數以顯示分頁
    $countStmt = $conn->prepare('SELECT COUNT(*) as c FROM posts WHERE category = ?');
    $countStmt->bind_param('s', $category);
    $countStmt->execute();
    $total = $countStmt->get_result()->fetch_assoc()['c'] ?? 0;
    $countStmt->close();
  } else {
    $sql = "SELECT posts.*, members.nickname FROM posts JOIN members ON posts.author_id = members.id ORDER BY created_at DESC LIMIT ?, ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $offset, $pageSize);
    $stmt->execute();
    $result = $stmt->get_result();
    // 總數
    $countStmt = $conn->prepare('SELECT COUNT(*) as c FROM posts');
    $countStmt->execute();
    $total = $countStmt->get_result()->fetch_assoc()['c'] ?? 0;
    $countStmt->close();
  }
  $totalPages = max(1, ceil($total / $pageSize));
  ?>
  <main class="forum-main container">
    <aside class="forum-sidebar">
      <h2>分類</h2>
      <ul>
        <li><a href="index.php">全部</a></li>
        <li><a href="?category=台灣小吃">台灣小吃</a></li>
        <li><a href="?category=異國料理">異國料理</a></li>
        <li><a href="?category=甜點飲品">甜點飲品</a></li>
        <li><a href="?category=素食專區">素食專區</a></li>
        <li><a href="?category=其他">其他</a></li>
      </ul>
    </aside>
    <section class="forum-content">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <?= render_post_card($row) ?>
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
