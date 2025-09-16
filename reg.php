<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>食話實說</title>
  <link rel="stylesheet" href="styles.css?v=<?= file_exists(__DIR__ . '/styles.css') ? filemtime(__DIR__ . '/styles.css') : time() ?>">
</head>

<body>
  <?php include 'header.php'; ?>
  <main class="simple-main container">
    <section class="reg-section post-card">
      <h2 class="form-title">會員註冊</h2>
      <form action="api/reg_process.php" method="post" class="reg-form">
        <div>
          <label for="username">帳號：</label>
          <input type="text" id="username" name="username" required>
        </div>
        <div>
          <label for="password">密碼：</label>
          <input type="password" id="password" name="password" required>
        </div>
        <div>
          <label for="nickname">暱稱：</label>
          <input type="text" id="nickname" name="nickname" required>
        </div>
        <div>
          <label for="email">電子郵件：</label>
          <input type="email" id="email" name="email" required>
        </div>
        <div>
          <label for="birthday">生日：</label>
          <input type="date" id="birthday" name="birthday" required>
        </div>
        <div style="display:flex;gap:12px;">
          <button type="submit">註冊</button>
          <button type="reset">重置</button>
        </div>
      </form>
    </section>
  </main>
  <?php include 'footer.php'; ?>
</body>

</html>
