<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'db.php';
include 'includes/header.php';

// Lấy 8 sản phẩm mới nhất
$stmt = $pdo->query("SELECT * FROM SanPham ORDER BY MaSanPham DESC LIMIT 8");
$products = $stmt->fetchAll();
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<style>
    /* 1. Style cho Banner Slider & Fix lỗi chồng ảnh */
    .main-slider {
        width: 100%;
        height: 550px;
        background: #000;
        overflow: hidden;
    }

    .swiper {
        width: 100%;
        height: 100%;
    }

    .swiper-slide {
        position: relative;
        overflow: hidden;
        opacity: 0 !important;
        /* Mặc định ẩn các slide không active */
        transition-property: opacity;
    }

    .swiper-slide-active {
        opacity: 1 !important;
        /* Chỉ hiện slide đang active */
    }

    .swiper-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.7;
        transition: transform 5s ease;
    }

    .swiper-slide-active img {
        transform: scale(1.1);
    }

    .slider-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        color: #fff;
        z-index: 10;
        width: 90%;
    }

    .slider-content h2 {
        font-size: 45px;
        letter-spacing: 15px;
        text-transform: uppercase;
        font-weight: 300;
        margin-bottom: 20px;
    }

    .slider-content p {
        font-size: 14px;
        letter-spacing: 5px;
        text-transform: uppercase;
        opacity: 0.8;
    }

    .swiper-button-next,
    .swiper-button-prev {
        color: #fff !important;
    }

    .swiper-pagination-bullet {
        background: #fff !important;
    }

    /* 2. Style cho phần giới thiệu (Hero Section) */
    .hero-section {
        padding: 100px 10% 80px;
        text-align: center;
        background: #fff;
    }

    .hero-title {
        font-size: 50px;
        font-weight: 300;
        letter-spacing: 18px;
        text-transform: uppercase;
        margin-bottom: 30px;
        color: #000;
    }

    .hero-subtitle {
        font-size: 15px;
        letter-spacing: 1px;
        color: #555;
        max-width: 850px;
        margin: 0 auto 45px;
        line-height: 2.2;
        text-align: justify;
        text-align-last: center;
    }

    .hero-divider {
        width: 60px;
        height: 1px;
        background: #000;
        margin: 0 auto;
    }

    /* 3. Grid sản phẩm & Card có viền chuyên nghiệp */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 30px;
        padding-bottom: 80px;
    }

    .product-card {
        text-align: center;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        position: relative;
        background: #fff;
        border: 1px solid #eee;
        padding: 25px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        /* Đẩy các nút xuống dưới cùng */
        height: 100%;
        /* Đảm bảo các card cao bằng nhau */
    }

    .product-card:hover {
        border-color: #000;
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        z-index: 2;
    }

    .product-image-wrapper {
        overflow: hidden;
        background: #f9f9f9;
        height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
    }

    .product-card img {
        max-width: 100%;
        max-height: 100%;
        transition: 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .product-card:hover img {
        transform: scale(1.08);
    }

    .badge {
        position: absolute;
        top: 15px;
        left: 15px;
        padding: 6px 12px;
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
        border: 1px solid #fff;
    }

    .out-of-stock img {
        filter: grayscale(0.8);
        opacity: 0.6;
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

    .soldout-text {
        color: #ff4d4d;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-size: 11px;
        margin: 20px 0;
    }

    .btn-action {
        font-size: 10px;
        letter-spacing: 2px;
        font-weight: 700;
        text-decoration: none;
        color: #000;
        text-transform: uppercase;
        transition: 0.3s;
    }

    .btn-primary {
        border: 1px solid #000;
        padding: 15px 45px;
        background: #000;
        color: #fff !important;
        margin-top: 5px;
    }

    .btn-primary:hover {
        background: transparent;
        color: #000 !important;
    }
</style>

<section class="main-slider">
    <div class="swiper mySwiper">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <img src="https://images.unsplash.com/photo-1516035069371-29a1b244cc32?q=80&w=1920" alt="Banner 1">
                <div class="slider-content">
                    <h2>Solpix Store</h2>
                    <p>Khám phá thế giới qua lăng kính chuyên nghiệp</p>
                </div>
            </div>
            <div class="swiper-slide">
                <img src="https://images.unsplash.com/photo-1452784444945-3f422708fe5e?q=80&w=1920" alt="Banner 2">
                <div class="slider-content">
                    <h2>Quality Gear</h2>
                    <p>Thiết bị nhiếp ảnh hàng đầu thế giới</p>
                </div>
            </div>
            <div class="swiper-slide">
                <img src="https://images.unsplash.com/photo-1502920917128-1aa500764cbd?q=80&w=1920" alt="Banner 3">
                <div class="slider-content">
                    <h2>Capture Everything</h2>
                    <p>Lưu giữ trọn vẹn từng khoảnh khắc cảm xúc</p>
                </div>
            </div>
            <div class="swiper-slide">
                <img src="https://images.unsplash.com/photo-1495707902641-75cac588d2e9?q=80&w=1920" alt="Banner 4">
                <div class="slider-content">
                    <h2>Professional Gear</h2>
                    <p>Nâng tầm nghệ thuật nhiếp ảnh của bạn</p>
                </div>
            </div>
            <div class="swiper-slide">
                <img src="https://images.unsplash.com/photo-1510127034890-ba27508e9f1c?q=80&w=1920" alt="Banner 5">
                <div class="slider-content">
                    <h2>Artistic Vision</h2>
                    <p>Khởi nguồn đam mê sáng tạo vô tận</p>
                </div>
            </div>
        </div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-pagination"></div>
    </div>
</section>

<main class="container">
    <section class="hero-section">
        <h1 class="hero-title">Solpix Store</h1>
        <p class="hero-subtitle">
            Chào mừng bạn đến với <strong>Solpix Store</strong> – nơi mỗi tấm ảnh là một câu chuyện, và mỗi khoảnh khắc đều xứng đáng được lưu giữ trọn vẹn nhất.
            Chúng tôi tự hào là hệ thống cung cấp máy ảnh và thiết bị nhiếp ảnh hàng đầu, hội tụ những sản phẩm đỉnh cao từ Mirrorless hiện đại đến DSLR bền bỉ.
            Tại Solpix, chúng tôi không chỉ mang đến giải pháp công nghệ chính hãng, mà còn đồng hành cùng bạn bằng
            <strong>hệ thống mua sắm thông minh</strong>: từ tư vấn chuyên sâu, thanh toán QR tiện lợi đến chính sách bảo hành uy tín.
            Hãy để Solpix cùng bạn kiến tạo nên những tác phẩm nghệ thuật vượt thời gian.
        </p>
        <div class="hero-divider"></div>
    </section>

    <div style="text-align: center; margin: 40px 0 60px;">
        <h2 style="letter-spacing: 8px; text-transform: uppercase; font-size: 16px; font-weight: 700; color: #000;">
            <span style="border-bottom: 2px solid #000; padding-bottom: 10px;">New Arrivals</span>
        </h2>
    </div>

    <div class="product-grid">
        <?php foreach ($products as $sp): ?>
            <?php
            $is_out_of_stock = ((int)$sp['SoLuongTon'] <= 0);
            $discount = isset($sp['PhanTramGiam']) ? (int)$sp['PhanTramGiam'] : 0;
            $final_price = ($discount > 0) ? $sp['Gia'] * (1 - ($discount / 100)) : $sp['Gia'];
            ?>
            <div class="product-card <?php echo $is_out_of_stock ? 'out-of-stock' : ''; ?>">
                <div>
                    <?php if ($is_out_of_stock): ?>
                        <div class="badge soldout-badge" style="background: red;">SOLD OUT</div>
                    <?php elseif ($discount > 0): ?>
                        <div class="badge sale-badge">-<?php echo $discount; ?>%</div>
                    <?php endif; ?>

                    <a href="product-detail.php?id=<?php echo $sp['MaSanPham']; ?>">
                        <div class="product-image-wrapper">
                            <img src="assets/images/products/<?php echo $sp['HinhAnh']; ?>"
                                alt="<?php echo htmlspecialchars($sp['TenSanPham']); ?>"
                                onerror="this.src='https://via.placeholder.com/500x500?text=SOLPIX+STORE'">
                        </div>
                    </a>

                    <h3 style="font-size: 13px; margin-top: 25px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">
                        <?php echo htmlspecialchars($sp['TenSanPham']); ?>
                    </h3>

                    <p class="price" style="font-size: 14px; margin: 10px 0 20px;">
                        <?php if ($discount > 0): ?>
                            <span class="price-old"><?php echo number_format($sp['Gia'], 0, ',', '.'); ?>đ</span>
                            <span class="price-new"><?php echo number_format($final_price, 0, ',', '.'); ?>đ</span>
                        <?php else: ?>
                            <span style="font-weight: 700;"><?php echo number_format($sp['Gia'], 0, ',', '.'); ?>đ</span>
                        <?php endif; ?>
                    </p>
                </div>

                <div style="margin-top: auto;">
                    <?php if (!$is_out_of_stock): ?>
                        <div style="display: flex; flex-direction: column; gap: 15px; align-items: center;">
                            <a href="cart.php?action=add&id=<?php echo $sp['MaSanPham']; ?>" class="btn-action" style="text-decoration: underline;">
                                + Thêm vào giỏ hàng
                            </a>
                            <a href="cart.php?action=add&id=<?php echo $sp['MaSanPham']; ?>" class="btn-action btn-primary">
                                Mua ngay
                            </a>
                        </div>
                    <?php else: ?>
                        <p class="soldout-text">Hết hàng</p>
                        <a href="product-detail.php?id=<?php echo $sp['MaSanPham']; ?>"
                            style="font-size: 10px; letter-spacing: 2px; color: #999; text-decoration: none; text-transform: uppercase; font-weight: 700;">
                            Xem chi tiết
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    var swiper = new Swiper(".mySwiper", {
        loop: true,
        effect: "fade",
        fadeEffect: {
            crossFade: true // Fix lỗi chồng ảnh tuyệt đối
        },
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        speed: 1000 // Tốc độ mờ dần 1s
    });
</script>