<?php
session_start();
require_once __DIR__ . '/config/db.php';

// 1. Bảo mật: Chặn người dùng thường
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

$success = false;
$error_message = '';

// 2. Xử lý khi Admin nhấn nút Đăng tin
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tieu_de = $_POST['tieu_de'];
    $noi_dung = $_POST['noi_dung'];

    $hinh_anh = "";
    if (!empty($_FILES['hinh_anh']['name'])) {
        $hinh_anh = time() . "_" . $_FILES['hinh_anh']['name'];
        $target_dir = "assets/images/news/";

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        move_uploaded_file($_FILES['hinh_anh']['tmp_name'], $target_dir . $hinh_anh);
    }

    try {
        $sql = "INSERT INTO TinTuc (tieude, noidung, hinhanh, ngaydang) VALUES (?, ?, ?, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$tieu_de, $noi_dung, $hinh_anh]);

        $success = true; // Đánh dấu thành công
    } catch (PDOException $e) {
        $error_message = "Lỗi Database: " . $e->getMessage();
    }
}

?>

<?php if ($success): ?>
    <div id="success-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.96); z-index: 9999; display: flex; justify-content: center; align-items: center; text-align: center;">
        <div>
            <div style="width: 100px; height: 100px; border: 3px solid #000; border-radius: 50%; display: flex; justify-content: center; align-items: center; margin: 0 auto 30px;">
                <span style="font-size: 50px; font-weight: bold;">✓</span>
            </div>
            <h2 style="letter-spacing: 10px; text-transform: uppercase;">Đăng tin thành công</h2>
            <p style="color: #888; font-size: 13px; margin-top: 10px;">Hệ thống đang chuyển hướng bạn về trang quản trị...</p>
        </div>
    </div>
    <script>
        setTimeout(function() {
            window.location.href = 'admin.php#news';
        }, 2000);
    </script>
<?php endif; ?>

<div class="container" style="padding: 60px 0; min-height: 80vh;">
    <div style="max-width: 750px; margin: 0 auto; border: 1px solid #000; padding: 50px; background: #fff;">
        <h2 style="text-align: center; letter-spacing: 5px; text-transform: uppercase; margin-bottom: 40px;">Thêm tin tức mới</h2>

        <?php if ($error_message): ?>
            <div style="background: #ff4d4d; color: #fff; padding: 15px; text-align: center; margin-bottom: 30px; font-size: 13px;">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div style="margin-bottom: 25px;">
                <label style="display: block; font-weight: bold; font-size: 11px; letter-spacing: 2px; margin-bottom: 10px;">TIÊU ĐỀ BÀI VIẾT</label>
                <input type="text" name="tieu_de" required style="width: 100%; padding: 15px; border: 1px solid #ccc; box-sizing: border-box; font-family: 'Montserrat', sans-serif;">
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display: block; font-weight: bold; font-size: 11px; letter-spacing: 2px; margin-bottom: 10px;">NỘI DUNG CHI TIẾT</label>
                <textarea name="noi_dung" rows="12" required style="width: 100%; padding: 15px; border: 1px solid #ccc; box-sizing: border-box; line-height: 1.8; font-family: 'Montserrat', sans-serif;"></textarea>
            </div>

            <div style="margin-bottom: 40px;">
                <label style="display: block; font-weight: bold; font-size: 11px; letter-spacing: 2px; margin-bottom: 10px;">HÌNH ẢNH MINH HỌA</label>
                <input type="file" name="hinh_anh" accept="image/*" required style="font-size: 12px;">
                <p style="font-size: 10px; color: #888; margin-top: 8px; letter-spacing: 1px;">* ĐỊNH DẠNG: JPG, PNG, WEBP. TỐI ĐA 2MB.</p>
            </div>

            <button type="submit" style="width: 100%; background: #000; color: #fff; padding: 20px; border: none; font-weight: bold; letter-spacing: 3px; cursor: pointer; text-transform: uppercase; transition: 0.3s;">
                XÁC NHẬN XUẤT BẢN
            </button>

            <a href="admin.php" style="display: block; text-align: center; margin-top: 25px; color: #999; text-decoration: none; font-size: 11px; letter-spacing: 1px;">← HỦY BỎ VÀ QUAY LẠI</a>
        </form>
    </div>
</div>