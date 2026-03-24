# Solpix Store - Hệ Thống Thương Mại Điện Tử Nhiếp Ảnh Tích Hợp AI

**Solpix Store** là một nền tảng thương mại điện tử chuyên sâu dành cho cộng đồng yêu nhiếp ảnh và các nhà nhiếp ảnh chuyên nghiệp. Dự án kết hợp giữa quy trình quản lý bán lẻ truyền thống và công nghệ **Trí tuệ nhân tạo (Generative AI)** hiện đại để tối ưu hóa trải nghiệm mua sắm và quản lý vận hành.

## 🚀 Các Tính Năng Chính

### Dành cho Khách hàng:
- **Trưng bày sản phẩm thông minh:** Slider tương tác hỗ trợ hình ảnh chất lượng cao và video review thực tế (Sử dụng Swiper.js).
- **Bộ lọc nâng cao:** Tìm kiếm đa tiêu chí (Thương hiệu, Giá, Loại máy) giúp khách hàng dễ dàng tìm thấy sản phẩm ưng ý.
- **Quy trình đặt hàng bảo mật:** Hệ thống xác nhận đơn hàng chuyên nghiệp với minh chứng thanh toán (Upload hóa đơn/Bill).

### Dành cho Quản trị viên (Admin):
- **Bảng điều khiển (Dashboard):** Thống kê thời gian thực về doanh số, doanh thu và tình trạng tồn kho.
- **Quản lý đơn hàng:** Quy trình xử lý từ xác nhận đến xuất hóa đơn điện tử chuyên nghiệp.
- **Kiểm soát kho hàng:** Tự động cập nhật tồn kho và cảnh báo khi sản phẩm sắp hết hàng.
- **Quản trị nội dung (CMS):** Thao tác CRUD (Thêm, Sửa, Xóa) cho sản phẩm, danh mục và tin tức nhiếp ảnh.

---

## 🛠️ Công Nghệ Sử Dụng

- **Backend:** PHP 8.x (Sử dụng PDO để chống tấn công SQL Injection)
- **Cơ sở dữ liệu:** MySQL
- **Frontend:** HTML5, CSS3 (Flexbox/Grid), JavaScript (ES6+)
- **Thư viện bổ trợ:** Swiper.js (Sliders), AOS (Hiệu ứng cuộn trang)
- **Môi trường phát triển:** XAMPP / Apache Server

---

## 📁 Cấu Trúc Cơ Sở Dữ Liệu (Góc độ IS)

Hệ thống được thiết kế dựa trên mô hình dữ liệu quan hệ chặt chẽ để đảm bảo tính toàn vẹn dữ liệu:
- `SanPham`: Quản lý thông số kỹ thuật, giá bán và tồn kho.
- `DonHang` & `ChiTietDonHang`: Quản lý dữ liệu giao dịch và lịch sử mua hàng.
- `NguoiDung`: Phân quyền người dùng (Admin/Customer) và bảo mật tài khoản.
- `TinTuc`: Hệ thống quản lý bài viết hỗ trợ SEO cho cửa hàng.

---

## 💻 Hướng Dẫn Cài Đặt

1. **Sao chép mã nguồn:**
   ```bash
   git clone [https://github.com/ten-cua-ban/SolpixStore.git](https://github.com/ten-cua-ban/SolpixStore.git)
