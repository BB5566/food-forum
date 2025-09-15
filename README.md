# 🍜 美食論壇 - Food Forum

一個優雅的美食分享平台，採用 MUJI 風格設計，專為美食愛好者打造的社群網站。

## ✨ 專案特色

### 🎨 設計理念

- **無印良品風格**：簡潔、現代的視覺設計
- **響應式佈局**：完美適配桌面與手機端
- **優雅動畫**：微妙的懸停效果與過渡動畫

### 🚀 核心功能

- **會員系統**：註冊、登入、個人資料管理
- **文章發布**：支援圖片上傳與分類管理
- **互動留言**：文章評論與討論功能
- **分類瀏覽**：台灣小吃、異國料理、甜點飲品等多個分類
- **分頁系統**：高效的內容瀏覽體驗

### 🛠️ 技術架構

- **後端**：PHP 8.2 + MySQL
- **前端**：原生 HTML/CSS + JavaScript
- **安全**：Prepared Statements 防 SQL 注入
- **上傳**：圖片處理與縮圖生成
- **架構**：MVC 模式的文件組織

## 📁 專案結構

```plaintext
food-forum/
├── index.php              # 首頁與文章列表
├── login.php              # 會員登入
├── reg.php                # 會員註冊
├── member_center.php      # 個人中心
├── new_post.php           # 發佈新文章
├── post_detail.php        # 文章詳情頁
├── header.php             # 頁面頭部
├── footer.php             # 頁面底部
├── styles.css             # MUJI 風格樣式表
├── db_connect.php         # 資料庫連線
├── members.sql            # 資料庫結構
├── api/                   # API 端點
│   ├── login_process.php
│   ├── reg_process.php
│   ├── new_post_process.php
│   ├── add_comment.php
│   └── delete_post.php
├── inc/                   # 共用組件
│   ├── config.php         # 系統配置
│   ├── init.php           # 初始化腳本
│   ├── template.php       # 模板函數
│   └── sanitize.php       # HTML 清理
├── img/                   # 靜態圖片資源
└── uploads/               # 用戶上傳圖片
```

## 🎯 開發亮點

### 安全性實作

- **輸入驗證**：所有用戶輸入皆經過清理
- **SQL 注入防護**：使用 Prepared Statements
- **XSS 防護**：HTML 標籤白名單過濾
- **會話管理**：安全的登入狀態控制

### 使用者體驗

- **直觀導航**：清晰的分類側邊欄
- **即時回饋**：操作結果的視覺提示
- **圖片優化**：自動生成縮圖，提升載入速度
- **無障礙設計**：支援鍵盤導航與螢幕閱讀器

### 程式碼品質

- **模組化設計**：清晰的文件結構與功能分離
- **註解完整**：每個功能都有詳細說明
- **錯誤處理**：完善的異常處理機制
- **效能優化**：分頁載入與懶載入圖片

## 🚀 快速開始

### 環境需求

- PHP 8.0+
- MySQL 5.7+
- GD 擴展（圖片處理）

### 安裝步驟

1. **下載專案**

   ```bash
   git clone https://github.com/yourusername/food-forum.git
   cd food-forum
   ```

2. **資料庫設定**

   ```bash
   # 建立資料庫
   mysql -u root -p < members.sql

   # 設定環境變數（建立 .env 文件）
   DB_SERVER=localhost
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   DB_NAME=food_forum
   ```

3. **權限設定**

   ```bash
   # 設定上傳目錄權限
   chmod 755 uploads/
   ```

4. **啟動服務**

   ```bash
   php -S localhost:8000
   ```

5. **訪問網站**
   開啟瀏覽器訪問 `http://localhost:8000`

## 📱 頁面展示

- **首頁**：文章列表與分類導航
- **會員中心**：個人資料與文章管理
- **發文頁面**：支援圖片上傳的編輯器
- **文章詳情**：完整內容與留言區

## 🔧 自訂設定

### 分類管理

在 `inc/config.php` 中修改允許的分類：

```php
'CATEGORIES' => ['台灣小吃', '異國料理', '甜點飲品', '素食專區', '其他']
```

### 樣式調整

`styles.css` 使用 CSS 自訂屬性，輕鬆修改主題色彩：

```css
:root {
  --primary-black: #1a1a1a;
  --accent-warm: #f5f5f0;
  /* ... */
}
```

## 📈 專案成果

這個專案展示了完整的 Web 應用開發流程：

- ✅ **全端開發**：從資料庫設計到前端介面
- ✅ **安全性實作**：防護常見 Web 安全漏洞
- ✅ **使用者體驗**：注重細節的互動設計
- ✅ **程式碼品質**：可維護且可擴展的架構
- ✅ **響應式設計**：跨裝置的完美體驗

## 📄 授權

本專案僅供學習與作品集展示使用。

---

**🍽️ 享受美食，分享快樂！**
