<?php
session_start();
require_once __DIR__ . '/config/db.php';

// 1. Bảo mật
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// 2. Lấy dữ liệu bài viết hiện tại
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM TinTuc WHERE MaTinTuc = ?");
$stmt->execute([$id]);
$news = $stmt->fetch();

if (!$news) {
    die("Bài viết không tồn tại!");
}

// 3. Xử lý cập nhật khi Submit Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tieu_de = $_POST['tieu_de'];
    $noi_dung = $_POST['noi_dung'];
    $hinh_anh = $news['HinhAnh']; // Giữ ảnh cũ mặc định

    // Xử lý nếu có upload ảnh mới
    if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] === 0) {
        $ext = pathinfo($_FILES['hinh_anh']['name'], PATHINFO_EXTENSION);
        $file_name = "news_" . time() . "." . $ext;
        $target = "assets/images/news/" . $file_name;

        if (move_uploaded_file($_FILES['hinh_anh']['tmp_name'], $target)) {
            $hinh_anh = $file_name;
            // (Tùy chọn) Xóa ảnh cũ trong thư mục để nhẹ máy
            if (file_exists("assets/images/news/" . $news['HinhAnh'])) {
                unlink("assets/images/news/" . $news['HinhAnh']);
            }
        }
    }

    $sql = "UPDATE TinTuc SET TieuDe = ?, NoiDung = ?, HinhAnh = ? WHERE MaTinTuc = ?";
    $pdo->prepare($sql)->execute([$tieu_de, $noi_dung, $hinh_anh, $id]);

    header("Location: admin.php#news");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa tin tức | Solpix Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: #f4f4f4;
            padding: 50px;
        }

        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            border: 1px solid #000;
        }

        h2 {
            letter-spacing: 5px;
            text-transform: uppercase;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            font-family: inherit;
            box-sizing: border-box;
        }

        textarea {
            height: 300px;
            line-height: 1.6;
        }

        .current-img {
            margin: 10px 0;
            display: block;
            border: 1px solid #eee;
            padding: 5px;
        }

        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            padding: 15px 30px;
            border: 1px solid #000;
            cursor: pointer;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-decoration: none;
            font-size: 12px;
            transition: 0.3s;
        }

        .btn-black {
            background: #000;
            color: #fff;
        }

        .btn:hover {
            opacity: 0.7;
        }
    </style>
</head>

<body>

    <div class="form-container">
        <h2>Chỉnh sửa bài viết</h2>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Tiêu đề bài viết</label>
                <input type="text" name="tieu_de" value="<?php echo htmlspecialchars($news['TieuDe']); ?>" required>
            </div>

            <div class="form-group">
                <label>Ảnh đại diện hiện tại</label>
                <img src="assets/images/news/<?php echo $news['HinhAnh']; ?>" width="200" class="current-img">
                <input type="file" name="hinh_anh" accept="image/*">
                <small style="color: #888; font-size: 10px;">(Để trống nếu không muốn thay đổi ảnh)</small>
            </div>

            <div class="form-group">
                <label>Nội dung bài viết</label>
                <textarea name="noi_dung" required><?php echo htmlspecialchars($news['NoiDung']); ?></textarea>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-black">Cập nhật bài viết</button>
                <a href="admin.php#news" class="btn">Hủy bỏ</a>
            </div>
        </form>
    </div>

</body>

</html>