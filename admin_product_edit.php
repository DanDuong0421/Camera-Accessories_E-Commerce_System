<?php
session_start();
// Bảo mật: Chỉ Admin mới được vào
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/config/db.php';

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM SanPham WHERE MaSanPham = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();

if (!$p) {
    echo "<script>alert('Sản phẩm không tồn tại!'); window.location='admin.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten = $_POST['ten'];
    $gia = $_POST['gia'];
    $ton = $_POST['ton'];
    $mota = $_POST['mota'];
    // Lấy thêm giá trị phần trăm giảm giá từ form
    $phan_tram_giam = (int)$_POST['phan_tram_giam'];

    // Nếu có upload ảnh mới thì dùng ảnh mới, không thì giữ ảnh cũ
    $hinh = !empty($_FILES['hinh']['name']) ? $_FILES['hinh']['name'] : $p['HinhAnh'];
    if (!empty($_FILES['hinh']['name'])) {
        move_uploaded_file($_FILES['hinh']['tmp_name'], "assets/images/products/" . $hinh);
    }

    // Cập nhật câu lệnh SQL: thêm cột PhanTramGiam
    $sql = "UPDATE SanPham SET TenSanPham=?, Gia=?, SoLuongTon=?, MoTa=?, HinhAnh=?, PhanTramGiam=? WHERE MaSanPham=?";
    $pdo->prepare($sql)->execute([$ten, $gia, $ton, $mota, $hinh, $phan_tram_giam, $id]);

    echo "<script>alert('Cập nhật thành công!'); window.location='admin.php';</script>";
}
?>

<div class="container" style="padding: 50px 0; min-height: 80vh;">
    <h2 style="text-align:center; letter-spacing:5px; font-weight: 300; text-transform: uppercase; margin-bottom: 30px;">Chỉnh sửa sản phẩm</h2>

    <form method="POST" enctype="multipart/form-data" style="max-width: 600px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px;">

        <div>
            <label style="font-size: 12px; font-weight: bold; text-transform: uppercase;">Tên máy ảnh</label>
            <input type="text" name="ten" value="<?php echo htmlspecialchars($p['TenSanPham']); ?>" required style="width: 100%; padding: 12px; margin-top: 5px; border: 1px solid #ddd;">
        </div>

        <div style="display: flex; gap: 20px;">
            <div style="flex: 1;">
                <label style="font-size: 12px; font-weight: bold; text-transform: uppercase;">Giá gốc (VNĐ)</label>
                <input type="number" name="gia" value="<?php echo $p['Gia']; ?>" required style="width: 100%; padding: 12px; margin-top: 5px; border: 1px solid #ddd;">
            </div>
            <div style="flex: 1;">
                <label style="font-size: 12px; font-weight: bold; text-transform: uppercase; color: #ff4d4d;">Khuyến mãi (%)</label>
                <input type="number" name="phan_tram_giam" value="<?php echo $p['PhanTramGiam'] ?? 0; ?>" min="0" max="100" style="width: 100%; padding: 12px; margin-top: 5px; border: 1px solid #ff4d4d; color: #ff4d4d;">
            </div>
        </div>

        <div>
            <label style="font-size: 12px; font-weight: bold; text-transform: uppercase;">Số lượng tồn kho</label>
            <input type="number" name="ton" value="<?php echo $p['SoLuongTon']; ?>" style="width: 100%; padding: 12px; margin-top: 5px; border: 1px solid #ddd;">
        </div>

        <div>
            <label style="font-size: 12px; font-weight: bold; text-transform: uppercase;">Mô tả sản phẩm</label>
            <textarea name="mota" rows="5" style="width: 100%; padding: 12px; margin-top: 5px; border: 1px solid #ddd;"><?php echo htmlspecialchars($p['MoTa']); ?></textarea>
        </div>

        <div>
            <label style="font-size: 12px; font-weight: bold; text-transform: uppercase;">Hình ảnh sản phẩm</label>
            <div style="margin: 10px 0;">
                <img src="assets/images/products/<?php echo $p['HinhAnh']; ?>" width="100" style="border: 1px solid #eee;">
                <p style="font-size: 11px; color: #888; margin-top: 5px;">File hiện tại: <?php echo $p['HinhAnh']; ?></p>
            </div>
            <input type="file" name="hinh" style="font-size: 12px;">
        </div>

        <button type="submit" style="background: #000; color: #fff; padding: 18px; border: none; font-weight: bold; letter-spacing: 2px; cursor: pointer; text-transform: uppercase; transition: 0.3s;">
            Lưu thay đổi
        </button>

        <a href="admin.php" style="text-align: center; color: #888; text-decoration: none; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">Hủy bỏ & Quay lại</a>
    </form>
</div>