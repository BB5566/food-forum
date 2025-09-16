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
  <main class="simple-main container">
    <section class="login-section post-card">
      <h2 class="form-title">會員登入</h2>
      <?php
      if (isset($_GET['error'])) {
        echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
      }
      ?>
      <form action="api/login_process.php" method="post" class="login-form">
        <div>
          <label for="username">帳號：</label>
          <input type="text" id="username" name="username" required>
        </div>
        <div>
          <label for="password">密碼：</label>
          <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">登入</button>
      </form>
    </section>
  </main>
  <?php include 'footer.php'; ?>
</body>

</html>
