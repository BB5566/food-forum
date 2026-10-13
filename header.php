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
      <a href="index.php" class="header-brand" aria-label="<?= htmlspecialchars($siteName) ?> 首頁">
        <img src="./img/w-logo.png" alt="<?= htmlspecialchars($siteName) ?>" class="logo-wide">
        <h1><?= htmlspecialchars($siteName) ?></h1>
      </a>
    </div>
    <button class="nav-toggle" aria-expanded="false" aria-controls="main-nav" aria-label="切換導覽選單">☰</button>
    <nav id="main-nav" class="nav-links" aria-label="主要導覽">
      <a href="index.php">首頁</a>
      <a href="./new_post.php">發表文章</a>
      <a href="member_center.php">會員中心</a>
    </nav>
    <div class="header-user">
      <?php if (isset($_SESSION['user_id'])): ?>
        <span class="user-pill"><span class="user-avatar" aria-hidden="true">👤</span><?= htmlspecialchars($_SESSION['nickname']) ?>，歡迎！</span>
        <a href="./api/logout.php" class="logout-btn">登出</a>
      <?php else: ?>
        <a href="reg.php" class="header-auth-link">註冊</a>
        <a href="login.php" class="header-auth-link header-auth-primary">登入</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<script>
  // Mobile nav toggle with accessibility support
  document.addEventListener('DOMContentLoaded', function() {
    var btn = document.querySelector('.nav-toggle');
    var nav = document.querySelector('.nav-links');
    var header = document.querySelector('.forum-header');

    if (btn && nav) {
      function closeNav() {
        if (nav.classList.contains('open')) {
          nav.classList.remove('open');
          btn.setAttribute('aria-expanded', 'false');
          nav.style.transform = '';
        }
      }

      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        var open = nav.classList.toggle('open');
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open) {
          nav.style.transform = 'translateY(-6px)';
          requestAnimationFrame(function() {
            nav.style.transform = 'translateY(0)';
          });
        } else {
          nav.style.transform = '';
        }
      });

      // Close mobile menu on Escape key
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && nav.classList.contains('open')) {
          closeNav();
          btn.focus();
        }
      });

      // Close mobile menu when clicking outside
      document.addEventListener('click', function(e) {
        if (nav.classList.contains('open') && !header.contains(e.target)) {
          closeNav();
        }
      });

      // Close mobile menu on viewport resize beyond tablet
      window.addEventListener('resize', function() {
        if (window.innerWidth > 768 && nav.classList.contains('open')) {
          closeNav();
        }
      });
    }

    // header shrink on scroll
    if (header) {
      window.addEventListener('scroll', function() {
        var y = window.scrollY || window.pageYOffset;
        if (y > 48) header.classList.add('shrink');
        else header.classList.remove('shrink');
      }, {
        passive: true
      });
    }
  });
</script>
