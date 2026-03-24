<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$error = "";

// XỬ LÝ CẬP NHẬT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Cập nhật thông tin cá nhân
    if (isset($_POST['update_info'])) {
        $ho_ten = $_POST['ho_ten'];
        $email = $_POST['email'];
        $sdt = $_POST['sdt'];
        $dia_chi = $_POST['dia_chi'];

        $sql = "UPDATE NguoiDung SET HoTen = ?, Email = ?, SoDienThoai = ?, DiaChi = ? WHERE MaNguoiDung = ?";
        if ($pdo->prepare($sql)->execute([$ho_ten, $email, $sdt, $dia_chi, $user_id])) {
            $_SESSION['ho_ten'] = $ho_ten;
            $message = "Cập nhật thông tin thành công!";
        }
    }

    // 2. Cập nhật mật khẩu
    if (isset($_POST['update_password'])) {
        $old_pass = $_POST['old_password'];
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];

        // Lấy mật khẩu hiện tại trong DB
        $stmt = $pdo->prepare("SELECT MatKhau FROM NguoiDung WHERE MaNguoiDung = ?");
        $stmt->execute([$user_id]);
        $user_data = $stmt->fetch();

        // Kiểm tra logic (Lưu ý: Nếu bạn dùng password_hash thì dùng password_verify ở đây)
        if ($old_pass !== $user_data['MatKhau']) {
            $error = "Mật khẩu cũ không chính xác!";
        } elseif ($new_pass !== $confirm_pass) {
            $error = "Mật khẩu mới không khớp!";
        } else {
            $sql_pass = "UPDATE NguoiDung SET MatKhau = ? WHERE MaNguoiDung = ?";
            $pdo->prepare($sql_pass)->execute([$new_pass, $user_id]);
            $message = "Đổi mật khẩu thành công!";
        }
    }
}

// Lấy thông tin hiển thị lên form
$stmt = $pdo->prepare("SELECT * FROM NguoiDung WHERE MaNguoiDung = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

include __DIR__ . '/includes/header.php';
?>

<div class="container" style="max-width: 600px; margin: 0 auto; padding: 60px 20px;">
    <h2 style="text-align: center; letter-spacing: 5px; text-transform: uppercase;">Tài khoản của tôi</h2>

    <?php if ($message): ?>
        <p style="text-align: center; color: green; font-size: 13px;"><?php echo $message; ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
        <p style="text-align: center; color: red; font-size: 13px;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" style="margin-top: 40px; border-bottom: 1px solid #eee; padding-bottom: 40px;">
        <h3 style="font-size: 14px; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 20px;">Thông tin cá nhân</h3>
        <input type="text" name="ho_ten" value="<?php echo htmlspecialchars($user['HoTen']); ?>" placeholder="Họ tên" required style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; font-family: Montserrat;">
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>" placeholder="Email" required style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; font-family: Montserrat;">
        <input type="text" name="sdt" value="<?php echo htmlspecialchars($user['SoDienThoai']); ?>" placeholder="Số điện thoại" style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; font-family: Montserrat;">
        <textarea name="dia_chi" placeholder="Địa chỉ giao hàng" style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; font-family: Montserrat; height: 80px;"><?php echo htmlspecialchars($user['DiaChi']); ?></textarea>
        <button type="submit" name="update_info" style="width: 100%; background: #000; color: #fff; padding: 15px; border: none; font-weight: bold; cursor: pointer; letter-spacing: 2px;">LƯU THÔNG TIN</button>
    </form>

    <form method="POST" style="margin-top: 40px;">
        <h3 style="font-size: 14px; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 20px;">Đổi mật khẩu</h3>
        <input type="password" name="old_password" placeholder="Mật khẩu hiện tại" required style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; font-family: Montserrat;">
        <input type="password" name="new_password" placeholder="Mật khẩu mới" required style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; font-family: Montserrat;">
        <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu mới" required style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; font-family: Montserrat;">
        <button type="submit" name="update_password" style="width: 100%; background: #fff; color: #000; padding: 15px; border: 1px solid #000; font-weight: bold; cursor: pointer; letter-spacing: 2px;">ĐỔI MẬT KHẨU</button>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>