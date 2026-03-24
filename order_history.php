<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/db.php';

// Bảo mật
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Truy vấn đơn hàng của khách
$sql = "SELECT * FROM DonHang WHERE MaNguoiDung = ? ORDER BY NgayDat DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding: 80px 5%; min-height: 70vh;">
    <h2 style="text-align: center; letter-spacing: 5px; text-transform: uppercase; margin-bottom: 50px; font-weight: 300;">Lịch sử đặt hàng</h2>

    <?php if (count($orders) > 0): ?>
        <table style="width: 100%; border-collapse: collapse; background: #fff; border: 1px solid #eee; font-size: 13px;">
            <thead>
                <tr style="background: #fafafa; border-bottom: 2px solid #000; text-transform: uppercase; letter-spacing: 1px;">
                    <th style="padding: 20px; text-align: left;">Mã đơn</th>
                    <th style="padding: 20px; text-align: left;">Ngày đặt</th>
                    <th style="padding: 20px; text-align: left;">Tổng tiền</th>
                    <th style="padding: 20px; text-align: left;">Trạng thái</th>
                    <th style="padding: 20px; text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <?php
                    $db_status = trim($order['TrangThaiDonHang'] ?? 'Chờ xác nhận');

                    // Định nghĩa màu sắc theo trạng thái
                    $color = '#000';
                    if ($db_status === 'Chờ thanh toán') $color = '#dbc119'; // Cam nhạt
                    if ($db_status === 'Chờ xác nhận') $color = '#e67e22';  // Cam đậm
                    if ($db_status === 'Đã xác nhận') $color = '#27ae60';   // Xanh lá
                    ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 20px; font-weight: bold;">#<?php echo $order['MaDonHang']; ?></td>
                        <td style="padding: 20px; color: #666;">
                            <?php echo date('d/m/Y H:i', strtotime($order['NgayDat'])); ?>
                        </td>
                        <td style="padding: 20px; font-weight: bold;">
                            <?php echo number_format($order['TongTien'], 0, ',', '.'); ?>đ
                        </td>
                        <td style="padding: 20px;">
                            <span style="color: <?php echo $color; ?>; font-weight: bold; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; border: 1px solid <?php echo $color; ?>; padding: 4px 8px; display: inline-block;">
                                <?php echo $db_status; ?>
                            </span>
                        </td>
                        <td style="padding: 20px; text-align: center;">
                            <?php if ($db_status === 'Chờ thanh toán'): ?>
                                <a href="payment.php?id=<?php echo $order['MaDonHang']; ?>"
                                    style="background: #c19f18; color: #fff; padding: 8px 15px; text-decoration: none; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s;">
                                    Thanh toán ngay
                                </a>
                            <?php else: ?>
                                <a href="order_detail.php?id=<?php echo $order['MaDonHang']; ?>"
                                    style="color: #000; text-decoration: underline; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">
                                    Xem chi tiết
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align: center; padding: 100px; border: 1px dashed #ccc;">
            <p style="color: #888; margin-bottom: 20px;">Bạn chưa thực hiện đơn hàng nào.</p>
            <a href="products.php" style="background: #000; color: #fff; padding: 15px 30px; display: inline-block; text-decoration: none; font-weight: bold; font-size: 12px; letter-spacing: 2px;">MUA SẮM NGAY</a>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>