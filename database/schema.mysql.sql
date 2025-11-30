-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2025-09-16 09:06:47
-- 伺服器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `food_forum`
--

-- --------------------------------------------------------

--
-- 資料表結構 `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `member_id`, `content`, `created_at`) VALUES
(4, 15, 1, '真好吃', '2025-09-15 12:05:24');

-- --------------------------------------------------------

--
-- 資料表結構 `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `nickname` text NOT NULL,
  `email` text NOT NULL,
  `birthday` text NOT NULL,
  `created_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `members`
--

INSERT INTO `members` (`id`, `username`, `password`, `nickname`, `email`, `birthday`, `created_time`, `is_admin`) VALUES
(1, 'admin', '$2y$10$zJLP5QwCoMjzv3kbLV2MBe5tLEsANUpBVdqXnHp2T4TxK6SWqPWMS', 'BB', '5566@gmail.com', '1992-05-06', '2025-09-15 00:30:02', 1);

-- --------------------------------------------------------

--
-- 資料表結構 `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `category` varchar(50) NOT NULL,
  `author_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `posts`
--

INSERT INTO `posts` (`id`, `title`, `content`, `category`, `author_id`, `created_at`, `image`) VALUES
(12, '永和佳香豆漿', '<div>有著20多年歷史的永和佳香豆漿。店內所有豆漿、燒餅、蛋餅皮與小籠湯包皆親手製作。招牌胡椒蔥餅外皮鬆軟、帶彈性，餅面滿布芝麻，咬下去蔥與胡椒的香氣交織，讓早晨的第一口格外踏實。<br></div>', '台灣小吃', 1, '2025-09-15 09:18:17', 'img_68c76959078549.65442888.webp'),
(13, '光興腿庫', '<p>自1998年開業以來，光興腿庫一直是老饕的午餐首選。滷豬腳膠質飽滿、腿庫軟嫩入味，大腸鹹香迷人，配上一份三菜一飯的便當，價格親民卻滿是誠意。嗜辣者不能錯過店家自製的小魚乾辣椒醬，鹹辣交織，讓白飯瞬間見底。<br></p>', '台灣小吃', 1, '2025-09-15 09:27:06', 'img_68c76b6a6f0b34.66142735.jpg'),
(14, '蔡家牛肉麵', '<p>藏身住宅區的蔡家牛肉麵，由夫妻共同經營，招牌有清燉與紅燒兩種風味。紅燒湯頭以澳洲牛腱心慢燉至入口即化，清燉湯則是金黃色牛骨高湯搭配厚切台灣牛胸肉，柔嫩卻帶嚼感。每日手工製作的Q彈麵條，將湯頭的香氣緊緊鎖住，是一碗會讓人想回味的牛肉麵。<br></p>', '台灣小吃', 1, '2025-09-15 09:33:01', 'img_68c76ccdf04391.36421508.jpg'),
(15, '阿爸の芋圓', '<p><br><br>​<span style=\"color: rgb(0, 0, 0); font-family: AdobeGaramondPro, &quot;Noto Serif TC&quot;, &quot;Songti TC&quot;, &quot;Noto Serif CJK TC&quot;, &quot;Microsoft JhengHei&quot;, &quot;Pingfang TC&quot;, serif; font-size: 20px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; widows: 2; word-spacing: 0px; -webkit-text-stroke-width: 0px; white-space: normal; background-color: rgb(255, 255, 255); text-decoration-thickness: initial; text-decoration-style: initial; text-decoration-color: initial; display: inline !important; float: none;\">想以甜點收尾，一定要來阿爸の芋圓。招牌芋見泥蔗片冰，將香滑芋泥、Q彈芋圓、白芋湯圓、粉圓與薏仁層層堆疊於細緻的甘蔗冰上，甘蔗冰還帶著淡淡煙燻香氣。天冷時可改點熱紅豆湯，甜而不膩，暖心暖胃。</span><br><br>​<br></p>', '甜點飲品', 1, '2025-09-15 09:48:33', 'img_68c77071452f21.31667054.jpg');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `member_id` (`member_id`);

--
-- 資料表索引 `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`) USING HASH;

--
-- 資料表索引 `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- 資料表的限制式 `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `members` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
