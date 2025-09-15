<?php
// inc/sanitize.php
// 共用的 HTML 清理函式，簡單白名單並移除事件屬性與 javascript: 危險連結
function sanitize_html($html)
{
  if (!is_string($html)) return '';
  // 允許的標籤（可再調整）
  $allowed = '<p><br><strong><em><ul><ol><li><a><img><h1><h2><h3><blockquote>';
  $clean = strip_tags($html, $allowed);
  // 移除 on* 事件與 javascript: URI
  $clean = preg_replace('#on[a-zA-Z]+\s*=\s*"[^"]*"#i', '', $clean);
  $clean = preg_replace('#on[a-zA-Z]+\s*=\s*\'[^\']*\'#i', '', $clean);
  $clean = preg_replace('#href\s*=\s*"javascript:[^"]*"#i', '', $clean);
  $clean = preg_replace('#href\s*=\s*\'javascript:[^\']*\'#i', '', $clean);
  return $clean;
}
