# Food Forum 資料庫設定指南

## 📋 資料庫概述

美食論壇系統使用 MySQL 資料庫來儲存會員資料、文章和留言。

### 技術規格
- **資料庫類型:** MySQL / MariaDB
- **字元集:** UTF-8 (utf8mb4)
- **連線方式:** mysqli
- **預設資料庫名稱:** `food_forum`

---

## 🚀 快速開始

### 方法一：使用 MySQL CLI

```bash
# 1. 登入 MySQL
mysql -u root -p

# 2. 匯入資料庫
source /path/to/food-forum/food_forum.sql

# 3. 驗證
USE food_forum;
SHOW TABLES;
```

### 方法二：使用 phpMyAdmin

1. 開啟 phpMyAdmin
2. 點選「匯入」
3. 選擇 `food_forum.sql` 檔案
4. 點擊「執行」

---

## ⚙️ 環境設定

### 步驟 1: 建立 .env 檔案

```bash
cp .env.example .env
```

### 步驟 2: 編輯 .env 檔案

```env
DB_SERVER=localhost
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
DB_NAME=food_forum
```

### 步驟 3: 執行資料庫 Schema

```bash
mysql -u your_username -p < food_forum.sql
```

---

## 📊 資料表結構

### 1. members 資料表

儲存會員資訊。

| 欄位 | 類型 | 說明 | 約束 |
|------|------|------|------|
| id | INT | 會員ID | 主鍵, 自動遞增 |
| username | TEXT | 使用者帳號 | 唯一, 非空 |
| password | TEXT | 密碼雜湊 | 非空 |
| nickname | TEXT | 暱稱 | 非空 |
| email | TEXT | 電子信箱 | 非空 |
| birthday | TEXT | 生日 | 非空 |
| created_time | TIMESTAMP | 註冊時間 | 預設當前時間 |
| is_admin | TINYINT(1) | 管理員權限 | 預設 0 |

**預設管理員帳號:**
- 帳號: `admin`
- 密碼: (需自行設定)
- 權限: `is_admin = 1`

---

### 2. posts 資料表

儲存文章資訊。

| 欄位 | 類型 | 說明 | 約束 |
|------|------|------|------|
| id | INT | 文章ID | 主鍵, 自動遞增 |
| title | VARCHAR(255) | 文章標題 | 非空 |
| content | TEXT | 文章內容 | 非空 |
| category | VARCHAR(50) | 分類 | 非空 |
| author_id | INT | 作者ID | 外鍵 → members.id |
| created_at | DATETIME | 發布時間 | 預設當前時間 |
| image | VARCHAR(255) | 圖片檔名 | 可空 |

**外鍵約束:**
- `author_id` 關聯到 `members.id`

**分類範例:**
- 台灣小吃
- 甜點飲品
- 異國料理
- 其他

---

### 3. comments 資料表

儲存留言資訊。

| 欄位 | 類型 | 說明 | 約束 |
|------|------|------|------|
| id | INT | 留言ID | 主鍵, 自動遞增 |
| post_id | INT | 文章ID | 外鍵 → posts.id |
| member_id | INT | 會員ID | 外鍵 → members.id |
| content | TEXT | 留言內容 | 非空 |
| created_at | DATETIME | 留言時間 | 預設當前時間 |

**外鍵約束:**
- `post_id` 關聯到 `posts.id` (CASCADE DELETE)
- `member_id` 關聯到 `members.id` (CASCADE DELETE)
- 刪除文章時會自動刪除所有留言
- 刪除會員時會自動刪除其所有留言

---

## 🔍 常用查詢

### 查詢文章列表 (含作者資訊)

```sql
SELECT
    p.*,
    m.nickname AS author_name,
    COUNT(DISTINCT c.id) AS comment_count
FROM posts p
LEFT JOIN members m ON p.author_id = m.id
LEFT JOIN comments c ON p.id = c.post_id
GROUP BY p.id
ORDER BY p.created_at DESC;
```

### 查詢特定文章的所有留言

```sql
SELECT
    c.*,
    m.nickname AS commenter_name
FROM comments c
JOIN members m ON c.member_id = m.id
WHERE c.post_id = 15
ORDER BY c.created_at ASC;
```

### 查詢會員的文章統計

```sql
SELECT
    m.id,
    m.nickname,
    COUNT(p.id) AS post_count,
    COUNT(c.id) AS comment_count
FROM members m
LEFT JOIN posts p ON m.id = p.author_id
LEFT JOIN comments c ON m.id = c.member_id
GROUP BY m.id;
```

### 查詢熱門分類

```sql
SELECT
    category,
    COUNT(*) AS post_count
FROM posts
GROUP BY category
ORDER BY post_count DESC;
```

---

## 🛠️ 維護建議

### 定期備份

```bash
# 完整備份
mysqldump -u root -p food_forum > backup_$(date +%Y%m%d).sql

# 僅備份結構
mysqldump -u root -p --no-data food_forum > schema.sql
```

### 清理測試資料

```sql
-- 刪除所有留言
DELETE FROM comments;

-- 刪除所有文章
DELETE FROM posts;

-- 重置自動遞增
ALTER TABLE comments AUTO_INCREMENT = 1;
ALTER TABLE posts AUTO_INCREMENT = 1;
```

---

## 🔒 安全建議

### 1. 修改預設管理員密碼

```sql
-- 使用 PHP 產生密碼雜湊
-- password_hash('your_new_password', PASSWORD_DEFAULT)

UPDATE members
SET password = '$2y$10$your_hashed_password_here'
WHERE username = 'admin';
```

### 2. 定期檢查異常帳號

```sql
-- 查詢近期註冊帳號
SELECT * FROM members
WHERE created_time > DATE_SUB(NOW(), INTERVAL 7 DAY)
ORDER BY created_time DESC;
```

### 3. 監控文章內容

```sql
-- 查詢包含敏感詞的文章
SELECT * FROM posts
WHERE content LIKE '%敏感詞%'
OR title LIKE '%敏感詞%';
```

---

## ❓ 常見問題

### Q1: 如何新增管理員?

```sql
-- 將現有會員設為管理員
UPDATE members SET is_admin = 1 WHERE id = 2;

-- 或建立新管理員帳號
INSERT INTO members (username, password, nickname, email, birthday, is_admin)
VALUES ('admin2', '$2y$10$hashed_password', '管理員2', 'admin@example.com', '1990-01-01', 1);
```

### Q2: 如何修改文章分類?

```sql
-- 更新單篇文章
UPDATE posts SET category = '新分類' WHERE id = 12;

-- 批次更新
UPDATE posts SET category = '台灣小吃' WHERE category = '小吃';
```

### Q3: 圖片檔案儲存在哪裡?

圖片儲存在專案的 `uploads/` 或 `img/` 目錄，資料庫僅儲存檔案名稱。

### Q4: 如何刪除會員但保留其文章?

需要先修改外鍵約束，或將文章轉移給其他會員:

```sql
-- 將文章轉移給管理員 (ID=1)
UPDATE posts SET author_id = 1 WHERE author_id = 待刪除會員ID;

-- 然後刪除會員
DELETE FROM members WHERE id = 待刪除會員ID;
```

---

## 📈 效能優化

### 建議的索引

```sql
-- 加速文章查詢
CREATE INDEX idx_category ON posts(category);
CREATE INDEX idx_created_at ON posts(created_at);

-- 加速會員查詢
CREATE INDEX idx_email ON members(email(100));
```

### 定期優化資料表

```sql
OPTIMIZE TABLE members;
OPTIMIZE TABLE posts;
OPTIMIZE TABLE comments;
```

---

**文件版本:** 1.0.0
**最後更新:** 2025-11-03
**相容版本:** Food Forum v1.0.0+
