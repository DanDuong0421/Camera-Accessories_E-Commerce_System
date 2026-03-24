<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/db.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_msg'] = "Vui lòng đăng nhập để thực hiện thanh toán!";
    header("Location: login.php");
    exit();
}

// 2. Chặn Admin mua hàng
if (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin') {
    header("Location: admin.php");
    exit();
}

// 3. Giỏ hàng trống thì quay lại
if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

include __DIR__ . '/includes/header.php';

// Tính tổng tiền để khách xem lại lần cuối
$total = 0;
foreach ($_SESSION['cart'] as $id => $quantity) {
    $stmt = $pdo->prepare("SELECT Gia, PhanTramGiam FROM SanPham WHERE MaSanPham = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    $price = $product['Gia'] * (1 - ($product['PhanTramGiam'] / 100));
    $total += $price * $quantity;
}
?>

<div class="container" style="min-height: 70vh; padding: 80px 0;">
    <h2 style="text-align:center; letter-spacing: 10px; text-transform: uppercase; margin-bottom: 50px; font-weight: 300;">Thanh toán</h2>

    <div style="max-width: 800px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">

        <div style="border: 1px solid #eee; padding: 30px; background: #fafafa;">
            <h3 style="font-size: 14px; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 20px;">Tóm tắt đơn hàng</h3>
            <p style="font-size: 13px; color: #666;">Người đặt: <strong><?php echo htmlspecialchars($_SESSION['ho_ten']); ?></strong></p>
            <p style="font-size: 13px; color: #666; margin: 10px 0;">Số lượng mặt hàng: <strong><?php echo array_sum($_SESSION['cart']); ?></strong></p>
            <hr style="border: 0; border-top: 1px solid #ddd; margin: 20px 0;">
            <p style="font-size: 18px; letter-spacing: 1px;">TỔNG CỘNG: <br><strong style="font-size: 24px;"><?php echo number_format($total, 0, ',', '.'); ?>đ</strong></p>
        </div>

        <div style="border: 1px solid #000; padding: 30px;">
            <h3 style="font-size: 14px; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 20px;">Phương thức thanh toán</h3>

            <form action="process_order.php" method="POST">
                <div style="margin-bottom: 15px; border: 1px solid #eee; padding: 15px;">
                    <input type="radio" name="payment_method" value="COD" id="cod" checked>
                    <label for="cod" style="font-size: 13px; cursor: pointer;">Thanh toán khi nhận hàng (COD)</label>
                </div>

                <div style="margin-bottom: 30px; border: 1px solid #eee; padding: 15px;">
                    <input type="radio" name="payment_method" value="QR" id="qr">
                    <label for="qr" style="font-size: 13px; cursor: pointer;">Chuyển khoản qua Mã QR (VietQR)</label>
                </div>

                <button type="submit" style="background:#000; color:#fff; padding:18px; border:none; cursor:pointer; font-weight:bold; letter-spacing:2px; width: 100%; text-transform: uppercase; font-size: 12px;">
                    Hoàn tất đặt hàng
                </button>
            </form>

            <p style="text-align: center; margin-top: 20px;">
                <a href="cart.php" style="color: #888; text-decoration: none; font-size: 11px; letter-spacing: 1px;">← QUAY LẠI GIỎ HÀNG</a>
            </p>
        </div>

    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>