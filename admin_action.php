<?php
session_start();
require_once __DIR__ . '/config/db.php';

// 1. Bảo mật: Chỉ Admin mới được thực thi các hành động này
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    die("Quyền truy cập bị từ chối!");
}

$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: admin.php");
    exit();
}

switch ($action) {
    case 'confirm_order': // XÁC NHẬN ĐƠN HÀNG
        // ĐÃ SỬA: Đổi TrangThaiDonHang thành TrangThai cho khớp với các file khác
        $stmt = $pdo->prepare("UPDATE DonHang SET TrangThaiDonHang = 'Đã xác nhận' WHERE MaDonHang = ?");
        $stmt->execute([$id]);

        // Chuyển hướng về trang chi tiết đơn hàng để thấy nút biến mất ngay lập tức
        header("Location: order_detail.php?id=" . $id);
        exit();

    case 'delete_order':
        $id = (int)$_GET['id'];
        try {
            $pdo->beginTransaction();

            // Bước 1: Lấy danh sách sản phẩm trong đơn để CỘNG LẠI vào kho
            $stmtItems = $pdo->prepare("SELECT MaSanPham, SoLuong FROM ChiTietDonHang WHERE MaDonHang = ?");
            $stmtItems->execute([$id]);
            $items = $stmtItems->fetchAll();

            foreach ($items as $item) {
                $sqlRestore = "UPDATE SanPham SET SoLuongTon = SoLuongTon + ? WHERE MaSanPham = ?";
                $pdo->prepare($sqlRestore)->execute([$item['SoLuong'], $item['MaSanPham']]);
            }

            // Bước 2: Xóa chi tiết đơn hàng trước
            $pdo->prepare("DELETE FROM ChiTietDonHang WHERE MaDonHang = ?")->execute([$id]);

            // Bước 3: Xóa đơn hàng chính
            $pdo->prepare("DELETE FROM DonHang WHERE MaDonHang = ?")->execute([$id]);

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            die("Lỗi xử lý hủy đơn: " . $e->getMessage());
        }
        header("Location: admin.php#orders");
        exit();

    case 'delete_product': // XÓA SẢN PHẨM
        $stmt = $pdo->prepare("DELETE FROM SanPham WHERE MaSanPham = ?");
        $stmt->execute([$id]);
        break;

    case 'delete_news': // XÓA TIN TỨC
        $stmt = $pdo->prepare("DELETE FROM TinTuc WHERE MaTinTuc = ?");
        $stmt->execute([$id]);
        break;

    case 'delete_category': // XÓA DANH MỤC
        $stmt = $pdo->prepare("DELETE FROM DanhMuc WHERE MaDanhMuc = ?");
        $stmt->execute([$id]);
        break;

    default:
        header("Location: admin.php");
        exit();
}

// Đối với các hành động xóa, quay về trang quản trị tổng
header("Location: admin.php");
exit();
