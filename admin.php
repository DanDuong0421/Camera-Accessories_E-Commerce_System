<?php
session_start();
// 1. Bảo mật: Chỉ Admin mới được vào
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// 2. Kết nối Database
require_once __DIR__ . '/config/db.php';

// 3. Lấy dữ liệu
$products = $pdo->query("SELECT p.*, d.TenDanhMuc FROM SanPham p LEFT JOIN DanhMuc d ON p.MaDanhMuc = d.MaDanhMuc ORDER BY MaSanPham DESC")->fetchAll();
$categories = $pdo->query("SELECT * FROM DanhMuc ORDER BY MaDanhMuc ASC")->fetchAll();
$news = $pdo->query("SELECT * FROM TinTuc ORDER BY NgayDang DESC")->fetchAll();
$orders = $pdo->query("SELECT d.*, n.HoTen FROM DonHang d JOIN NguoiDung n ON d.MaNguoiDung = n.MaNguoiDung ORDER BY NgayDat DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Solpix Admin | Quản trị hệ thống</title>
    <link rel="icon" href="/SolpixStore/assets/images/logo/logo.ico" type="image/x-icon">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            background: #f4f4f4;
            color: #333;
            scroll-behavior: smooth;
        }

        .sidebar {
            width: 240px;
            height: 100vh;
            background: #000;
            color: #fff;
            position: fixed;
            padding: 30px;
        }

        .sidebar h2 {
            letter-spacing: 4px;
            font-size: 20px;
            border-bottom: 1px solid #333;
            padding-bottom: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin-top: 30px;
        }

        .sidebar ul li {
            margin-bottom: 20px;
        }

        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 13px;
            letter-spacing: 1px;
            transition: 0.3s;
        }

        .sidebar ul li a:hover {
            color: #888;
        }

        .main-content {
            margin-left: 300px;
            padding: 40px;
        }

        .section-card {
            background: #fff;
            padding: 30px;
            border-radius: 2px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }

        .section-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-left: 4px solid #000;
            padding-left: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #fafafa;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Nút bấm */
        .btn {
            padding: 7px 12px;
            font-size: 10px;
            font-weight: bold;
            text-decoration: none;
            text-transform: uppercase;
            border: 1px solid #000;
            transition: 0.3s;
            display: inline-block;
            cursor: pointer;
        }

        .btn-black {
            background: #000;
            color: #fff;
        }

        .btn-outline {
            color: #000;
        }

        .btn-danger {
            color: #ff4d4d;
            border-color: #ff4d4d;
        }

        /* Màu sắc Trạng thái đơn hàng */
        .status-pay {
            color: #abb408;
            font-weight: bold;
            font-style: italic;
        }

        /* Vàng cam: Chờ thanh toán */
        .status-wait {
            color: #e59b11;
            font-weight: bold;
        }

        /* Cam đậm: Chờ xác nhận */
        .status-done {
            color: #27ae60;
            font-weight: bold;
        }

        /* Xanh lá: Đã xác nhận */

        .sale-tag {
            background: #ff4d4d;
            color: #fff;
            padding: 2px 5px;
            font-size: 10px;
            border-radius: 3px;
        }

        .news-thumb {
            width: 60px;
            height: 40px;
            object-fit: cover;
            border-radius: 2px;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h2>SOLPIX ADMIN</h2>
        <p style="font-size: 11px; color: #888;">Quản trị viên: <strong><?php echo $_SESSION['ho_ten']; ?></strong></p>
        <ul>
            <li><a href="#products">QUẢN LÝ SẢN PHẨM</a></li>
            <li><a href="#categories">QUẢN LÝ DANH MỤC</a></li>
            <li><a href="#news">QUẢN LÝ TIN TỨC</a></li>
            <li><a href="#orders">QUẢN LÝ ĐƠN HÀNG</a></li>
            <li><a href="index.php" target="_blank" style="color: #3498db;">XEM WEBSITE</a></li>
            <li><a href="logout.php" style="color: #ff4d4d;">ĐĂNG XUẤT</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="section-card" id="products">
            <div class="section-title">
                <h3>SẢN PHẨM</h3>
                <a href="admin_product_add.php" class="btn btn-black">+ THÊM MỚI</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Hình</th>
                        <th>Tên máy ảnh</th>
                        <th>Danh mục</th>
                        <th>Giá gốc</th>
                        <th>Giảm (%)</th>
                        <th>Tồn kho</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td><img src="assets/images/products/<?php echo $p['HinhAnh']; ?>" width="50" style="object-fit: cover;" onerror="this.src='https://via.placeholder.com/50'"></td>
                            <td><strong><?php echo htmlspecialchars($p['TenSanPham']); ?></strong></td>
                            <td><?php echo $p['TenDanhMuc']; ?></td>
                            <td><?php echo number_format($p['Gia'], 0, ',', '.'); ?>đ</td>
                            <td><?php echo ($p['PhanTramGiam'] > 0) ? '<span class="sale-tag">-' . $p['PhanTramGiam'] . '%</span>' : '<span style="color:#ccc;">0%</span>'; ?></td>
                            <td><?php echo ($p['SoLuongTon'] <= 0) ? '<span style="color:red; font-weight:bold;">Hết hàng</span>' : $p['SoLuongTon']; ?></td>
                            <td>
                                <a href="admin_product_edit.php?id=<?php echo $p['MaSanPham']; ?>" class="btn btn-outline">SỬA</a>
                                <a href="admin_action.php?action=delete_product&id=<?php echo $p['MaSanPham']; ?>" class="btn btn-danger" onclick="return confirm('Xóa sản phẩm này?')">XÓA</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="section-card" id="categories">
            <div class="section-title">
                <h3>DANH MỤC</h3>
                <a href="admin_category_add.php" class="btn btn-black">+ THÊM DANH MỤC</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên danh mục</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $c): ?>
                        <tr>
                            <td>#<?php echo $c['MaDanhMuc']; ?></td>
                            <td><strong><?php echo htmlspecialchars($c['TenDanhMuc']); ?></strong></td>
                            <td>
                                <a href="admin_action.php?action=delete_category&id=<?php echo $c['MaDanhMuc']; ?>" class="btn btn-danger" onclick="return confirm('Xóa danh mục này?')">XÓA</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="section-card" id="news">
            <div class="section-title">
                <h3>TIN TỨC</h3>
                <a href="admin_news_add.php" class="btn btn-black">+ VIẾT BÀI MỚI</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Ảnh</th>
                        <th>Tiêu đề bài viết</th>
                        <th>Ngày đăng</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($news as $n): ?>
                        <tr>
                            <td><img src="assets/images/news/<?php echo $n['HinhAnh']; ?>" class="news-thumb" onerror="this.src='https://via.placeholder.com/60x40'"></td>
                            <td><strong><?php echo htmlspecialchars($n['TieuDe']); ?></strong></td>
                            <td><?php echo date('d/m/Y', strtotime($n['NgayDang'])); ?></td>
                            <td>
                                <a href="admin_news_edit.php?id=<?php echo $n['MaTinTuc']; ?>" class="btn btn-outline">SỬA</a>
                                <a href="admin_action.php?action=delete_news&id=<?php echo $n['MaTinTuc']; ?>" class="btn btn-danger" onclick="return confirm('Xóa bài viết này?')">XÓA</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="section-card" id="orders">
            <div class="section-title">
                <h3>ĐƠN HÀNG</h3>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Mã ĐH</th>
                        <th>Khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                        <?php
                        $status = trim($o['TrangThaiDonHang']);
                        $status_class = '';
                        if ($status === 'Chờ thanh toán') $status_class = 'status-pay';
                        elseif ($status === 'Chờ xác nhận') $status_class = 'status-wait';
                        else $status_class = 'status-done';
                        ?>
                        <tr>
                            <td>#<?php echo $o['MaDonHang']; ?></td>
                            <td><?php echo htmlspecialchars($o['HoTen']); ?></td>
                            <td><strong><?php echo number_format($o['TongTien'], 0, ',', '.'); ?>đ</strong></td>
                            <td>
                                <span class="<?php echo $status_class; ?>">
                                    <?php echo $status; ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <?php if ($status === 'Chờ xác nhận'): ?>
                                        <a href="admin_action.php?action=confirm_order&id=<?php echo $o['MaDonHang']; ?>" class="btn btn-black">XÁC NHẬN</a>
                                    <?php endif; ?>

                                    <?php if ($status !== 'Đã xác nhận'): ?>
                                        <a href="admin_action.php?action=delete_order&id=<?php echo $o['MaDonHang']; ?>" class="btn btn-danger" onclick="return confirm('Hủy đơn hàng này?')">HỦY</a>
                                    <?php endif; ?>

                                    <a href="order_detail.php?id=<?php echo $o['MaDonHang']; ?>" class="btn btn-outline">CHI TIẾT</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>