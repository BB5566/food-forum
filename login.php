<?php require_once __DIR__ . '/inc/init.php'; // 先啟動 session，CSRF token 才存得進去 ?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>會員登入 - <?= htmlspecialchars($CONFIG->SITE_NAME ?? '美食論壇') ?></title>
  <link rel="stylesheet" href="styles.css?v=<?= file_exists(__DIR__ . '/styles.css') ? filemtime(__DIR__ . '/styles.css') : time() ?>">
</head>

<body>
  <a href="#main-content" class="skip-link">跳至主要內容</a>
  <?php include 'header.php'; ?>
  <main id="main-content" class="simple-main container">
    <section class="login-section post-card">
      <h2 class="form-title">會員登入</h2>
      <?php
      if (isset($_GET['error'])) {
        echo '<div class="error-message" role="alert">' . htmlspecialchars($_GET['error']) . '</div>';
      }
      ?>
      <form action="api/login_process.php" method="post" class="login-form">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
        <div class="form-group">
          <label for="username">帳號：</label>
          <input type="text" id="username" name="username" autocomplete="username" required>
        </div>
        <div class="form-group">
          <label for="password">密碼：</label>
          <input type="password" id="password" name="password" autocomplete="current-password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">登入</button>
      </form>
      <p class="form-switch-prompt">還沒有帳號？<a href="reg.php">立即註冊</a></p>
    </section>
  </main>
  <?php include 'footer.php'; ?>
</body>

</html>
