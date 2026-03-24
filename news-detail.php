<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/db.php';

// 1. Lấy ID từ URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. Truy vấn tin tức hiện tại
$stmt = $pdo->prepare("SELECT * FROM TinTuc WHERE MaTinTuc = ?");
$stmt->execute([$id]);
$news = $stmt->fetch();

if (!$news) {
    header("Location: news.php");
    exit();
}

// 3. Truy vấn các bài đăng khác (Gợi ý 3 bài mới nhất, trừ bài đang xem)
$stmt_related = $pdo->prepare("SELECT * FROM TinTuc WHERE MaTinTuc != ? ORDER BY NgayDang DESC LIMIT 3");
$stmt_related->execute([$id]);
$related_news = $stmt_related->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="news-detail-container" style="max-width: 800px; margin: 0 auto; padding: 80px 20px;">
    <header style="text-align: center; margin-bottom: 50px;">
        <span style="font-size: 12px; color: #888; letter-spacing: 2px; text-transform: uppercase;">
            Ngày đăng: <?php echo date('d/m/Y', strtotime($news['NgayDang'])); ?>
        </span>
        <h1 style="font-size: 36px; margin-top: 20px; line-height: 1.2; letter-spacing: 1px;">
            <?php echo htmlspecialchars($news['TieuDe']); ?>
        </h1>
    </header>

    <img src="assets/images/news/<?php echo $news['HinhAnh']; ?>"
        style="width: 100%; height: auto; margin-bottom: 50px;"
        onerror="this.src='https://via.placeholder.com/1200x800?text=Solpix+News'">

    <div class="news-content" style="font-size: 17px; line-height: 2; color: #333; text-align: justify; margin-bottom: 100px;">
        <?php echo nl2br(htmlspecialchars($news['NoiDung'])); ?>
    </div>

    <section class="related-news" style="border-top: 1px solid #eee; padding-top: 60px;">
        <h3 style="text-align: center; letter-spacing: 5px; text-transform: uppercase; margin-bottom: 40px; font-size: 18px;">Bài viết liên quan</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px;">
            <?php foreach ($related_news as $r_news): ?>
                <a href="news-detail.php?id=<?php echo $r_news['MaTinTuc']; ?>" style="text-decoration: none; color: inherit; display: block;">
                    <img src="assets/images/news/<?php echo $r_news['HinhAnh']; ?>"
                        style="width: 100%; height: 150px; object-fit: cover; margin-bottom: 15px;"
                        onerror="this.src='https://via.placeholder.com/400x250?text=Solpix+News'">
                    <h4 style="font-size: 14px; line-height: 1.4;"><?php echo htmlspecialchars($r_news['TieuDe']); ?></h4>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>