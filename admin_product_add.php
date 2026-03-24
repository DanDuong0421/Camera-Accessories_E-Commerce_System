<?php
session_start();
require_once __DIR__ . '/config/db.php';

// 1. Bảo mật
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

$success = false;
$error_message = '';

// 2. Lấy danh sách danh mục
$categories = $pdo->query("SELECT * FROM DanhMuc ORDER BY TenDanhMuc ASC")->fetchAll();

// 3. Xử lý Form khi Admin nhấn "Xác nhận"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten = $_POST['ten_san_pham'];
    $gia = $_POST['gia'];
    $ton_kho = $_POST['so_luong_ton'];
    $ma_dm = $_POST['ma_danh_muc'];
    $mo_ta = $_POST['mo_ta'];
    // Thêm biến phần trăm giảm giá
    $phan_tram_giam = isset($_POST['phan_tram_giam']) ? (int)$_POST['phan_tram_giam'] : 0;

    // Xử lý Upload Ảnh
    $hinh_anh = "";
    if (!empty($_FILES['hinh_anh']['name'])) {
        $hinh_anh = time() . "_" . $_FILES['hinh_anh']['name'];
        $target = "assets/images/products/" . $hinh_anh;

        if (!is_dir('assets/images/products/')) {
            mkdir('assets/images/products/', 0777, true);
        }
        move_uploaded_file($_FILES['hinh_anh']['tmp_name'], $target);
    }

    try {
        // Cập nhật câu SQL: Thêm cột PhanTramGiam
        $sql = "INSERT INTO SanPham (TenSanPham, Gia, SoLuongTon, MaDanhMuc, MoTa, HinhAnh, PhanTramGiam) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$ten, $gia, $ton_kho, $ma_dm, $mo_ta, $hinh_anh, $phan_tram_giam]);

        $success = true;
    } catch (PDOException $e) {
        $error_message = "Lỗi: " . $e->getMessage();
    }
}

?>

<?php if ($success): ?>
    <div id="success-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.96); z-index: 9999; display: flex; justify-content: center; align-items: center; text-align: center;">
        <div>
            <div style="width: 100px; height: 100px; border: 3px solid #000; border-radius: 50%; display: flex; justify-content: center; align-items: center; margin: 0 auto 30px;">
                <span style="font-size: 50px;">✓</span>
            </div>
            <h2 style="letter-spacing: 10px; text-transform: uppercase;">Thêm sản phẩm thành công</h2>
            <p style="color: #888; font-size: 13px; margin-top: 10px;">Máy ảnh mới đã được đưa lên kệ hàng...</p>
        </div>
    </div>
    <script>
        setTimeout(function() {
            window.location.href = 'admin.php#products';
        }, 2000);
    </script>
<?php endif; ?>

<div class="container" style="padding: 60px 0;">
    <div style="max-width: 800px; margin: 0 auto; border: 1px solid #000; padding: 50px; background: #fff;">
        <h2 style="text-align: center; letter-spacing: 5px; text-transform: uppercase; margin-bottom: 40px;">Thêm máy ảnh mới</h2>

        <?php if ($error_message): ?>
            <p style="color: red; text-align: center; margin-bottom: 20px;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: bold; font-size: 11px; letter-spacing: 1px; margin-bottom: 8px;">TÊN SẢN PHẨM</label>
                    <input type="text" name="ten_san_pham" required style="width: 100%; padding: 12px; border: 1px solid #ccc;">
                </div>
                <div>
                    <label style="display: block; font-weight: bold; font-size: 11px; letter-spacing: 1px; margin-bottom: 8px;">DANH MỤC</label>
                    <select name="ma_danh_muc" required style="width: 100%; padding: 12px; border: 1px solid #ccc;">
                        <option value="">Chọn loại máy ảnh</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['MaDanhMuc']; ?>"><?php echo $cat['TenDanhMuc']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: bold; font-size: 11px; letter-spacing: 1px; margin-bottom: 8px;">GIÁ BÁN (VNĐ)</label>
                    <input type="number" name="gia" required style="width: 100%; padding: 12px; border: 1px solid #ccc;">
                </div>
                <div>
                    <label style="display: block; font-weight: bold; font-size: 11px; letter-spacing: 1px; margin-bottom: 8px; color: #ff4d4d;">GIẢM GIÁ (%)</label>
                    <input type="number" name="phan_tram_giam" value="0" min="0" max="100" style="width: 100%; padding: 12px; border: 1px solid #ff4d4d; color: #ff4d4d;">
                </div>
                <div>
                    <label style="display: block; font-weight: bold; font-size: 11px; letter-spacing: 1px; margin-bottom: 8px;">SỐ LƯỢNG TỒN</label>
                    <input type="number" name="so_luong_ton" required style="width: 100%; padding: 12px; border: 1px solid #ccc;">
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: bold; font-size: 11px; letter-spacing: 1px; margin-bottom: 8px;">MÔ TẢ CHI TIẾT</label>
                <textarea name="mo_ta" rows="6" style="width: 100%; padding: 12px; border: 1px solid #ccc; font-family: sans-serif;"></textarea>
            </div>

            <div style="margin-bottom: 40px;">
                <label style="display: block; font-weight: bold; font-size: 11px; letter-spacing: 1px; margin-bottom: 8px;">HÌNH ẢNH SẢN PHẨM</label>
                <input type="file" name="hinh_anh" accept="image/*" required>
            </div>

            <button type="submit" style="width: 100%; background: #000; color: #fff; padding: 20px; border: none; font-weight: bold; letter-spacing: 3px; cursor: pointer; text-transform: uppercase;">
                XÁC NHẬN THÊM SẢN PHẨM
            </button>

            <a href="admin.php" style="display: block; text-align: center; margin-top: 25px; color: #999; text-decoration: none; font-size: 11px;">← HỦY BỎ</a>
        </form>
    </div>
</div>