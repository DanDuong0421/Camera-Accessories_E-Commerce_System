<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/db.php';

// Truy vấn lấy danh sách tin tức
$stmt = $pdo->query("SELECT * FROM TinTuc ORDER BY NgayDang DESC");
$news_list = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<style>
    .news-container {
        padding: 60px 10%;
    }

    .news-header {
        text-align: center;
        letter-spacing: 8px;
        margin-bottom: 60px;
        text-transform: uppercase;
    }

    .news-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 40px;
    }

    .news-item {
        border-bottom: 1px solid #eee;
        padding-bottom: 30px;
        transition: 0.3s;
    }

    .news-item a {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .news-item:hover {
        transform: translateY(-5px);
        opacity: 0.8;
    }

    .news-img-wrapper {
        width: 100%;
        height: 250px;
        overflow: hidden;
        margin-bottom: 20px;
        background: #f9f9f9;
    }

    .news-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .news-date {
        font-size: 11px;
        color: #888;
        letter-spacing: 1px;
        margin-bottom: 10px;
        display: block;
    }

    .news-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 15px;
        line-height: 1.4;
        height: 56px;
        overflow: hidden;
    }

    .btn-readmore {
        font-size: 12px;
        font-weight: bold;
        text-decoration: underline;
        letter-spacing: 1px;
        text-transform: uppercase;
    }
</style>

<div class="news-container">
    <h2 class="news-header">Tin tức & Review</h2>
    <div class="news-grid">
        <?php foreach ($news_list as $news): ?>
            <article class="news-item">
                <a href="news-detail.php?id=<?php echo $news['MaTinTuc']; ?>">
                    <div class="news-img-wrapper">
                        <img src="assets/images/news/<?php echo $news['HinhAnh']; ?>"
                            class="news-img"
                            onerror="this.src='https://via.placeholder.com/600x400?text=Solpix+News'">
                    </div>
                    <span class="news-date"><?php echo date('d/m/Y', strtotime($news['NgayDang'])); ?></span>
                    <h3 class="news-title"><?php echo htmlspecialchars($news['TieuDe']); ?></h3>
                    <span class="btn-readmore">XEM CHI TIẾT</span>
                </a>
            </article>
        <?php endforeach; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>