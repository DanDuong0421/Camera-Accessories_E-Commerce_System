<?php
session_start();
require_once __DIR__ . '/config/db.php';

// 1. Bảo mật
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

$success = false;
$error_message = '';

// 2. Xử lý Form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten_danh_muc = trim($_POST['ten_danh_muc']);

    if (!empty($ten_danh_muc)) {
        try {
            $sql = "INSERT INTO DanhMuc (TenDanhMuc) VALUES (?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$ten_danh_muc]);

            $success = true; // Đánh dấu để hiện overlay
        } catch (PDOException $e) {
            $error_message = "Lỗi: " . $e->getMessage();
        }
    } else {
        $error_message = "Vui lòng nhập tên danh mục!";
    }
}

?>

<?php if ($success): ?>
    <div id="success-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.96); z-index: 9999; display: flex; justify-content: center; align-items: center; text-align: center;">
        <div>
            <div style="width: 100px; height: 100px; border: 3px solid #000; border-radius: 50%; display: flex; justify-content: center; align-items: center; margin: 0 auto 30px;">
                <span style="font-size: 50px;">✓</span>
            </div>
            <h2 style="letter-spacing: 10px; text-transform: uppercase;">Thêm danh mục thành công</h2>
            <p style="color: #888; font-size: 13px; margin-top: 10px;">Đang quay lại trang quản trị...</p>
        </div>
    </div>
    <script>
        setTimeout(function() {
            window.location.href = 'admin.php#categories';
        }, 1500);
    </script>
<?php endif; ?>

<div class="container" style="padding: 100px 0; min-height: 60vh;">
    <div style="max-width: 500px; margin: 0 auto; border: 1px solid #000; padding: 50px; background: #fff;">
        <h2 style="text-align: center; letter-spacing: 5px; text-transform: uppercase; margin-bottom: 40px;">Thêm danh mục mới</h2>

        <?php if ($error_message): ?>
            <div style="background: #ff4d4d; color: #fff; padding: 15px; text-align: center; margin-bottom: 25px; font-size: 12px;">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div style="margin-bottom: 30px;">
                <label style="display: block; font-weight: bold; font-size: 11px; letter-spacing: 2px; margin-bottom: 10px;">TÊN DANH MỤC</label>
                <input type="text" name="ten_danh_muc" placeholder="Ví dụ: Máy ảnh Leica, Flycam..." required
                    style="width: 100%; padding: 15px; border: 1px solid #ccc; box-sizing: border-box; font-family: 'Montserrat', sans-serif;">
            </div>

            <button type="submit" style="width: 100%; background: #000; color: #fff; padding: 20px; border: none; font-weight: bold; letter-spacing: 3px; cursor: pointer; text-transform: uppercase;">
                XÁC NHẬN THÊM
            </button>

            <a href="admin.php" style="display: block; text-align: center; margin-top: 25px; color: #999; text-decoration: none; font-size: 11px; letter-spacing: 1px;">← QUAY LẠI</a>
        </form>
    </div>
</div>