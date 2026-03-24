<?php
require_once __DIR__ . '/config/db.php';
session_start();

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM DonHang WHERE MaDonHang = ?");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: index.php");
    exit();
}

// Xử lý gửi ảnh minh chứng
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['bill_image'])) {
    $file = $_FILES['bill_image'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = "BILL_" . $id . "_" . time() . "." . $ext;
    $target = "assets/images/bills/" . $filename;

    if (!is_dir('assets/images/bills/')) {
        mkdir('assets/images/bills/', 0777, true);
    }

    if (move_uploaded_file($file['tmp_name'], $target)) {
        $sql = "UPDATE DonHang SET TrangThaiDonHang = 'Chờ xác nhận', HinhAnhBill = ? WHERE MaDonHang = ?";
        $pdo->prepare($sql)->execute([$filename, $id]);

        echo "<script>alert('Gửi minh chứng thành công! Vui lòng chờ Admin xác nhận.'); window.location.href='index.php';</script>";
        exit();
    }
}

// THÔNG TIN NGÂN HÀNG
$BANK_ID = "MB";
$ACCOUNT_NO = "*821012004";
$ACCOUNT_NAME = "DUONG HUU DAN";
$AMOUNT = $order['TongTien'];
$CONTENT = "SOLPIX - " . $order['MaDonHang'];

$qr_url = "https://img.vietqr.io/image/$BANK_ID-$ACCOUNT_NO-compact.png?amount=$AMOUNT&addInfo=$CONTENT&accountName=$ACCOUNT_NAME";
?>

<div class="container" style="text-align: center; padding: 50px 0; font-family: 'Montserrat', sans-serif;">
    <h2 style="letter-spacing: 5px; text-transform: uppercase; font-weight: 300;">Thanh toán QR</h2>
    <p style="color: #888; margin-bottom: 30px;">Quét mã và tải lên ảnh màn hình chuyển khoản thành công</p>

    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 50px; max-width: 1000px; margin: 0 auto;">

        <div style="flex: 1; min-width: 300px; border: 1px solid #eee; padding: 20px;">
            <img src="<?php echo $qr_url; ?>" alt="QR" style="width: 100%; max-width: 250px;">
            <div style="text-align: center; margin: 25px auto 0; font-size: 16px; background: #fdfdfd; padding: 22px 30px; max-width: 420px; border: 1px solid #eee;">
                <p style="margin-bottom: 12px;">Số tiền: <strong><?php echo number_format($AMOUNT, 0, ',', '.'); ?>đ</strong></p>
                <p>Nội dung: <strong style="color: #0a58ca; font-size: 17px;"><?php echo $CONTENT; ?></strong></p>
            </div>
        </div>

        <div style="flex: 1; min-width: 300px; text-align: left; border: 1px solid #000; padding: 30px;">
            <h3 style="font-size: 14px; letter-spacing: 2px; text-transform: uppercase;">Xác nhận chuyển khoản</h3>
            <p style="font-size: 12px; color: #666; margin: 15px 0 25px;">Vui lòng tải lên ảnh chụp màn hình giao dịch để hệ thống xử lý nhanh hơn.</p>

            <form method="POST" enctype="multipart/form-data">
                <div style="margin-bottom: 25px;">
                    <label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 10px;">CHỌN ẢNH BILL</label>
                    <input type="file" name="bill_image" accept="image/*" required style="font-size: 12px;">
                </div>

                <button type="submit" style="background: #000; color: #fff; border: none; padding: 15px; width: 100%; font-weight: bold; cursor: pointer; letter-spacing: 2px; text-transform: uppercase;">
                    Gửi minh chứng thanh toán
                </button>
            </form>

            <div style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; text-align: center;">
                <p style="font-size: 11px; color: #888; margin-bottom: 10px;">Hoặc thay đổi ý định?</p>
                <a href="change_payment.php?id=<?php echo $id; ?>"
                    onclick="return confirm('Bạn muốn chuyển sang thanh toán khi nhận hàng (COD)?')"
                    style="display: inline-block; color: #000; text-decoration: underline; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">
                    Thanh toán khi nhận hàng (COD)
                </a>
            </div>

            <a href="index.php" style="display: block; text-align: center; margin-top: 20px; color: #aaa; text-decoration: none; font-size: 11px;">Để sau</a>
        </div>
    </div>
</div>