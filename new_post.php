<?php
// new_post.php - new post page
require_once __DIR__ . '/inc/init.php'; // include init to get config

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="zh-TW">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>新增文章 - 美食論壇</title>
  <link rel="stylesheet" href="styles.css?v=<?= file_exists(__DIR__ . '/styles.css') ? filemtime(__DIR__ . '/styles.css') : time() ?>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/suneditor/dist/css/suneditor.min.css">
</head>

<body>
  <?php include 'header.php'; ?>
  <main class="simple-main container">
    <section class="new-post-section post-card">
      <h2 class="form-title">發表新文章</h2>
      <form action="api/new_post_process.php" method="post" enctype="multipart/form-data" class="new-post-form">
        <div class="form-group">
          <label for="title">標題：</label>
          <input type="text" id="title" name="title" required>
        </div>
        <div class="form-group">
          <label for="category">分類：</label>
          <select id="category" name="category" required>
            <?php foreach ($CONFIG->CATEGORIES as $category) : ?>
              <option value="<?= htmlspecialchars($category) ?>"><?= htmlspecialchars($category) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="content">內容：</label>
          <textarea id="content" name="content" rows="8"></textarea>
        </div>
        <div class="form-group">
          <label for="image">插入圖片：</label>
          <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)">
          <img id="imgPreview" class="img-preview" style="display:none;" />
        </div>
        <button type="submit">送出</button>
      </form>
    </section>
  </main>
  <script src="https://cdn.jsdelivr.net/npm/suneditor@2.41.3/dist/suneditor.min.js"></script>
  <script>
    let editorInstance;
    document.addEventListener('DOMContentLoaded', function() {
      if (window.SUNEDITOR) {
        editorInstance = SUNEDITOR.create('content', {
          height: '260px',
          buttonList: [
            ['bold', 'underline', 'italic', 'list', 'link', 'image', 'codeView']
          ]
        });
      } else {
        console.error('SunEditor 載入失敗，請檢查網路或 CDN。');
      }
      // 送出前同步內容
      document.querySelector('form').addEventListener('submit', function(e) {
        if (editorInstance) {
          // SunEditor 的內容更新到 textarea
          editorInstance.save();
        }
      });
    });

    function previewImage(event) {
      const input = event.target;
      const preview = document.getElementById('imgPreview');
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          preview.src = e.target.result;
          preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
      } else {
        preview.src = '';
        preview.style.display = 'none';
      }
    }
  </script>
  <?php include 'footer.php'; ?>
</body>

</html>
