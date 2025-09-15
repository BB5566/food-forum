<?php
// header.php：網站上方共用區塊（導覽列與登入顯示）
// 載入共用初始化（若還沒載入）
if (session_status() === PHP_SESSION_NONE) {
  @include __DIR__ . '/inc/init.php';
}
$siteName = isset($CONFIG) ? ($CONFIG->SITE_NAME ?? '網站') : '網站';
?>
<header class="forum-header glassy-header">
  <div class="container header-flex">
    <div class="header-logo-title">
      <img src="./img/logo.png" alt="正方形logo" class="logo-square">
      <img src="./img/w-logo.png" alt="橫式logo" class="logo-wide">
      <h1><?= htmlspecialchars($siteName) ?></h1>
    </div>
    <button class="nav-toggle" aria-expanded="false" aria-label="切換導覽">☰</button>
    <nav class="nav-links" role="navigation">
      <a href="index.php">首頁</a>
      <a href="#">熱門主題</a>
      <a href="./new_post.php">發表文章</a>
      <a href="member_center.php">會員中心</a>
    </nav>
    <div class="header-user">
      <?php if (isset($_SESSION['user_id'])): ?>
        <span class="user-pill"><span class="user-avatar">👤</span><?= htmlspecialchars($_SESSION['nickname']) ?>，歡迎！</span>
        <a href="./api/logout.php" class="logout-btn">登出</a>
      <?php else: ?>
        <a href="reg.php">註冊</a>
        <a href="login.php">登入</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<script>
  // small nav toggle for mobile: toggles .open on .nav-links
  document.addEventListener('DOMContentLoaded', function() {
    var btn = document.querySelector('.nav-toggle');
    var nav = document.querySelector('.nav-links');
    if (btn && nav) {
      btn.addEventListener('click', function() {
        var open = nav.classList.toggle('open');
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        // animate: force reflow then set transform for smoother slide
        if (open) {
          nav.style.transform = 'translateY(-6px)';
          requestAnimationFrame(function() {
            nav.style.transform = 'translateY(0)';
          });
        } else {
          nav.style.transform = '';
        }
      });
    }

    // header shrink on scroll
    var header = document.querySelector('.forum-header');
    var lastScroll = 0;
    if (header) {
      window.addEventListener('scroll', function() {
        var y = window.scrollY || window.pageYOffset;
        if (y > 48) header.classList.add('shrink');
        else header.classList.remove('shrink');
        lastScroll = y;
      }, {
        passive: true
      });
    }
  });
</script>
