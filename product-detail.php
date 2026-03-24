<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/db.php';

// 1. Lấy ID sản phẩm từ URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. Truy vấn chi tiết sản phẩm
$stmt = $pdo->prepare("SELECT * FROM SanPham WHERE MaSanPham = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

// Nếu không tìm thấy sản phẩm, quay lại trang danh sách
if (!$product) {
    header("Location: products.php");
    exit();
}

include __DIR__ . '/includes/header.php';
?>

<style>
    .detail-container {
        display: flex;
        gap: 80px;
        padding: 80px 10%;
        align-items: flex-start;
    }

    .detail-image {
        flex: 1;
        background: #f9f9f9;
        padding: 40px;
    }

    .detail-image img {
        width: 100%;
        height: auto;
        object-fit: contain;
    }

    .detail-info {
        flex: 1;
    }

    .status-badge {
        display: inline-block;
        font-size: 10px;
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-bottom: 20px;
        color: #888;
    }

    .product-name {
        font-size: 32px;
        font-weight: 400;
        letter-spacing: 2px;
        margin-bottom: 20px;
    }

    .product-price {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 30px;
        border-bottom: 1px solid #eee;
        padding-bottom: 20px;
    }

    .product-desc {
        font-size: 15px;
        line-height: 1.8;
        color: #555;
        margin-bottom: 40px;
    }

    .inventory-info {
        font-size: 13px;
        margin-bottom: 30px;
        color: #888;
    }

    .inventory-info span {
        color: #000;
        font-weight: bold;
    }

    .btn-add-large {
        display: block;
        width: 100%;
        background: #000;
        color: #fff;
        text-align: center;
        padding: 20px;
        text-decoration: none;
        font-weight: bold;
        letter-spacing: 3px;
        transition: 0.3s;
    }

    .btn-add-large:hover {
        background: #333;
    }
</style>

<div class="container">
    <div class="detail-container">
        <div class="detail-image">
            <img src="assets/images/products/<?php echo $product['HinhAnh']; ?>"
                alt="<?php echo htmlspecialchars($product['TenSanPham']); ?>"
                onerror="this.src='https://via.placeholder.com/600x600?text=SOLPIX+STORE'">
        </div>

        <div class="detail-info">
            <div class="status-badge">
                <?php echo ($product['MaDanhMuc'] == 6) ? 'Pre-owned / Vintage' : 'Brand New / Original'; ?>
            </div>

            <h1 class="product-name"><?php echo htmlspecialchars($product['TenSanPham']); ?></h1>

            <div class="product-price">
                <?php echo number_format($product['Gia'], 0, ',', '.'); ?>đ
            </div>

            <div class="product-desc">
                <strong>MÔ TẢ:</strong><br>
                <?php echo nl2br(htmlspecialchars($product['MoTa'])); ?>
            </div>

            <div class="inventory-info">
                TRẠNG THÁI: <span><?php echo ($product['SoLuongTon'] > 0) ? 'Còn hàng' : 'Hết hàng'; ?></span><br>
                TÌNH TRẠNG MÁY: <span><?php echo $product['TrangThai']; ?> sản phẩm</span><br>
                SỐ LƯỢNG TRONG KHO: <span><?php echo $product['SoLuongTon']; ?> sản phẩm</span>
            </div>

            <?php if ($product['SoLuongTon'] > 0): ?>
                <a href="cart.php?action=add&id=<?php echo $product['MaSanPham']; ?>" class="btn-add-large">
                    + THÊM VÀO GIỎ HÀNG
                </a><br>
                <a href="cart.php?action=add&id=<?php echo $product['MaSanPham']; ?>" style="border: 1px solid #000; padding: 10px 30px; font-size:12px; display: flex; justify-content: center; align-items: center;">MUA NGAY</a>
            <?php else: ?>
                <button disabled style="width: 100%; padding: 20px; background: #070202; border: none; color: #fff; cursor: not-allowed;">
                    OUT OF STOCK
                </button>
            <?php endif; ?>

            <a href="products.php" style="display: block; text-align: center; margin-top: 30px; font-size: 12px; color: #534e4e; text-decoration: none;">
                ← QUAY LẠI DANH SÁCH
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>