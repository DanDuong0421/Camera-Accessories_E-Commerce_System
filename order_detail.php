<?php
session_start();
require_once __DIR__ . '/config/db.php';

// 1. Bảo mật
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

$ma_don_hang = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. Truy vấn thông tin đơn hàng & khách hàng
$sql_order = "SELECT d.*, n.HoTen, n.Email, n.SoDienThoai, n.DiaChi 
              FROM DonHang d 
              JOIN NguoiDung n ON d.MaNguoiDung = n.MaNguoiDung 
              WHERE d.MaDonHang = ?";
$stmt = $pdo->prepare($sql_order);
$stmt->execute([$ma_don_hang]);
$order = $stmt->fetch();

if (!$order) {
    die("Đơn hàng không tồn tại!");
}

// 3. Truy vấn danh sách sản phẩm
$sql_items = "SELECT c.*, s.TenSanPham, s.HinhAnh 
              FROM ChiTietDonHang c 
              JOIN SanPham s ON c.MaSanPham = s.MaSanPham 
              WHERE c.MaDonHang = ?";
$stmt_items = $pdo->prepare($sql_items);
$stmt_items->execute([$ma_don_hang]);
$items = $stmt_items->fetchAll();

$status = trim($order['TrangThaiDonHang'] ?? 'Chờ xác nhận');
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng #<?php echo $ma_don_hang; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: #f9f9f9;
            padding: 50px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #000;
            padding: 40px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .info-box h4 {
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-top: 0;
        }

        .info-box p {
            font-size: 13px;
            line-height: 1.8;
            color: #555;
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            text-align: left;
            padding: 15px;
            background: #f0f0f0;
            font-size: 11px;
            text-transform: uppercase;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .total-row {
            font-weight: bold;
            font-size: 18px;
            text-align: right;
            padding: 20px;
            border-top: 2px solid #000;
            margin-top: 20px;
        }

        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            text-decoration: none;
            color: #000;
            font-size: 11px;
            font-weight: bold;
            border: 1px solid #000;
            padding: 12px 20px;
            text-transform: uppercase;
            transition: 0.3s;
            cursor: pointer;
        }

        .btn-black {
            background: #000;
            color: #fff;
        }

        .btn:hover {
            opacity: 0.7;
        }

        .bill-preview {
            margin-top: 20px;
            border: 1px dashed #ccc;
            padding: 15px;
            text-align: center;
        }

        /* Đẩy nút in hóa đơn sang phải */
        .btn-print {
            margin-left: auto;
        }

        @media print {

            .btn-group,
            .bill-preview {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <h2 style="letter-spacing: 5px; margin: 0;">ĐƠN HÀNG #<?php echo $ma_don_hang; ?></h2>
            <p style="margin: 0;">Ngày đặt: <?php echo date('d/m/Y H:i', strtotime($order['NgayDat'])); ?></p>
        </div>

        <div class="info-grid">
            <div class="info-box">
                <h4>Khách hàng</h4>
                <p><strong><?php echo htmlspecialchars($order['HoTen']); ?></strong></p>
                <p>SĐT: <?php echo htmlspecialchars($order['SoDienThoai']); ?></p>
                <p>Email: <?php echo htmlspecialchars($order['Email']); ?></p>
            </div>
            <div class="info-box">
                <h4>Giao hàng</h4>
                <p><?php echo nl2br(htmlspecialchars($order['DiaChi'])); ?></p>
                <p>
                    Trạng thái hiện tại:
                    <?php
                    $color = '#000';
                    if ($status == 'Chờ thanh toán') $color = '#f39c12';
                    if ($status == 'Chờ xác nhận') $color = '#d86721';
                    if ($status == 'Đã xác nhận') $color = '#2e7d32';
                    ?>
                    <b style="color: <?php echo $color; ?>; text-transform: uppercase;"><?php echo $status; ?></b>
                </p>
            </div>
        </div>

        <?php if (!empty($order['HinhAnhBill'])): ?>
            <div class="bill-preview">
                <h4 style="font-size: 11px; letter-spacing: 2px; margin-bottom: 10px;">MINH CHỨNG THANH TOÁN</h4>
                <a href="assets/images/bills/<?php echo $order['HinhAnhBill']; ?>" target="_blank">
                    <img src="assets/images/bills/<?php echo $order['HinhAnhBill']; ?>" style="max-width: 200px; border: 1px solid #ddd;">
                </a>
                <p style="font-size: 10px; color: #888; margin-top: 5px;">(Click vào ảnh để phóng to)</p>
            </div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td style="display: flex; align-items: center; gap: 15px;">
                            <img src="assets/images/products/<?php echo $item['HinhAnh']; ?>" width="50" onerror="this.src='https://via.placeholder.com/50'">
                            <?php echo htmlspecialchars($item['TenSanPham']); ?>
                        </td>
                        <td><?php echo number_format($item['GiaBan'], 0, ',', '.'); ?>đ</td>
                        <td><?php echo $item['SoLuong']; ?></td>
                        <td><?php echo number_format($item['GiaBan'] * $item['SoLuong'], 0, ',', '.'); ?>đ</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-row">TỔNG CỘNG: <?php echo number_format($order['TongTien'], 0, ',', '.'); ?>đ</div>

        <div class="btn-group">
            <a href="admin.php#orders" class="btn">← QUAY LẠI</a>

            <?php if ($status === 'Chờ thanh toán'): ?>
                <span style="color: #f39c12; font-size: 11px; font-weight: bold; letter-spacing: 1px;">[ ĐỢI KHÁCH THANH TOÁN & UP BILL ]</span>

                <a href="admin_action.php?action=delete_order&id=<?php echo $ma_don_hang; ?>"
                    class="btn" style="color: #d9534f; border-color: #d9534f;"
                    onclick="return confirm('Hủy đơn hàng chưa thanh toán này?')">HỦY ĐƠN</a>

            <?php elseif ($status === 'Chờ xác nhận'): ?>
                <a href="admin_action.php?action=confirm_order&id=<?php echo $ma_don_hang; ?>"
                    class="btn btn-black">XÁC NHẬN ĐƠN HÀNG</a>

                <a href="admin_action.php?action=delete_order&id=<?php echo $ma_don_hang; ?>"
                    class="btn" style="color: #d9534f; border-color: #d9534f;"
                    onclick="return confirm('Bạn có chắc chắn muốn HỦY đơn hàng này?')">HỦY ĐƠN</a>

            <?php elseif ($status === 'Đã xác nhận'): ?>
                <div style="padding: 10px 20px; border: 1px solid #2e7d32; color: #2e7d32; font-size: 11px; font-weight: bold;">✓ ĐÃ XÁC NHẬN</div>

                <a href="print_invoice.php?id=<?php echo $ma_don_hang; ?>" target="_blank" class="btn btn-black btn-print">XUẤT HÓA ĐƠN (PDF/PRINT)</a>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>