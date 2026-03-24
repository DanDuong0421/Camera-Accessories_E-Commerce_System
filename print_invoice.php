<?php
session_start();
require_once __DIR__ . '/config/db.php';

// 1. Bảo mật
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    die("Quyền truy cập bị từ chối!");
}

$ma_don_hang = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. Lấy thông tin đơn hàng & Khách hàng
$sql_order = "SELECT d.*, n.HoTen, n.Email, n.SoDienThoai, n.DiaChi 
              FROM DonHang d 
              JOIN NguoiDung n ON d.MaNguoiDung = n.MaNguoiDung 
              WHERE d.MaDonHang = ?";
$stmt = $pdo->prepare($sql_order);
$stmt->execute([$ma_don_hang]);
$order = $stmt->fetch();

if (!$order) die("Không tìm thấy đơn hàng!");

// 3. Lấy danh sách sản phẩm
$sql_items = "SELECT c.*, s.TenSanPham FROM ChiTietDonHang c 
              JOIN SanPham s ON c.MaSanPham = s.MaSanPham 
              WHERE c.MaDonHang = ?";
$stmt_items = $pdo->prepare($sql_items);
$stmt_items->execute([$ma_don_hang]);
$items = $stmt_items->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn #<?php echo $ma_don_hang; ?></title>

    <link rel="icon" href="/SolpixStore/assets/images/logo/logo.ico?v=1" type="image/x-icon">
    <link rel="shortcut icon" href="/SolpixStore/assets/images/logo/logo.ico?v=1" type="image/x-icon">

    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            color: #000;
            padding: 20px;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            border: 1px solid #eee;
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            /* Giúp logo và text thẳng hàng trục giữa */
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .logo-group {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-text {
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 5px;
            white-space: nowrap;
            /* Không cho chữ xuống hàng */
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background: #f2f2f2;
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        .total {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
        }

        .footer-note {
            margin-top: 50px;
            text-align: center;
            font-style: italic;
            font-size: 12px;
        }

        /* CSS dành riêng khi in */
        @media print {
            .no-print {
                display: none;
            }

            .invoice-box {
                border: none;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; font-weight: bold;">BẤM VÀO ĐÂY ĐỂ IN</button>
        <a href="order_detail.php?id=<?php echo $ma_don_hang; ?>" style="margin-left: 10px; text-decoration: none; color: #666;">← Quay lại</a>
    </div>

    <div class="invoice-box">
        <div class="header">
            <div class="logo-group">
                <img src="/SolpixStore/assets/images/logo/logo.ico" alt="Solpix Store" style="width: 60px; height: 60px; object-fit: contain;">
                <div class="logo-text">SOLPIX STORE</div>
            </div>

            <div style="text-align: right;">
                <p style="margin: 0; font-size: 18px;"><strong>HÓA ĐƠN BÁN HÀNG</strong></p>
                <p style="margin: 5px 0 0 0;">Mã đơn: #<?php echo $ma_don_hang; ?></p>
            </div>
        </div>

        <div class="info-section">
            <div>
                <p><strong>TỪ:</strong></p>
                <p style="font-weight: bold; margin-bottom: 5px;">SOLPIX STORE</p>
                <p>Ung Văn Khiêm, Long Xuyên, An Giang</p>
                <p>Hotline: 0854171599</p>
            </div>
            <div style="text-align: right;">
                <p><strong>ĐẾN:</strong></p>
                <p style="font-weight: bold; margin-bottom: 5px;"><?php echo htmlspecialchars($order['HoTen']); ?></p>
                <p><?php echo nl2br(htmlspecialchars($order['DiaChi'])); ?></p>
                <p>SĐT: <?php echo htmlspecialchars($order['SoDienThoai']); ?></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">STT</th>
                    <th>Sản phẩm</th>
                    <th style="width: 80px; text-align: center;">Số lượng</th>
                    <th style="width: 120px;">Đơn giá</th>
                    <th style="width: 140px; text-align: right;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1;
                foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($item['TenSanPham']); ?></td>
                        <td style="text-align: center;"><?php echo $item['SoLuong']; ?></td>
                        <td><?php echo number_format($item['GiaBan'], 0, ',', '.'); ?>đ</td>
                        <td style="text-align: right;"><?php echo number_format($item['GiaBan'] * $item['SoLuong'], 0, ',', '.'); ?>đ</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total">
            TỔNG CỘNG: <?php echo number_format($order['TongTien'], 0, ',', '.'); ?>đ
        </div>

        <div class="footer-note">
            <p>Cảm ơn quý khách đã tin dùng sản phẩm của Solpix!</p>
            <p>Ngày in: <?php echo date('d/m/Y H:i'); ?></p>
        </div>
    </div>

</body>

</html>