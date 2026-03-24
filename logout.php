<?php
session_start();

// 1. Xóa toàn bộ các biến trong Session (user_id, ho_ten, role...)
$_SESSION = array();

// 2. Nếu muốn xóa triệt để cả Cookie của Session (tùy chọn nhưng nên làm)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 3. Hủy bỏ Session trên Server
session_destroy();

// 4. Chuyển hướng người dùng về trang chủ
header("Location: index.php");
exit();
