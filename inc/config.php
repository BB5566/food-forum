<?php
// config.php - 集中專案設定（展示用，安全性未做過度強化）
return (object) [
  // 網站資訊
  'SITE_NAME' => '美食論壇',

  // 分類清單（使用同一來源以免不同檔案雜湊）
  'CATEGORIES' => ['台灣小吃', '異國料理', '甜點飲品', '素食專區', '其他'],

  // 分頁設定
  'PAGE_SIZE' => 10,

  // 上傳設定
  'UPLOAD_DIR' => __DIR__ . '/../uploads', // 實際儲存路徑
  'UPLOAD_URL' => 'uploads', // 用於前端顯示的相對 URL
  'THUMB_PREFIX' => 'thumb_',
  'THUMB_MAX_WIDTH' => 800,
  'THUMB_MAX_HEIGHT' => 800,

  // 開發模式（debug 記錄會開啟）；上線前請改為 false
  'DEV_MODE' => false,
];
