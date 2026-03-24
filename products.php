<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/db.php';

$current_cat = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// 1. Lấy danh sách danh mục
$stmt_cats = $pdo->query("SELECT * FROM DanhMuc ORDER BY MaDanhMuc ASC");
$categories = $stmt_cats->fetchAll();

// 2. Xây dựng câu lệnh SQL có Search và Category Filter
$sql = "SELECT * FROM SanPham WHERE 1=1";
$params = [];

if ($current_cat > 0) {
    $sql .= " AND MaDanhMuc = ?";
    $params[] = $current_cat;
}
if ($search_query !== '') {
    $sql .= " AND TenSanPham LIKE ?";
    $params[] = "%$search_query%";
}
$sql .= " ORDER BY MaSanPham DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<style>
    /* Thanh điều hướng danh mục */
    .filter-bar {
        text-align: center;
        margin: 40px 0 60px;
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 25px;
    }

    .filter-link {
        font-size: 11px;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: #999;
        font-weight: 700;
        text-decoration: none;
        transition: 0.3s;
        padding-bottom: 5px;
        border-bottom: 1px solid transparent;
    }

    .filter-link:hover,
    .filter-link.active {
        color: #000;
        border-bottom: 1px solid #000;
    }

    /* Grid sản phẩm */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 30px;
        padding-bottom: 80px;
    }

    /* Card sản phẩm có viền */
    .product-card {
        background: #fff;
        border: 1px solid #eee;
        /* Viền mảnh ban đầu */
        padding: 25px;
        text-align: center;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    /* Hiệu ứng khi di chuột vào card */
    .product-card:hover {
        border-color: #000;
        /* Viền đen */
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        /* Đổ bóng nhẹ */
    }

    .product-image-wrapper {
        overflow: hidden;
        background: #f9f9f9;
        height: 250px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }

    .product-card img {
        max-width: 100%;
        max-height: 100%;
        transition: transform 0.6s;
    }

    .product-card:hover img {
        transform: scale(1.05);
    }

    /* Trạng thái Badges */
    .status-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        padding: 5px 12px;
        font-size: 9px;
        font-weight: bold;
        z-index: 10;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .sale-badge {
        background: #ff4d4d;
        color: #fff;
    }

    .soldout-badge {
        background: #000;
        color: #fff;
    }

    .out-of-stock img {
        filter: grayscale(1);
        opacity: 0.5;
    }

    .soldout-text {
        color: #ff4d4d;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-size: 11px;
        margin: 20px 0;
    }

    .price-old {
        text-decoration: line-through;
        color: #bbb;
        font-size: 13px;
        margin-right: 10px;
    }

    .price-new {
        color: #ff4d4d;
        font-weight: 700;
    }
</style>

<div class="container">
    <div style="text-align: center; margin-top: 40px;">
        <h2 style="letter-spacing: 12px; text-transform: uppercase; font-weight: 300;">
            <?php echo $search_query !== '' ? 'Kết quả cho: "' . htmlspecialchars($search_query) . '"' : 'Collection'; ?>
        </h2>
    </div>

    <nav class="filter-bar">
        <a href="products.php" class="filter-link <?php echo ($current_cat == 0 && $search_query == '') ? 'active' : ''; ?>">Tất cả</a>
        <?php foreach ($categories as $cat): ?>
            <a href="products.php?cat=<?php echo $cat['MaDanhMuc']; ?>"
                class="filter-link <?php echo $current_cat == $cat['MaDanhMuc'] ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($cat['TenDanhMuc']); ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="product-grid">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $item): ?>
                <?php
                $is_out_of_stock = ($item['SoLuongTon'] <= 0);
                $has_discount = isset($item['PhanTramGiam']) && $item['PhanTramGiam'] > 0;
                $final_price = $has_discount ? $item['Gia'] * (1 - ($item['PhanTramGiam'] / 100)) : $item['Gia'];
                ?>
                <div class="product-card <?php echo $is_out_of_stock ? 'out-of-stock' : ''; ?>">

                    <div>
                        <?php if ($is_out_of_stock): ?>
                            <div class="status-badge soldout-badge" style="background: red;">SOLD OUT</div>
                        <?php elseif ($has_discount): ?>
                            <div class="status-badge sale-badge">-<?php echo $item['PhanTramGiam']; ?>%</div>
                        <?php endif; ?>

                        <a href="product-detail.php?id=<?php echo $item['MaSanPham']; ?>">
                            <div class="product-image-wrapper">
                                <img src="assets/images/products/<?php echo $item['HinhAnh']; ?>"
                                    alt="<?php echo htmlspecialchars($item['TenSanPham']); ?>"
                                    onerror="this.src='https://via.placeholder.com/500x500?text=SOLPIX+STORE'">
                            </div>
                        </a>

                        <h3 style="font-size: 13px; margin-top: 15px; font-weight: 600; letter-spacing: 1px; min-height: 40px; text-transform: uppercase;">
                            <?php echo htmlspecialchars($item['TenSanPham']); ?>
                        </h3>

                        <p class="price" style="font-size: 14px; margin-bottom: 20px;">
                            <?php if ($has_discount): ?>
                                <span class="price-old"><?php echo number_format($item['Gia'], 0, ',', '.'); ?>đ</span>
                                <span class="price-new"><?php echo number_format($final_price, 0, ',', '.'); ?>đ</span>
                            <?php else: ?>
                                <span style="font-weight: 700;"><?php echo number_format($item['Gia'], 0, ',', '.'); ?>đ</span>
                            <?php endif; ?>
                        </p>
                    </div>

                    <div style="margin-top: auto;">
                        <?php if (!$is_out_of_stock): ?>
                            <div style="display: flex; flex-direction: column; gap: 12px; align-items: center;">
                                <a href="cart.php?action=add&id=<?php echo $item['MaSanPham']; ?>"
                                    style="font-size: 10px; letter-spacing: 2px; font-weight: 700; text-decoration: underline; color: #000; text-transform: uppercase;">
                                    + Thêm vào giỏ hàng
                                </a>
                                <a href="cart.php?action=add&id=<?php echo $item['MaSanPham']; ?>"
                                    style="border: 1px solid #000; padding: 12px 0; width: 100%; font-size: 11px; text-transform: uppercase; letter-spacing: 2px; font-weight: bold; background: #000; color: #fff; text-decoration: none;">
                                    MUA NGAY
                                </a>
                            </div>
                        <?php else: ?>
                            <p class="soldout-text">Hết hàng</p>
                            <a href="product-detail.php?id=<?php echo $item['MaSanPham']; ?>"
                                style="font-size: 10px; letter-spacing: 2px; color: #999; text-decoration: none; text-transform: uppercase; font-weight: bold; border-bottom: 1px solid #eee;">
                                Xem chi tiết
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 100px 0; color: #888; border: 1px dashed #eee;">
                Không tìm thấy sản phẩm phù hợp trong danh mục này.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>