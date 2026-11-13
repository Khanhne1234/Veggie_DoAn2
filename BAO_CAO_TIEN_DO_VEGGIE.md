# BÁO CÁO TIẾN ĐỘ ĐỀ TÀI VEGGIE

Ngày rà soát: 04/11/2026.

Tên đề tài đề xuất: **Xây dựng website bán thực phẩm trực tuyến Veggie tích hợp trợ lý tư vấn bán hàng**. Cần dùng tên đề tài đã đăng ký nếu khác tên đề xuất này.

## 1. Phạm vi và hiện trạng

Đã đối chiếu cấu trúc dự án, cấu hình công nghệ, routes khách hàng/quản trị, models, migrations, danh sách giao diện, các luồng xử lý chính và bộ kiểm thử hiện có. Đây là rà soát mã nguồn phục vụ lập tiến độ; chưa thay thế kiểm thử đầy đủ trên trình duyệt, kiểm thử dịch vụ bên ngoài hoặc nghiệm thu triển khai.

Dự án sử dụng PHP ^8.2, Laravel ^12.0, Blade và Vite. Mã nguồn chia hai khu vực khách hàng và quản trị, có middleware xác thực và kiểm tra quyền quản trị.

| Nhóm | Nội dung đã có trong mã nguồn | Căn cứ chính |
|---|---|---|
| Tài khoản | Đăng ký, kích hoạt qua email, đăng nhập/đăng xuất, quên/đặt lại mật khẩu, hồ sơ và địa chỉ giao hàng | [Routes khách hàng](Veggie/routes/web.php), [controllers khách hàng](Veggie/app/Http/Controllers/Clients/) |
| Tra cứu sản phẩm | Danh sách, lọc, tìm kiếm, chi tiết và sản phẩm liên quan | [ProductController](Veggie/app/Http/Controllers/Clients/ProductController.php), [SearchController](Veggie/app/Http/Controllers/Clients/SearchController.php) |
| Giỏ hàng | Giỏ khách vãng lai và khách đăng nhập, cập nhật/xóa sản phẩm, hợp nhất khi đăng nhập, kiểm tra tồn kho | [CartController](Veggie/app/Http/Controllers/Clients/CartController.php), [MergeCartAfterLogin](Veggie/app/Listeners/MergeCartAfterLogin.php), [CartInventory](Veggie/app/Services/CartInventory.php) |
| Đặt hàng | Đặt COD/PayPal, lưu chi tiết và thanh toán, trừ tồn kho trong giao dịch có khóa bản ghi | [CheckoutController](Veggie/app/Http/Controllers/Clients/CheckoutController.php) |
| Theo dõi đơn | Xem đơn, hủy đơn, xác nhận nhận hàng; phía quản trị có xác nhận, hủy và gửi hóa đơn email | [OrderController khách hàng](Veggie/app/Http/Controllers/Clients/OrderController.php), [OrderController quản trị](Veggie/app/Http/Controllers/Admin/OrderController.php) |
| Tương tác | Yêu thích, đánh giá, gửi liên hệ và phản hồi email | [Routes khách hàng](Veggie/routes/web.php), [Routes quản trị](Veggie/routes/admin.php) |
| Trợ lý bán hàng | Lưu lịch sử hội thoại, tích hợp Gemini, tìm sản phẩm và phản hồi dự phòng khi dịch vụ AI không khả dụng | [ChatController](Veggie/app/Http/Controllers/Clients/ChatController.php) |
| Quản trị | Quản lý tài khoản/vai trò, danh mục, sản phẩm/ảnh/tồn kho, đơn, liên hệ, thông báo và dashboard | [Routes quản trị](Veggie/routes/admin.php), [controllers quản trị](Veggie/app/Http/Controllers/Admin/) |
| Thống kê | Tổng quan, sản phẩm bán chạy, biểu đồ giá trị đơn hàng theo ngày/tuần/tháng; hiện loại đơn hủy nhưng vẫn tính đơn chờ và đang xử lý | [DashboardController](Veggie/app/Http/Controllers/Admin/DashboardController.php) |
| Phân tích thiết kế | Có bộ sơ đồ Use Case, Class và Sequence cho các nghiệp vụ chính ở thư mục gốc | [Use Case tổng quát](SoDo_UseCase_TongQuat_Veggie.drawio), [Class tổng thể](SoDo_Class_TongThe_Veggie.drawio) |
| Kiểm thử | Có ba tệp kiểm thử chức năng cho giỏ hàng/tồn kho, phản hồi chatbot và thống kê | [tests/Feature](Veggie/tests/Feature/) |

**Lưu ý:** Có mã nguồn không đồng nghĩa đã hoàn tất nghiệm thu. Các mốc bên dưới là cách phân chia công việc và mục tiêu bàn giao theo yêu cầu báo cáo, không phải xác nhận lịch sử hoàn thành vào từng ngày. Không suy ra phần trăm hoàn thành thực tế từ số lượng chức năng.

## 2. Nội dung ngắn để điền vào bốn ô trong biểu mẫu

| 14/11 — Đợt 1 | 28/11 — Đợt 2 | 12/12 — Đợt 3 | 26/12 — Đợt 4 |
|---|---|---|---|
| Khảo sát nghiệp vụ bán thực phẩm trực tuyến; xác định yêu cầu và tác nhân; thiết kế Use Case, Class, Sequence và cơ sở dữ liệu; xây dựng khung giao diện khách hàng, quản trị và khởi tạo dự án Laravel. | Xây dựng đăng ký, kích hoạt email, đăng nhập, quên mật khẩu, hồ sơ và địa chỉ; hoàn thiện danh sách, tìm kiếm, lọc, chi tiết sản phẩm, yêu thích và giỏ hàng; xây dựng quản lý danh mục, sản phẩm và phân quyền cơ bản. | Hoàn thiện đặt hàng COD và tích hợp PayPal; kiểm tra tồn kho; theo dõi, xác nhận, hủy đơn; quản lý tài khoản, đơn hàng, liên hệ và thống kê; tích hợp đánh giá sản phẩm và trợ lý tư vấn bán hàng. | Kiểm thử toàn bộ luồng khách hàng và quản trị; sửa lỗi phân quyền, thanh toán và tồn kho; kiểm tra email, PayPal và AI; hoàn thiện giao diện, tài liệu, hướng dẫn cài đặt/sử dụng, dữ liệu demo, slide và kịch bản bảo vệ. |

Hạn nộp: **trước 17h00 ngày 14/11, 28/11, 12/12 và 26/12**, theo thông báo trong biểu mẫu.

## 3. Nội dung và sản phẩm bàn giao từng giai đoạn

### Đợt 1 — Đến trước 17h00 ngày 14/11: Phân tích yêu cầu và thiết kế hệ thống

- Khảo sát quy trình mua hàng, quản lý sản phẩm, xử lý đơn và hỗ trợ khách hàng.
- Xác định các tác nhân: khách vãng lai, khách hàng, nhân viên và quản trị viên; mô tả quyền và phạm vi chức năng.
- Xác định yêu cầu chức năng và phi chức năng, phạm vi COD/PayPal và trợ lý bán hàng.
- Hoàn thiện Use Case tổng quát/khách hàng/quản trị, Class và Sequence cho các luồng chính.
- Thiết kế cơ sở dữ liệu: tài khoản/vai trò/quyền; danh mục/sản phẩm/ảnh; giỏ hàng/địa chỉ; đơn/chi tiết/thanh toán; yêu thích/đánh giá/liên hệ/thông báo/hội thoại.
- Thiết lập Laravel, cấu trúc MVC, migrations, seeders và khung giao diện.

**Bàn giao:** Tài liệu yêu cầu, bộ sơ đồ, thiết kế dữ liệu và bản chạy khung giao diện. Cần đối chiếu sơ đồ với mã nguồn trước khi nộp.

### Đợt 2 — Từ 15/11 đến trước 17h00 ngày 28/11: Chức năng nền tảng và mua sắm

- Hoàn thiện đăng ký, kích hoạt email, đăng nhập/đăng xuất, quên/đặt lại mật khẩu.
- Hoàn thiện hồ sơ, đổi mật khẩu và quản lý địa chỉ giao hàng.
- Xây dựng trang chủ, danh sách, lọc, tìm kiếm, chi tiết và sản phẩm liên quan.
- Hoàn thiện yêu thích; thêm, sửa số lượng, xóa và xem giỏ hàng.
- Xử lý giỏ khách vãng lai, giỏ tài khoản và hợp nhất giỏ sau đăng nhập; kiểm tra tồn kho và dữ liệu số lượng.
- Xây dựng đăng nhập quản trị, phân quyền cơ bản, quản lý danh mục và sản phẩm/ảnh/tồn kho.

**Bàn giao:** Demo từ đăng ký/đăng nhập đến tìm sản phẩm và thêm vào giỏ; demo quản trị thêm/sửa/xóa danh mục, sản phẩm; dữ liệu mẫu và kết quả kiểm thử các luồng nền tảng.

### Đợt 3 — Từ 29/11 đến trước 17h00 ngày 12/12: Giao dịch và chức năng mở rộng

- Hoàn thiện chọn địa chỉ, kiểm tra giỏ và tồn kho, tính giá, phí giao hàng, tạo đơn COD.
- Hoàn thiện PayPal với kiểm tra giao dịch tại máy chủ, đối chiếu số tiền/tiền tệ và ngăn ghi nhận trùng.
- Hoàn thiện xác nhận đơn, nhận hàng, hủy đơn, hoàn tồn và gửi hóa đơn email; thống nhất trạng thái đơn.
- Hoàn thiện quản lý tài khoản/vai trò, thông báo và liên hệ/phản hồi.
- Hoàn thiện đánh giá sản phẩm và kiểm tra điều kiện đánh giá tại máy chủ.
- Hoàn thiện dashboard; xác định rõ chỉ số doanh thu thực thu hay giá trị đơn đặt hàng.
- Tích hợp trợ lý tư vấn Gemini, lịch sử hội thoại, dữ liệu sản phẩm phù hợp và cơ chế trả lời dự phòng.

**Bàn giao:** Demo mua hàng xuyên suốt, thanh toán PayPal sandbox đã đối soát, xử lý đơn phía quản trị, thống kê và hội thoại tư vấn; biên bản kiểm thử tích hợp.

### Đợt 4 — Từ 13/12 đến trước 17h00 ngày 26/12: Kiểm thử, hoàn thiện và bảo vệ

- Kiểm thử theo vai trò khách vãng lai/khách hàng/nhân viên/quản trị viên; kiểm tra truy cập trái quyền và truy cập dữ liệu người khác.
- Kiểm thử giỏ rỗng, số lượng không hợp lệ, hết hàng, thay đổi tồn, gửi yêu cầu đồng thời, hủy lặp và gửi lại yêu cầu thanh toán.
- Kiểm thử thành công/thất bại/hủy thanh toán PayPal; dịch vụ email và AI không khả dụng.
- Sửa các vấn đề còn tồn tại, chạy lại kiểm thử hồi quy và kiểm tra giao diện trên máy tính/điện thoại.
- Chuẩn bị cấu hình mẫu, hướng dẫn cài đặt, migrate/seed, tài khoản demo và hướng dẫn sử dụng; sao lưu dữ liệu demo.
- Hoàn thiện báo cáo đồ án, đồng bộ sơ đồ với phiên bản cuối, ảnh minh họa, slide, video nếu được yêu cầu và kịch bản trình diễn.

**Bàn giao:** Phiên bản chốt có thể cài đặt và demo; kết quả kiểm thử và danh sách giới hạn còn lại; báo cáo, sơ đồ, hướng dẫn và slide bảo vệ.

## 4. Các điểm cần xử lý trước khi xác nhận hoàn thành

Các nhận xét sau dựa trên mã nguồn đã đọc, chưa phải kết quả khai thác hoặc kiểm thử toàn bộ hệ thống.

| Ưu tiên | Phát hiện | Hướng hoàn thiện | Căn cứ |
|---|---|---|---|
| Cao | Nhánh đặt PayPal ghi trạng thái thanh toán `completed` và nhận `transactionID` từ request; chưa thấy xác minh giao dịch PayPal tại máy chủ trong luồng này | Xác minh/capture tại máy chủ, đối chiếu số tiền, tiền tệ, người nhận và chống dùng lại giao dịch; xử lý trường hợp đã thanh toán nhưng tạo đơn thất bại | [CheckoutController](Veggie/app/Http/Controllers/Clients/CheckoutController.php) |
| Cao | Xem chi tiết đơn bằng ID chưa lọc theo người dùng sở hữu | Ràng buộc `user_id` hoặc áp dụng policy trước khi trả dữ liệu | [OrderController khách hàng](Veggie/app/Http/Controllers/Clients/OrderController.php) |
| Cao | Tạo đơn lấy `address_id` từ request trực tiếp; kiểm tra sở hữu ở API lấy địa chỉ chưa thay thế kiểm tra lúc tạo đơn | Kiểm tra địa chỉ tồn tại và thuộc tài khoản hiện tại ngay trong luồng tạo đơn | [CheckoutController](Veggie/app/Http/Controllers/Clients/CheckoutController.php) |
| Cao | Hủy đơn phía quản trị chưa chặn hủy lặp; hoàn tồn và đổi trạng thái chưa nằm trong giao dịch có khóa. Phía khách cũng chưa khóa khi hủy | Khóa bản ghi, kiểm tra trạng thái được phép và chỉ hoàn tồn một lần trong giao dịch | [OrderController quản trị](Veggie/app/Http/Controllers/Admin/OrderController.php), [OrderController khách hàng](Veggie/app/Http/Controllers/Clients/OrderController.php) |
| Trung bình | Trạng thái hủy dùng cả `canceled` và `cancelled`; xác nhận đơn quản trị chưa kiểm tra trạng thái nguồn | Thống nhất trạng thái, quy tắc chuyển trạng thái và đồng bộ thanh toán/lịch sử nếu thuộc phạm vi đề tài | [Controllers đơn hàng](Veggie/app/Http/Controllers/Admin/OrderController.php) |
| Trung bình | API gửi đánh giá chỉ kiểm tra sản phẩm/rating/comment, chưa kiểm tra đã mua hoặc đã đánh giá | Nếu quy định chỉ người mua được đánh giá, kiểm tra đơn hoàn tất và chống đánh giá lặp tại máy chủ | [ReviewController](Veggie/app/Http/Controllers/Clients/ReviewController.php) |
| Trung bình | Route quản trị liên hệ gọi `confirm`, nhưng controller đã đọc chưa có phương thức này | Bổ sung xử lý hoặc bỏ route/giao diện không sử dụng; kiểm thử đường dẫn tương ứng | [Routes quản trị](Veggie/routes/admin.php), [ContactController](Veggie/app/Http/Controllers/Admin/ContactController.php) |
| Trung bình | Dashboard đang cộng giá trị mọi đơn chưa hủy, gồm cả đơn chưa hoàn tất/chưa thanh toán | Chốt định nghĩa chỉ số và sửa tên hiển thị hoặc điều kiện tính theo nghiệp vụ | [DashboardController](Veggie/app/Http/Controllers/Admin/DashboardController.php) |
| Trung bình | Bộ test hiện có tập trung vào giỏ hàng, chatbot và dashboard | Bổ sung kiểm thử quyền sở hữu đơn/địa chỉ, checkout, xác minh PayPal, hủy lặp và quyền quản trị | [tests/Feature](Veggie/tests/Feature/) |
| Tài liệu | README ứng dụng chủ yếu là tài liệu mặc định Laravel | Viết hướng dẫn riêng cho Veggie: yêu cầu môi trường, cài đặt, CSDL, email/PayPal/Gemini, tài khoản demo và cách kiểm thử | [README](Veggie/README.md) |

Không đưa API key, mật khẩu hoặc thông tin kết nối thật vào báo cáo và mã nguồn mẫu.

## 5. Kết quả xác minh

Đã chạy `php artisan test` ngày 04/11/2026: **12 kiểm thử đạt, 95 assertions**, gồm giỏ hàng/tồn kho, chatbot và thống kê. Cấu hình kiểm thử sử dụng SQLite trong bộ nhớ. Kết quả này không xác nhận toàn bộ chức năng, dịch vụ PayPal/email/Gemini thực tế hoặc các vấn đề nêu tại mục 4 đã được kiểm chứng đầy đủ.

## 6. Cách dùng khi báo cáo

- Dùng bảng ở mục 2 để điền biểu mẫu theo ảnh.
- Dùng mục 3 để trình bày công việc, kết quả bàn giao và demo của từng đợt.
- Chỉ chuyển câu chữ thành “đã hoàn thành” khi có bản chạy và minh chứng kiểm thử tương ứng.
- Dùng mục 4 làm danh sách hoàn thiện nội bộ; báo cáo trung thực vấn đề còn tồn tại nếu chưa xử lý xong.
- Không ghi tiến độ 25%/50%/75%/100% như số liệu đã đo nếu chưa có tiêu chí nghiệm thu và trọng số công việc.
