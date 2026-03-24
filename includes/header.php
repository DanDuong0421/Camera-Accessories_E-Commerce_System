<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solpix Store</title>
    <link rel="icon" href="/SolpixStore/assets/images/logo/logo.ico" type="image/x-icon">

    <style>
    /* Dán bộ CSS chuyên nghiệp của bạn vào đây */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Montserrat', sans-serif;
        background-color: #fff;
        color: #000;
        line-height: 1.6;
    }

    a {
        text-decoration: none;
        color: inherit;
        transition: 0.3s;
    }

    /* Navbar */
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 25px 10%;
        border-bottom: 1px solid #eee;
        position: sticky;
        top: 0;
        background: #ffffff;
        z-index: 1000;
    }

    .logo {
        font-size: 26px;
        font-weight: 700;
        letter-spacing: 4px;
    }

    .nav-links {
        display: flex;
        list-style: none;
        gap: 30px;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .nav-links a:hover {
        opacity: 0.5;
    }

    .btn-login {
        border: 1px solid #000;
        padding: 8px 20px;
    }

    .btn-login:hover {
        background: #000;
        color: #fff;
    }

    /* Product Grid */
    .container {
        padding: 60px 10%;
    }

    .logo img {
        height: 85px;
        width: auto;
        display: block;
    }

    .logo a {
        display: flex;
        align-items: center;
        gap: 10px;
    }


    .logo span {
        font-size: 22px;
        font-weight: 700;
        letter-spacing: 3px;
        white-space: nowrap;
        /* không xuống dòng */
    }


    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 40px;
    }

    .product-card {
        text-align: center;
        padding-bottom: 20px;
        transition: 0.4s;
    }

    .product-card:hover {
        transform: translateY(-10px);
    }

    .product-card img {
        width: 100%;
        height: 300px;
        object-fit: contain;
        background: #fafafa;
    }

    .price {
        font-weight: 700;
        margin: 15px 0;
        font-size: 18px;
    }

    /* Footer */
    .footer {
        text-align: center;
        padding: 50px;
        border-top: 1px solid #eee;
        font-size: 12px;
        color: #888;
        letter-spacing: 1px;
    }

    /* Đảm bảo danh sách không bị hiện bullet point (chấm tròn) */
    ul {
        list-style: none;
    }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <a href="index.php">
                <img src="/SolpixStore/assets/images/logo/logo.ico" alt="Solpix Store">
                <span>Solpix Store</span>
            </a>
        </div>

        <ul class="nav-links">
            <li><a href="index.php">Trang chủ</a></li>
            <li><a href="products.php">Sản phẩm</a></li>
            <li><a href="news.php">Tin tức</a></li>
            <li><a href="cart.php">Giỏ hàng</a></li>

            <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="order_history.php">Lịch sử đơn</a></li>
            <li><a href="personalize.php">Tôi</a></li>

            <li><a href="logout.php">Đăng xuất (<?php echo htmlspecialchars($_SESSION['ho_ten']); ?>)</a></li>
            <?php else: ?>
            <li><a href="login.php" class="btn-login">Đăng nhập</a></li>
            <?php endif; ?>
        </ul>
    </nav>