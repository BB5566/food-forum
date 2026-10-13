<?php require_once __DIR__ . '/inc/init.php'; // 先啟動 session，CSRF token 才存得進去 ?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>會員註冊 - <?= htmlspecialchars($CONFIG->SITE_NAME ?? '美食論壇') ?></title>
  <link rel="stylesheet" href="styles.css?v=<?= file_exists(__DIR__ . '/styles.css') ? filemtime(__DIR__ . '/styles.css') : time() ?>">
</head>

<body>
  <a href="#main-content" class="skip-link">跳至主要內容</a>
  <?php include 'header.php'; ?>
  <main id="main-content" class="simple-main container">
    <section class="reg-section post-card">
      <h2 class="form-title">會員註冊</h2>
      <form action="api/reg_process.php" method="post" class="reg-form">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
        <div class="form-group">
          <label for="username">帳號：</label>
          <input type="text" id="username" name="username" autocomplete="username" required>
        </div>
        <div class="form-group">
          <label for="password">密碼：</label>
          <input type="password" id="password" name="password" autocomplete="new-password" required>
        </div>
        <div class="form-group">
          <label for="nickname">暱稱：</label>
          <input type="text" id="nickname" name="nickname" autocomplete="nickname" required>
        </div>
        <div class="form-group">
          <label for="email">電子郵件：</label>
          <input type="email" id="email" name="email" autocomplete="email" required>
        </div>
        <div class="form-group">
          <label for="birthday">生日：</label>
          <input type="date" id="birthday" name="birthday" autocomplete="bday" required>
        </div>
        <div class="form-actions">
          <button type="submit" class="btn btn-primary">註冊</button>
          <button type="reset" class="btn btn-secondary">重置</button>
        </div>
      </form>
      <p class="form-switch-prompt">已有帳號？<a href="login.php">前往登入</a></p>
    </section>
  </main>
  <?php include 'footer.php'; ?>
</body>

</html>
