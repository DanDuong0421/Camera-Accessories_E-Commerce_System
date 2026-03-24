<?php
// Thông tin cấu hình database
$host = 'localhost';
$db   = 'mayanh';
$user = 'root';      // Mặc định của XAMPP là root
$pass = '';          // Mặc định của XAMPP là trống
$charset = 'utf8mb4';

// Tạo chuỗi DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Các tùy chọn cho PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Đẩy lỗi ra ngoại lệ để dễ debug
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Trả về dữ liệu dạng mảng kết hợp
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Tắt giả lập chuẩn bị câu lệnh
];

try {
    // Tạo đối tượng kết nối PDO
    $pdo = new PDO($dsn, $user, $pass, $options);
    //echo "Kết nối thành công!"; // Bỏ comment dòng này để test thử
} catch (\PDOException $e) {
    // Nếu lỗi, dừng hệ thống và thông báo lỗi
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
