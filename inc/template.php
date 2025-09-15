<?php
// template.php - 小型模板助手與常用 UI component

function render_post_card(array $row)
{
  // 簡單的卡片輸出（回傳字串，讓呼叫端決定 echo）
  $title = htmlspecialchars($row['title']);
  $id = intval($row['id']);
  $category = htmlspecialchars($row['category']);
  $date = htmlspecialchars(date('Y-m-d', strtotime($row['created_at'])));
  $author = htmlspecialchars($row['nickname'] ?? '');
  $excerpt = htmlspecialchars(mb_strimwidth(strip_tags($row['content']), 0, 200, '...'));
  // 圖片顯示：若有 image 欄位，優先顯示縮圖（thumb_ 前綴），否則顯示原圖
  $imgHtml = '';
  if (!empty($row['image'])) {
    $cfg = require __DIR__ . '/config.php';
    $uploadUrl = $cfg->UPLOAD_URL ?? 'uploads';
    $thumb = ($cfg->THUMB_PREFIX ?? 'thumb_') . $row['image'];
    $thumbPath = $uploadUrl . '/' . $thumb;
    $origPath = $uploadUrl . '/' . $row['image'];
    if (file_exists(__DIR__ . '/../' . $thumbPath)) {
      $imgHtml = "<img loading=\"lazy\" src=\"" . htmlspecialchars($thumbPath) . "\" alt=\"" . htmlspecialchars($title) . "\" class=\"post-image\">";
    } else {
      $imgHtml = "<img loading=\"lazy\" src=\"" . htmlspecialchars($origPath) . "\" alt=\"" . htmlspecialchars($title) . "\" class=\"post-image\">";
    }
  }

  $html = "<div class=\"post-card\">";
  if ($imgHtml) $html .= $imgHtml;
  $html .= "<h3><a href=\"post_detail.php?id={$id}\">{$title}</a></h3>";
  $html .= "<div style=\"color:#ffd54f;font-size:0.98rem;margin-bottom:6px;\">[{$category}]</div>";
  $html .= "<p>{$excerpt}</p>";
  $html .= "<div class=\"post-meta\">by {$author} | {$date}</div>";
  $html .= "</div>\n";
  return $html;
}
