<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ho_ten = trim($_POST['ho_ten']);
    $tai_khoan = trim($_POST['tai_khoan']);
    $mat_khau = $_POST['mat_khau'];
    $email = trim($_POST['email']);
    $sdt = trim($_POST['sdt']);
    $dia_chi = trim($_POST['dia_chi']);

    // Kiểm tra tài khoản tồn tại
    $stmt = $pdo->prepare("SELECT * FROM NguoiDung WHERE TaiKhoan = ?");
    $stmt->execute([$tai_khoan]);

    if ($stmt->rowCount() > 0) {
        $error = "Tên tài khoản này đã được sử dụng!";
    } else {
        try {
            // Thêm đầy đủ thông tin vào bảng NguoiDung
            $sql = "INSERT INTO NguoiDung (HoTen, TaiKhoan, MatKhau, Email, SoDienThoai, DiaChi, PhanQuyen) 
                    VALUES (?, ?, ?, ?, ?, ?, 'User')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$ho_ten, $tai_khoan, $mat_khau, $email, $sdt, $dia_chi]);

            $success = "Tạo tài khoản thành công! Đang chuyển hướng...";
            header("refresh:2;url=login.php");
        } catch (PDOException $e) {
            $error = "Lỗi: " . $e->getMessage();
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<style>
    .register-wrapper {
        padding: 80px 0;
        display: flex;
        justify-content: center;
        background: #fafafa;
    }

    .register-card {
        width: 100%;
        max-width: 600px;
        background: #fff;
        border: 1px solid #000;
        padding: 50px;
    }

    .register-card h2 {
        text-align: center;
        letter-spacing: 8px;
        text-transform: uppercase;
        margin-bottom: 40px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        font-size: 11px;
        font-weight: bold;
        letter-spacing: 1px;
        margin-bottom: 8px;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ccc;
        box-sizing: border-box;
        font-family: 'Montserrat', sans-serif;
    }

    .full-width {
        grid-column: 1 / span 2;
    }

    .btn-register {
        width: 100%;
        padding: 18px;
        background: #000;
        color: #fff;
        border: none;
        font-weight: bold;
        letter-spacing: 3px;
        cursor: pointer;
        text-transform: uppercase;
        transition: 0.3s;
    }

    .btn-register:hover {
        opacity: 0.8;
    }
</style>

<div class="register-wrapper">
    <div class="register-card">
        <h2>Join Solpix</h2>

        <?php if ($error): ?>
            <div style="background:#000; color:#fff; padding:15px; text-align:center; margin-bottom:20px; font-size:12px;"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div style="border:1px solid #000; padding:15px; text-align:center; margin-bottom:20px; font-size:12px; font-weight:bold;"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>TÀI KHOẢN (USERNAME)</label>
                    <input type="text" name="tai_khoan" required>
                </div>
                <div class="form-group">
                    <label>MẬT KHẨU</label>
                    <input type="password" name="mat_khau" required>
                </div>

                <div class="form-group">
                    <label>HỌ VÀ TÊN</label>
                    <input type="text" name="ho_ten" required>
                </div>
                <div class="form-group">
                    <label>SỐ ĐIỆN THOẠI</label>
                    <input type="tel" name="sdt" required>
                </div>

                <div class="form-group full-width">
                    <label>EMAIL</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group full-width">
                    <label>ĐỊA CHỈ GIAO HÀNG</label>
                    <textarea name="dia_chi" rows="3" required></textarea>
                </div>
            </div>

            <button type="submit" class="btn-register">XÁC NHẬN ĐĂNG KÝ</button>
        </form>

        <p style="text-align: center; margin-top: 30px; font-size: 11px; color: #888; letter-spacing: 1px;">
            ĐÃ CÓ TÀI KHOẢN? <a href="login.php" style="color: #000; font-weight: bold; text-decoration: underline;">ĐĂNG NHẬP TẠI ĐÂY</a>
        </p>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>