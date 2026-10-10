<?php
// 處理發表文章的請求（簡單版）
// 使用共用初始化檔，確保 session 與 $conn 可用
require_once __DIR__ . '/../inc/init.php'; // 啟動 session 並載入 DB

// 檢查是否登入，沒登入導回登入頁
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}
require_csrf_token();

// 取得表單資料
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$category = trim($_POST['category'] ?? '');
// 取得作者 ID，並把它轉成整數以避免空字串或非數字造成外鍵錯誤
$author_id = intval($_SESSION['user_id']);
if ($author_id <= 0) {
    echo '您尚未登入或登入資訊已失效，請重新登入後再發文。';
    exit();
}

// 驗證此 member id 在 members 表中存在（防止 session 裡殘留不存在的 id）
$chk = $conn->prepare('SELECT id FROM members WHERE id = ? LIMIT 1');
$chk->execute([$author_id]);
$reschk = $chk->fetch();
if (!$reschk) {
    echo '無效的會員，請重新登入或聯絡管理員。';
    exit();
}

// 處理上傳圖片（使用 config 中的路徑與簡單縮圖）
$image_name = '';
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $tmpName = $_FILES['image']['tmp_name'];
    $origName = $_FILES['image']['name'] ?? '';
    $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
    $allow_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (is_uploaded_file($tmpName) && in_array($ext, $allow_ext, true)) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $tmpName);
        finfo_close($finfo);

        $mimeToExt = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        ];

        if (isset($mimeToExt[$mime])) {
            $image_name = bin2hex(random_bytes(16)) . '.' . $mimeToExt[$mime];
            $uploadDir = $CONFIG->UPLOAD_DIR;
            $target = $uploadDir . DIRECTORY_SEPARATOR . $image_name;

            if (!move_uploaded_file($tmpName, $target)) {
                $image_name = '';
            } else {
                @chmod($target, 0644);

                // re-encode 圖片以去除危險 metadata（若 GD 可用）
                if (function_exists('imagecreatefromstring') && function_exists('imagejpeg')) {
                    $data = file_get_contents($target);
                    $src = @imagecreatefromstring($data);
                    if ($src) {
                        switch ($mime) {
                            case 'image/jpeg':
                                imagejpeg($src, $target, 92);
                                break;
                            case 'image/png':
                                imagepng($src, $target, 4);
                                break;
                            case 'image/gif':
                                imagegif($src, $target);
                                break;
                            case 'image/webp':
                                if (function_exists('imagewebp')) imagewebp($src, $target, 90);
                                break;
                        }
                        imagedestroy($src);
                    }
                }

                // 超過 200KB 時做近無損壓縮（尺寸維持不變）
                if (filesize($target) > 204800 && function_exists('getimagesize')) {
                    $info = @getimagesize($target);
                    if ($info) {
                        switch ($mime) {
                            case 'image/jpeg':
                                $img = @imagecreatefromjpeg($target);
                                if ($img) {
                                    imagejpeg($img, $target, 90);
                                    imagedestroy($img);
                                }
                                break;
                            case 'image/png':
                                $img = @imagecreatefrompng($target);
                                if ($img) {
                                    imagepng($img, $target, 5);
                                    imagedestroy($img);
                                }
                                break;
                            case 'image/webp':
                                if (function_exists('imagecreatefromwebp') && function_exists('imagewebp')) {
                                    $img = @imagecreatefromwebp($target);
                                    if ($img) {
                                        imagewebp($img, $target, 88);
                                        imagedestroy($img);
                                    }
                                }
                                break;
                        }
                    }
                }

                // 嘗試用 GD 產生縮圖（如果環境支援）
                if (function_exists('getimagesize') && function_exists('imagecreatetruecolor')) {
                    $info = @getimagesize($target);
                    if ($info) {
                        list($width, $height, $type) = $info;
                        $maxW = $CONFIG->THUMB_MAX_WIDTH;
                        $maxH = $CONFIG->THUMB_MAX_HEIGHT;
                        $ratio = min($maxW / $width, $maxH / $height, 1);
                        $newW = (int)($width * $ratio);
                        $newH = (int)($height * $ratio);
                        $thumb = imagecreatetruecolor($newW, $newH);
                        switch ($type) {
                            case IMAGETYPE_JPEG:
                                $src = imagecreatefromjpeg($target);
                                break;
                            case IMAGETYPE_PNG:
                                $src = imagecreatefrompng($target);
                                break;
                            case IMAGETYPE_GIF:
                                $src = imagecreatefromgif($target);
                                break;
                            case IMAGETYPE_WEBP:
                                $src = function_exists('imagecreatefromwebp') ? imagecreatefromwebp($target) : null;
                                break;
                            default:
                                $src = null;
                        }
                        if ($src) {
                            imagecopyresampled($thumb, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);
                            $thumbName = $CONFIG->THUMB_PREFIX . $image_name;
                            $thumbPath = $uploadDir . DIRECTORY_SEPARATOR . $thumbName;
                            // 儲存縮圖
                            switch ($type) {
                                case IMAGETYPE_JPEG:
                                    imagejpeg($thumb, $thumbPath, 85);
                                    break;
                                case IMAGETYPE_PNG:
                                    imagepng($thumb, $thumbPath, 5);
                                    break;
                                case IMAGETYPE_GIF:
                                    imagegif($thumb, $thumbPath);
                                    break;
                                case IMAGETYPE_WEBP:
                                    if (function_exists('imagewebp')) imagewebp($thumb, $thumbPath, 85);
                                    break;
                            }
                            @chmod($thumbPath, 0644);
                            imagedestroy($thumb);
                            imagedestroy($src);
                        }
                    }
                }
            }
        }
    }
}

if ($title === '' || $content === '' || $category === '') {
    echo '請填寫所有欄位。';
    exit();
}

// 把資料寫進資料庫
$sql = "INSERT INTO posts (title, content, category, author_id, image) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if ($stmt->execute([$title, $content, $category, $author_id, $image_name])) {
    header('Location: ../index.php');
    exit();
}

echo '發文失敗，請稍後再試。';
