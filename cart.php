<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/db.php';

// --- PHẦN XỬ LÝ LOGIC (BACKEND) ---

// 1. Thêm sản phẩm (Dành cho nút MUA NGAY và THÊM VÀO GIỎ)
if (isset($_GET['action']) && $_GET['action'] == 'add') {
    $id = $_GET['id'];
    // Lấy tồn kho để kiểm tra trước khi thêm
    $stmt_check = $pdo->prepare("SELECT SoLuongTon FROM SanPham WHERE MaSanPham = ?");
    $stmt_check->execute([$id]);
    $p_check = $stmt_check->fetch();

    if ($p_check) {
        $current_in_cart = isset($_SESSION['cart'][$id]) ? $_SESSION['cart'][$id] : 0;
        if ($current_in_cart < $p_check['SoLuongTon']) {
            $_SESSION['cart'][$id] = $current_in_cart + 1;
        } else {
            $_SESSION['error_cart'] = "Sản phẩm này đã đạt giới hạn tồn kho!";
        }
    }
    header("Location: cart.php");
    exit();
}

// 2. Xóa từng sản phẩm
if (isset($_GET['action']) && $_GET['action'] == 'remove') {
    unset($_SESSION['cart'][$_GET['id']]);
    header("Location: cart.php");
    exit();
}

// 3. Tăng/Giảm số lượng (Cập nhật trực tiếp trong giỏ)
if (isset($_GET['action']) && $_GET['action'] == 'update') {
    $id = $_GET['id'];
    $type = $_GET['type'];

    if (isset($_SESSION['cart'][$id])) {
        if ($type == 'plus') {
            $stmt_check = $pdo->prepare("SELECT SoLuongTon FROM SanPham WHERE MaSanPham = ?");
            $stmt_check->execute([$id]);
            $product_stock = $stmt_check->fetch();

            if ($product_stock && $_SESSION['cart'][$id] < $product_stock['SoLuongTon']) {
                $_SESSION['cart'][$id]++;
            } else {
                $_SESSION['error_cart'] = "Chỉ còn " . $product_stock['SoLuongTon'] . " sản phẩm trong kho.";
            }
        } elseif ($type == 'minus') {
            if ($_SESSION['cart'][$id] > 1) {
                $_SESSION['cart'][$id]--;
            }
        }
    }
    header("Location: cart.php");
    exit();
}

include __DIR__ . '/includes/header.php';
?>

<style>
    .qty-btn {
        border: 1px solid #000;
        background: transparent;
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        text-decoration: none;
        color: #000;
        font-size: 14px;
        transition: 0.3s;
    }

    .qty-btn:hover {
        background: #000;
        color: #fff;
    }

    .cart-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        background: #f9f9f9;
        border: 1px solid #eee;
    }

    .error-msg {
        background: #f8d7da;
        color: #721c24;
        padding: 10px;
        margin-bottom: 20px;
        text-align: center;
        font-size: 13px;
        border-radius: 2px;
    }
</style>

<div class="container" style="min-height: 60vh; padding-top: 50px;">
    <h2 style="text-align:center; letter-spacing:8px; margin-bottom:50px; text-transform: uppercase; font-weight: 300;">Giỏ hàng</h2>

    <?php if (isset($_SESSION['error_cart'])): ?>
        <div class="error-msg">
            <?php echo $_SESSION['error_cart'];
            unset($_SESSION['error_cart']); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($_SESSION['cart'])): ?>
        <div style="text-align:center; padding: 100px 0;">
            <p style="color: #999;">GIỎ HÀNG CỦA BẠN ĐANG TRỐNG</p>
            <br>
            <a href="products.php" style="text-decoration:underline; color:#000; font-size: 12px; font-weight: bold;">QUAY LẠI CỬA HÀNG</a>
        </div>
    <?php else: ?>
        <table style="width:100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #000; text-align: left; font-size: 11px; letter-spacing: 2px; color: #888;">
                    <th style="padding: 15px 0;">SẢN PHẨM</th>
                    <th style="text-align: center;">SỐ LƯỢNG</th>
                    <th>GIÁ</th>
                    <th style="text-align: right;">THÀNH TIỀN</th>
                    <th style="text-align: right; padding-right: 10px;">THAO TÁC</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($_SESSION['cart'] as $id => $quantity):
                    $stmt = $pdo->prepare("SELECT * FROM SanPham WHERE MaSanPham = ?");
                    $stmt->execute([$id]);
                    $product = $stmt->fetch();

                    $discount = isset($product['PhanTramGiam']) ? (int)$product['PhanTramGiam'] : 0;
                    $current_price = ($discount > 0) ? $product['Gia'] * (1 - ($discount / 100)) : $product['Gia'];
                    $subtotal = $current_price * $quantity;
                    $total += $subtotal;
                ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 20px 0; display: flex; align-items: center; gap: 20px;">
                            <img src="assets/images/products/<?php echo $product['HinhAnh']; ?>" class="cart-img" onerror="this.src='https://via.placeholder.com/80'">
                            <div>
                                <span style="font-weight: bold; display: block; font-size: 14px;"><?php echo htmlspecialchars($product['TenSanPham']); ?></span>
                                <?php if ($discount > 0): ?>
                                    <small style="color: #ff4d4d; font-weight: bold;">Sale -<?php echo $discount; ?>%</small>
                                <?php endif; ?>
                            </div>
                        </td>

                        <td style="text-align: center;">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                                <a href="cart.php?action=update&type=minus&id=<?php echo $id; ?>"
                                    class="qty-btn"
                                    style="<?php echo $quantity <= 1 ? 'opacity: 0.3; pointer-events: none;' : ''; ?>">-</a>

                                <span style="font-weight: bold; min-width: 15px;"><?php echo $quantity; ?></span>

                                <a href="cart.php?action=update&type=plus&id=<?php echo $id; ?>" class="qty-btn">+</a>
                            </div>
                        </td>

                        <td><span style="font-size: 14px;"><?php echo number_format($current_price, 0, ',', '.'); ?>đ</span></td>
                        <td style="text-align: right; font-weight: bold; font-size: 14px;"><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</td>

                        <td style="text-align: right;">
                            <a href="cart.php?action=remove&id=<?= $id; ?>" style="color:#8B0000; font-size:11px; text-decoration: none; font-weight: bold;" onclick="return confirm('Xóa sản phẩm này?')">[ XÓA ]</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="margin-top: 50px; text-align: right; padding-bottom: 80px;">
            <p style="font-size: 12px; color: #888; letter-spacing: 2px; margin-bottom: 10px;">TỔNG CỘNG</p>
            <h2 style="letter-spacing: 2px; font-size: 26px; font-weight: 300;"><?php echo number_format($total, 0, ',', '.'); ?> VNĐ</h2>
            <br>
            <a href="checkout.php" style="background:#000; color:#fff; padding:18px 60px; text-decoration:none; font-size: 12px; font-weight:bold; letter-spacing:3px; text-transform: uppercase; display: inline-block;">Tiến hành thanh toán</a>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>