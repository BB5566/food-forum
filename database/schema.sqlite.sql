-- Food Forum SQLite Schema
-- 轉換自 MySQL schema
-- 建立時間: 2025-11-30

-- ====================================================================
-- 會員資料表
-- ====================================================================

CREATE TABLE IF NOT EXISTS members (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    nickname TEXT NOT NULL,
    email TEXT NOT NULL,
    birthday TEXT NOT NULL,
    created_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_admin INTEGER NOT NULL DEFAULT 0
);

-- ====================================================================
-- 文章資料表
-- ====================================================================

CREATE TABLE IF NOT EXISTS posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category VARCHAR(50) NOT NULL,
    author_id INTEGER NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    image VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (author_id) REFERENCES members(id)
);

-- ====================================================================
-- 留言資料表
-- ====================================================================

CREATE TABLE IF NOT EXISTS comments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    post_id INTEGER NOT NULL,
    member_id INTEGER NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

-- ====================================================================
-- 建立索引
-- ====================================================================

CREATE INDEX IF NOT EXISTS idx_posts_author ON posts(author_id);
CREATE INDEX IF NOT EXISTS idx_comments_post ON comments(post_id);
CREATE INDEX IF NOT EXISTS idx_comments_member ON comments(member_id);

-- ====================================================================
-- 插入範例資料
-- ====================================================================

-- 管理員帳號 (密碼: admin)
INSERT INTO members (id, username, password, nickname, email, birthday, is_admin) VALUES
(1, 'admin', '$2y$10$zJLP5QwCoMjzv3kbLV2MBe5tLEsANUpBVdqXnHp2T4TxK6SWqPWMS', 'BB', '5566@gmail.com', '1992-05-06', 1);

-- 範例文章
INSERT INTO posts (id, title, content, category, author_id, image) VALUES
(1, '永和佳香豆漿', '<div>有著20多年歷史的永和佳香豆漿。店內所有豆漿、燒餅、蛋餅皮與小籠湯包皆親手製作。招牌胡椒蔥餅外皮鬆軟、帶彈性，餅面滿布芝麻，咬下去蔥與胡椒的香氣交織，讓早晨的第一口格外踏實。</div>', '台灣小吃', 1, 'img_68c76959078549.65442888.webp'),
(2, '光興腿庫', '<p>自1998年開業以來，光興腿庫一直是老饕的午餐首選。滷豬腳膠質飽滿、腿庫軟嫩入味，大腸鹹香迷人，配上一份三菜一飯的便當，價格親民卻滿是誠意。</p>', '台灣小吃', 1, 'img_68c76b6a6f0b34.66142735.jpg'),
(3, '蔡家牛肉麵', '<p>藏身住宅區的蔡家牛肉麵，由夫妻共同經營，招牌有清燉與紅燒兩種風味。紅燒湯頭以澳洲牛腱心慢燉至入口即化，是一碗會讓人想回味的牛肉麵。</p>', '台灣小吃', 1, 'img_68c76ccdf04391.36421508.jpg'),
(4, '阿爸の芋圓', '<p>想以甜點收尾，一定要來阿爸の芋圓。招牌芋見泥蔗片冰，將香滑芋泥、Q彈芋圓層層堆疊於細緻的甘蔗冰上。</p>', '甜點飲品', 1, 'img_68c77071452f21.31667054.jpg');

-- 範例留言
INSERT INTO comments (post_id, member_id, content) VALUES
(4, 1, '真好吃');
