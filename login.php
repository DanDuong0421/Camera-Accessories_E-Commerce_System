<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Nạp file kết nối từ database 'mayanh'
require_once __DIR__ . '/config/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tai_khoan = $_POST['tai_khoan'];
    $mat_khau = $_POST['mat_khau'];

    // Truy vấn lấy toàn bộ thông tin người dùng
    $stmt = $pdo->prepare("SELECT * FROM NguoiDung WHERE TaiKhoan = ?");
    $stmt->execute([$tai_khoan]);
    $user = $stmt->fetch();

    if ($user && $mat_khau == $user['MatKhau']) {
        // Lưu thông tin vào Session
        $_SESSION['user_id'] = $user['MaNguoiDung'];
        $_SESSION['ho_ten'] = $user['HoTen'];

        // --- PHẦN QUAN TRỌNG: LƯU QUYỀN VÀO SESSION ---
        $_SESSION['role'] = $user['PhanQuyen'];

        // Kiểm tra quyền để điều hướng
        if ($_SESSION['role'] === 'Admin') {
            // Nếu là Admin, dẫn sang trang quản trị
            header("Location: admin.php");
        } else {
            // Nếu là User thường, dẫn về trang chủ
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "Tài khoản hoặc mật khẩu không đúng!";
    }
}

include $_SERVER['DOCUMENT_ROOT'] . '/SolpixStore/includes/header.php';
?>

<style>
    .login-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 70vh;
        padding: 20px;
    }

    .login-card {
        width: 100%;
        max-width: 450px;
        border: 1px solid #000;
        padding: 40px;
        background: #fff;
    }

    .login-card h2 {
        text-align: center;
        letter-spacing: 10px;
        margin-bottom: 40px;
        font-weight: 400;
        text-transform: uppercase;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group input {
        width: 100%;
        padding: 15px 20px;
        font-size: 16px;
        border: 1px solid #ccc;
        box-sizing: border-box;
        font-family: 'Montserrat', sans-serif;
        transition: border 0.3s ease;
    }

    .form-group input:focus {
        border-color: #000;
        outline: none;
    }

    .btn-login-submit {
        width: 100%;
        padding: 15px;
        background: #000;
        color: #fff;
        border: none;
        font-size: 14px;
        font-weight: bold;
        letter-spacing: 3px;
        cursor: pointer;
        text-transform: uppercase;
        transition: opacity 0.3s;
    }

    .btn-login-submit:hover {
        opacity: 0.8;
    }

    .error-msg {
        color: #d9534f;
        text-align: center;
        margin-bottom: 20px;
        font-size: 14px;
    }

    /* Style mới cho thông báo từ Checkout */
    .checkout-alert {
        background: #000;
        color: #fff;
        padding: 10px;
        text-align: center;
        font-size: 12px;
        letter-spacing: 1px;
        margin-bottom: 20px;
        text-transform: uppercase;
    }

    .back-link {
        display: block;
        text-align: center;
        margin-top: 25px;
        color: #888;
        text-decoration: none;
        font-size: 12px;
        letter-spacing: 1px;
    }

    .back-link:hover {
        color: #000;
    }
</style>

<div class="login-wrapper">
    <div class="login-card">
        <?php if (isset($_SESSION['error_msg'])): ?>
            <div class="checkout-alert">
                <?php
                echo $_SESSION['error_msg'];
                unset($_SESSION['error_msg']); // Hiện xong thì xóa để lần sau không hiện lại thừa
                ?>
            </div>
        <?php endif; ?>

        <h2>LOGIN</h2>

        <?php if ($error): ?>
            <p class="error-msg"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <input type="text" name="tai_khoan" placeholder="USERNAME" required>
            </div>
            <div class="form-group">
                <input type="password" name="mat_khau" placeholder="PASSWORD" required>
            </div>
            <button type="submit" class="btn-login-submit">ĐĂNG NHẬP</button>
        </form>

        <a href="index.php" class="back-link">← QUAY LẠI TRANG CHỦ</a>
        <a href="dangky.php" class="back-link">ĐĂNG KÝ</a>
    </div>
</div>

<?php
include __DIR__ . '/includes/footer.php';
?>