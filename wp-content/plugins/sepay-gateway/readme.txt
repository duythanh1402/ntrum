=== SePay Gateway ===
 - Author: SePay Team
 - Contributors: sepayteam
 - Tags: woocommerce, payment gateway, vietqr, ngan hang, thanh toan
 - Requires WooCommerce at least: 2.1
 - Stable Tag: 1.1.2
 - Version: 1.1.2
 - Tested up to: 6.6
 - Requires at least: 5.6
 - Requires PHP: 7.2
 - Author URI: https://sepay.vn
 - Plugin URI: https://docs.sepay.vn/woocommerce.html
 - License: GPLv3.0
 - License URI: http://www.gnu.org/licenses/gpl-3.0.html

Thanh toán QR chuyển khoản (VietQR) bởi SePay cho WooCommerce. Hỗ trợ hơn 50 ngân hàng. Kết nối 15+ ngân hàng để xác nhận tự động.

== Description ==
**Lưu ý**: Trước khi sử dụng plugin bạn phải đăng kí một tài khoản trên SePay và liên kết ngân hàng vào trước [tại đây](https://sepay.vn). Link hướng dẫn [tại đây](https://docs.sepay.vn/woocommerce.html)

Cấu hình tùy biến mẫu VietQR bằng cách nhập mã Template VietQR được tạo tại website [Tạo QR Code VietQR](https://qr.sepay.vn/)

**Chính sách bảo mật**: [Xem tại đây](https://sepay.vn/privacy.html)

SePay hỗ trợ kết nối hơn 15 ngân hàng để tự xác nhận thanh toán khi khách hàng chuyển khoản. Bao gồm: Vietcombank, VPBank, VIB, VietinBank, MBBank, ACB, Sacombank, TPBank, Eximbank, HDBank, BIDV, TechcomBank, MSB, ShinhanBank, Agribank, PublicBank

Hỗ trợ cả tài khoản cá nhân và doanh nghiệp.

Các tính năng của plugin này:
- Hiển thị thông tin thanh toán: Hiện mã QR và box thông tin thanh toán. Giúp khách hàng quét QR code để thanh toán tiện lợi.
- Sau khi thanh toán thành công, từ 5 đến 10 giây:
+ Phía khách hàng: Giao diện thanh toán sẽ hiển thị thông báo Bạn đã thanh toán thành công. 
+ Đơn hàng tại giao diện admin sẽ tự động chuyển trạng thái từ ***Tạm giữ*** (On-Hold) sang ***Đang xử lý*** (Processing) vì đã nhận được thanh toán.
+ Đơn hàng tại giao diện admin sẽ tự động thêm ghi chú đã nhận được thanh toán với các thông tin như số tiền, thời gian nhận thanh toán.

Yêu cầu:

Bạn cần có tài khoản [tại đây](https://my.sepay.vn)

== Screenshots ==
1. Cài đặt plugin 
2. Thiết lập thông tin cần thiết để đảm bảo mọi thứ hoạt động chính xác và hợp lý
3. Khách hàng sẽ thấy thêm tùy chọn thanh toán qua chuyển khoản ngân hàng bằng cách quét mã QR 
4. Sau khi hoàn tất đơn hàng, khách hàng sẽ được gợi ý chuyển tiền qua mã QR hoặc chuyển tiền thủ công
5. Sau khi thanh toán thành công, khách hàng sẽ chờ khoảng từ 5 đến 10 giây để hệ thống xác nhận thanh toán và chuyển trạng thái đơn hàng nếu nhận đủ tiền
6. Đơn hàng sẽ chuyển trạng thái đã hoàn thành sau khi hệ thống xác nhận thành công
7. Hiển thị thông tin chi tiết của đơn hàng khi xác nhận thanh toán thành công

== Installation ==

Cấu hình plugin và thêm webhook tại SePay. Xem hướng dẫn tại https://docs.sepay.vn/woocommerce.html

== CHANGELOG ==

11/03/2025:
- [Tính năng mới] Cho phép WooCommerce kết nối với tài khoản của khách trên SePay để đồng bộ dữ liệu tài khoản ngân hàng, tiền tố mã thanh toán và webhook.


15/11/2023:
- [Fix lỗi]: Không xác thực thanh toán khi sử dụng VA MSB.

07/11/2023:
- [Cập nhật] Tối ưu giao diện CSS để tương thích với nhiều giao diện WordPress.
- [Tính năg mới] Hỗ trợ Digital/Downloadable product. Cho phép download sau khi thanh toán.
- [Fix lỗi] Fix lỗi json response.

04/10/2023:
- [Thay đổi]: Đổi trạng thái ghi chú cho đơn hàng từ ghi chú cho Khách hàng sang ghi chú cho Admin. Như vậy ghi chú tự động tạo bởi SePay sẽ không còn gửi email cho khách hàng.
- [Tính năng mới]: Cho phép tuỳ chỉnh thông điệp sau khi khách hàng thanh toán thành công. Hỗ trợ chữ thuần, HTML và Javascript. Nếu bạn muốn thêm code javascript để bắn sự kiện lên các trang tracking như Google Analytics, bạn có thể chèn mã Javascript tại đây.
- [Tính năng mới]: Tuỳ chỉnh trạng thái đơn hàng sau khi khách thanh toán đủ. Nếu không chỉ định, trạng thái này sẽ do WooCommerce quyết định. Hoặc bạn có thể chỉ định là Đang xử lý (Proccessing) hoặc Đã hoàn tất (Completed)
