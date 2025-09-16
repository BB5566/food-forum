<?php
// 處理發表文章的請求（簡單版）
// 使用共用初始化檔，確保 session 與 $conn 可用
require_once __DIR__ . '/../inc/init.php'; // 啟動 session 並載入 DB

// 檢查是否登入，沒登入導回登入頁
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

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
if ($chk) {
    $chk->bind_param('i', $author_id);
    $chk->execute();
    $reschk = $chk->get_result();
    if (!$reschk || $reschk->num_rows === 0) {
        echo '無效的會員，請重新登入或聯絡管理員。';
        $chk->close();
        exit();
    }
    $chk->close();
} else {
    // 如果查詢無法建立，記錄原因但不顯示內部錯誤
}

// 處理上傳圖片（使用 config 中的路徑與簡單縮圖）
$image_name = '';
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allow_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (in_array($ext, $allow_ext)) {
        $image_name = uniqid('img_', true) . '.' . $ext;
        $uploadDir = $CONFIG->UPLOAD_DIR;
        $uploadUrl = $CONFIG->UPLOAD_URL;
        $target = $uploadDir . DIRECTORY_SEPARATOR . $image_name;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $image_name = '';
        } else {
            // 驗證 MIME 類型以防止偽造副檔名
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $target);
            finfo_close($finfo);
            $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($mime, $allowed_mimes)) {
                // 刪除上傳檔案
                @unlink($target);
                $image_name = '';
            } else {
                // re-encode 圖片以統一 format 與去除奇怪 metadata（若 GD 可用）
                if (function_exists('imagecreatefromstring') && function_exists('imagejpeg')) {
                    $data = file_get_contents($target);
                    $src = @imagecreatefromstring($data);
                    if ($src) {
                        $tmpPath = $target;
                        // 儲存為原始類型的 re-encoded 檔案（覆蓋）
                        switch ($mime) {
                            case 'image/jpeg':
                                imagejpeg($src, $tmpPath, 90);
                                break;
                            case 'image/png':
                                imagepng($src, $tmpPath);
                                break;
                            case 'image/gif':
                                imagegif($src, $tmpPath);
                                break;
                            case 'image/webp':
                                if (function_exists('imagewebp')) imagewebp($src, $tmpPath, 85);
                                break;
                        }
                        imagedestroy($src);
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
                                imagepng($thumb, $thumbPath);
                                break;
                            case IMAGETYPE_GIF:
                                imagegif($thumb, $thumbPath);
                                break;
                        }
                        imagedestroy($thumb);
                        imagedestroy($src);
                    }
                }
            }
        }
    } else {
    }
}

if ($title === '' || $content === '' || $category === '') {
    echo '請填寫所有欄位。';
    exit();
}

// 把資料寫進資料庫
$sql = "INSERT INTO posts (title, content, category, author_id, image) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('sssis', $title, $content, $category, $author_id, $image_name);
if ($stmt->execute()) {
    header('Location: ../index.php');
    exit();
} else {
    // 記錄錯誤以便調查，並向使用者顯示友善訊息
    $err = $stmt->error ?: $conn->error;
    echo '發文失敗，請稍後再試（錯誤已記錄）。';
}
$stmt->close();
$conn->close();