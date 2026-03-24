<?php
session_start();
require_once __DIR__ . '/config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Kiểm tra đơn hàng thuộc về user và đang chờ thanh toán
$stmt = $pdo->prepare("SELECT * FROM DonHang WHERE MaDonHang = ? AND MaNguoiDung = ? AND TrangThaiDonHang = 'Chờ thanh toán'");
$stmt->execute([$id, $user_id]);
$order = $stmt->fetch();

if ($order) {
    // Chuyển trạng thái sang Chờ xác nhận (như đơn COD bình thường)
    $sql = "UPDATE DonHang SET TrangThaiDonHang = 'Chờ xác nhận' WHERE MaDonHang = ?";
    $pdo->prepare($sql)->execute([$id]);

    echo "<script>alert('Đã chuyển đổi sang phương thức thanh toán COD!'); window.location.href='index.php';</script>";
} else {
    header("Location: index.php");
}
