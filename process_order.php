<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/db.php';

// 1. Kiểm tra bảo mật
if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

// Nhận phương thức thanh toán từ checkout.php
$payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'COD';

try {
    $pdo->beginTransaction();

    // 2. Tính tổng tiền (Sử dụng đồng bộ logic giảm giá %)
    $total = 0;
    foreach ($_SESSION['cart'] as $id => $quantity) {
        $stmt = $pdo->prepare("SELECT Gia, PhanTramGiam FROM SanPham WHERE MaSanPham = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        $discount = (int)$product['PhanTramGiam'];
        $price = ($discount > 0) ? $product['Gia'] * (1 - ($discount / 100)) : $product['Gia'];
        $total += $price * $quantity;
    }

    // 3. Phân luồng trạng thái
    // Nếu QR: 'Chờ thanh toán' (đợi khách up ảnh bill ở payment.php)
    // Nếu COD: 'Chờ xác nhận' (quy trình truyền thống)
    $trang_thai = ($payment_method === 'QR') ? 'Chờ thanh toán' : 'Chờ xác nhận';
    $maNguoiDung = $_SESSION['user_id'];

    $sqlDonHang = "INSERT INTO DonHang (MaNguoiDung, TongTien, TrangThaiDonHang, NgayDat) VALUES (?, ?, ?, NOW())";
    $stmtDH = $pdo->prepare($sqlDonHang);
    $stmtDH->execute([$maNguoiDung, $total, $trang_thai]);

    $maDonHang = $pdo->lastInsertId();

    // 4. Lưu Chi tiết & Cập nhật kho
    foreach ($_SESSION['cart'] as $id => $quantity) {
        $stmtInfo = $pdo->prepare("SELECT Gia, PhanTramGiam, SoLuongTon FROM SanPham WHERE MaSanPham = ?");
        $stmtInfo->execute([$id]);
        $pInfo = $stmtInfo->fetch();

        $discount = (int)$pInfo['PhanTramGiam'];
        $gia_ban = ($discount > 0) ? $pInfo['Gia'] * (1 - ($discount / 100)) : $pInfo['Gia'];

        // Lưu chi tiết đơn hàng
        $sqlCT = "INSERT INTO ChiTietDonHang (MaDonHang, MaSanPham, SoLuong, GiaBan) VALUES (?, ?, ?, ?)";
        $pdo->prepare($sqlCT)->execute([$maDonHang, $id, $quantity, $gia_ban]);

        // Cập nhật tồn kho
        $newStock = $pInfo['SoLuongTon'] - $quantity;
        if ($newStock < 0) throw new Exception("Sản phẩm " . $pInfo['TenSanPham'] . " đã hết hàng!");

        $sqlStock = "UPDATE SanPham SET SoLuongTon = ? WHERE MaSanPham = ?";
        $pdo->prepare($sqlStock)->execute([$newStock, $id]);
    }

    $pdo->commit();
    unset($_SESSION['cart']);

    // 5. CHUYỂN HƯỚNG THEO PHƯƠNG THỨC
    if ($payment_method === 'QR') {
        header("Location: payment.php?id=" . $maDonHang);
        exit();
    }
?>
    <!DOCTYPE html>
    <html lang="vi">

    <head>
        <meta charset="UTF-8">
        <title>Đặt hàng thành công | Solpix Store</title>
        <style>
            body {
                font-family: 'Montserrat', sans-serif;
                margin: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                text-align: center;
            }

            .circle {
                width: 80px;
                height: 80px;
                border: 2px solid #000;
                border-radius: 50%;
                display: flex;
                justify-content: center;
                align-items: center;
                margin: 0 auto 20px;
                font-size: 40px;
                color: #000;
            }

            .success-text {
                letter-spacing: 5px;
                text-transform: uppercase;
                font-weight: 300;
            }
        </style>
    </head>

    <body>
        <div>
            <div class="circle">✓</div>
            <h2 class="success-text">Đặt hàng thành công</h2>
            <p style="color: #888; font-size: 12px;">Đơn hàng COD của bạn đã được ghi nhận.</p>
        </div>
        <script>
            setTimeout(() => {
                window.location.href = 'index.php';
            }, 3000);
        </script>
    </body>

    </html>
<?php
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo "<div style='padding:50px; text-align:center; font-family: Montserrat;'>";
    echo "<h2 style='color: #ff4d4d;'>Lỗi hệ thống</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<a href='cart.php' style='color: #000; font-weight: bold;'>Quay lại giỏ hàng</a></div>";
}
