# Review dự án và bộ câu hỏi bảo vệ đồ án Veggie

> Rà soát mã nguồn ngày 13/11/2026. Tài liệu này dùng để chuẩn bị báo cáo và luyện trả lời hội đồng. Các nhận xét về lỗi dựa trên mã nguồn tại thời điểm rà soát; chưa thay thế kiểm thử đầu cuối hoặc nghiệm thu triển khai.

## 1. Nhận định của người chấm

**Điểm tham khảo: khoảng 7/10 ở trạng thái hiện tại.** Dự án có phạm vi chức năng rộng, cấu trúc Laravel rõ ràng, có xử lý tồn kho tương đối cẩn thận và có chatbot kết hợp dữ liệu sản phẩm. Điểm cần cải thiện nhiều nhất là xác minh thanh toán PayPal tại máy chủ, kiểm tra quyền sở hữu dữ liệu và tính nhất quán của trạng thái đơn hàng.

Đã chạy `php artisan test`: **12 test đạt, 95 assertions**. Test hiện tập trung vào giỏ hàng/tồn kho, chatbot dự phòng và thống kê. Chưa có bằng chứng kiểm thử đầy đủ cho checkout, PayPal, phân quyền, hủy đơn và luồng email thực tế.

### 1.1. Giới thiệu đề tài ngắn để nói trước hội đồng

> Veggie là website bán thực phẩm trực tuyến xây dựng bằng Laravel. Khách có thể tìm và lọc sản phẩm, dùng giỏ hàng, đặt hàng, theo dõi đơn và nhận hỗ trợ từ chatbot. Hệ thống có khu quản trị để quản lý sản phẩm, danh mục, người dùng, đơn hàng và thống kê. Phần em tập trung xử lý là kiểm tra tồn kho khi mua hàng và kết hợp câu trả lời dựa trên dữ liệu cửa hàng với Gemini cho chatbot.

### 1.2. Phạm vi chức năng đã có

| Nhóm | Chức năng | Căn cứ |
|---|---|---|
| Tài khoản | Đăng ký, kích hoạt email, đăng nhập/đăng xuất, quên/đặt lại mật khẩu, hồ sơ và địa chỉ | [Routes khách hàng](Veggie/routes/web.php), [AuthController](Veggie/app/Http/Controllers/Clients/AuthController.php) |
| Sản phẩm | Danh sách, lọc, tìm kiếm, chi tiết, sản phẩm liên quan | [ProductController](Veggie/app/Http/Controllers/Clients/ProductController.php), [SearchController](Veggie/app/Http/Controllers/Clients/SearchController.php) |
| Giỏ hàng | Giỏ khách vãng lai trong session, giỏ tài khoản trong CSDL, hợp nhất giỏ, kiểm tra tồn kho | [CartController](Veggie/app/Http/Controllers/Clients/CartController.php), [MergeCartAfterLogin](Veggie/app/Listeners/MergeCartAfterLogin.php) |
| Giao dịch | Đặt COD/PayPal, lưu đơn và thanh toán, trừ kho | [CheckoutController](Veggie/app/Http/Controllers/Clients/CheckoutController.php) |
| Đơn hàng | Xem, xác nhận, hủy, xác nhận đã nhận, gửi hóa đơn email | [Controller khách](Veggie/app/Http/Controllers/Clients/OrderController.php), [controller quản trị](Veggie/app/Http/Controllers/Admin/OrderController.php) |
| Tương tác | Yêu thích, đánh giá, liên hệ, chatbot | [Routes khách hàng](Veggie/routes/web.php), [ChatController](Veggie/app/Http/Controllers/Clients/ChatController.php) |
| Quản trị | Tài khoản, danh mục, sản phẩm, đơn, liên hệ, thông báo, dashboard | [Routes quản trị](Veggie/routes/admin.php) |

### 1.3. Công nghệ

- Backend: PHP `^8.2`, Laravel `^12.0`, Eloquent ORM, migrations, seeders, PHPUnit; xem [composer.json](Veggie/composer.json).
- Frontend: Blade, JavaScript, jQuery, Bootstrap, Toastr; Vite và Tailwind có trong cấu hình dự án; xem [package.json](Veggie/package.json), [vite.config.js](Veggie/vite.config.js), [layout khách](Veggie/resources/views/layouts/client.blade.php), [layout quản trị](Veggie/resources/views/layouts/admin.blade.php).
- Tích hợp: PayPal JavaScript SDK, Gemini API, email; xem [services.php](Veggie/config/services.php).
- Kiểm thử: SQLite trong bộ nhớ, mail giả lập, cache/session dạng array; xem [phpunit.xml](Veggie/phpunit.xml).

## 2. Những điểm có thể bị hỏi khó

| Mức độ | Phát hiện | Cách trả lời và hướng sửa |
|---|---|---|
| Cao | Máy chủ nhận `transactionID` PayPal từ request và ghi thanh toán `completed`, chưa xác minh lại với PayPal, số tiền, tiền tệ và trùng giao dịch. [CheckoutController](Veggie/app/Http/Controllers/Clients/CheckoutController.php), [custom.js](Veggie/public/assets/clients/js/custom.js) | Nói đây là tích hợp ở mức demo. Cần xác minh/capture tại máy chủ, đối soát và chống dùng lại giao dịch trước khi đưa vào vận hành thật. |
| Cao | Trang chi tiết đơn lấy theo ID mà chưa lọc `user_id` hiện tại. [OrderController khách](Veggie/app/Http/Controllers/Clients/OrderController.php) | Dùng truy vấn theo người sở hữu hoặc policy; thêm test truy cập đơn của người khác. |
| Cao | Tạo đơn lấy `address_id` trực tiếp từ request, chưa xác minh địa chỉ thuộc khách hiện tại ngay lúc tạo đơn. [CheckoutController](Veggie/app/Http/Controllers/Clients/CheckoutController.php) | Kiểm tra tồn tại và quyền sở hữu trong giao dịch tạo đơn. |
| Cao | Hủy đơn phía quản trị chưa chặn hủy lặp; hoàn kho và đổi trạng thái chưa nằm trong giao dịch có khóa. [OrderController quản trị](Veggie/app/Http/Controllers/Admin/OrderController.php) | Quy định trạng thái được phép hủy; khóa đơn/sản phẩm và hoàn kho đúng một lần trong một giao dịch. |
| Cao | Xóa sản phẩm có thể xóa các dòng `order_items` vì khóa ngoại `onDelete('cascade')`. [Migration order_items](Veggie/database/migrations/2026_04_13_053610_create_order_items_table.php) | Cân nhắc xóa mềm/ngừng bán; giữ dữ liệu lịch sử đơn và snapshot sản phẩm. |
| Trung bình | Khách và quản trị dùng hai chuỗi hủy `cancelled` và `canceled`; xác nhận đơn quản trị chưa kiểm tra trạng thái nguồn. [Controllers đơn hàng](Veggie/app/Http/Controllers/Admin/OrderController.php) | Chuẩn hóa một tập trạng thái và quy tắc chuyển trạng thái. |
| Trung bình | API đánh giá chưa kiểm tra người dùng đã mua hoặc đã đánh giá, dù giao diện có xét điều kiện. [ReviewController](Veggie/app/Http/Controllers/Clients/ReviewController.php) | Kiểm tra ở máy chủ; thêm ràng buộc chống đánh giá trùng nếu nghiệp vụ yêu cầu. |
| Trung bình | Chatbot nói miễn phí ship từ 300.000 VNĐ nhưng checkout luôn cộng 15.000 VNĐ. [ChatController](Veggie/app/Http/Controllers/Clients/ChatController.php), [CheckoutController](Veggie/app/Http/Controllers/Clients/CheckoutController.php) | Thống nhất một chính sách và dùng chung một nguồn tính phí. |
| Trung bình | Dashboard gọi là doanh thu nhưng cộng cả đơn `pending`/`processing` chưa hủy. [DashboardController](Veggie/app/Http/Controllers/Admin/DashboardController.php) | Đổi nhãn thành giá trị đơn hàng chưa hủy hoặc chỉ cộng đơn đã thanh toán/hoàn tất tùy định nghĩa nghiệp vụ. |
| Trung bình | Tỷ giá VNĐ/USD cố định `23000` ở JavaScript. [custom.js](Veggie/public/assets/clients/js/custom.js) | Xác định tỷ giá có kiểm soát và kiểm tra số tiền phía máy chủ. |
| Trung bình | Route `admin/contact/confirm` trỏ tới `confirm`, trong khi controller liên hệ quản trị hiện không có phương thức này. [Routes quản trị](Veggie/routes/admin.php), [ContactController quản trị](Veggie/app/Http/Controllers/Admin/ContactController.php) | Bổ sung xử lý hoặc bỏ route không sử dụng; thêm test. |
| Tài liệu | [README](Veggie/README.md) vẫn là tài liệu mặc định của Laravel. | Viết hướng dẫn cài đặt, cấu hình dịch vụ, migrate/seed, tài khoản demo, chạy test. |
| Bản làm việc hiện tại | [home.blade.php](Veggie/resources/views/clients/pages/home.blade.php) từng bị xóa trong working tree, trong khi [HomeController](Veggie/app/Http/Controllers/Clients/HomeController.php) vẫn gọi view đó. | Đã khôi phục nguyên vẹn từ Git ngày 13/11/2026; cần chạy thử trang chủ trên bản demo. |

## 3. Bộ câu hỏi và câu trả lời mẫu

### A. Tổng quan và kiến trúc

**1. Đề tài giải quyết vấn đề gì?**  
Veggie số hóa việc giới thiệu và bán thực phẩm, giúp khách tìm sản phẩm, mua hàng và theo dõi đơn; nhân viên quản lý hàng hóa và giao dịch trên cùng hệ thống.

**2. Các tác nhân của hệ thống là ai?**  
Khách vãng lai, khách hàng đã đăng nhập, nhân viên và quản trị viên. Mỗi nhóm có tập chức năng và quyền khác nhau.

**3. Vì sao chọn Laravel?**  
Laravel có sẵn định tuyến, MVC, ORM, session, xác thực, validation, migrations và môi trường test, phù hợp ứng dụng có nhiều thực thể và quy trình giao dịch.

**4. MVC thể hiện ở đâu?**  
Route nhận URL, controller xử lý yêu cầu, model làm việc với CSDL, Blade hiển thị. Ví dụ: [routes khách](Veggie/routes/web.php) → [ProductController](Veggie/app/Http/Controllers/Clients/ProductController.php) → [Product](Veggie/app/Models/Product.php) → [products.blade.php](Veggie/resources/views/clients/pages/products.blade.php).

**5. Vì sao tách khu khách hàng và quản trị?**  
Hai khu vực có nghiệp vụ và quyền khác nhau. Tách route/controller giúp quản lý luồng rõ hơn và áp dụng middleware xác thực, phân quyền cho khu quản trị.

**6. Hệ thống có phải SPA không?**  
Không. Đây chủ yếu là ứng dụng Laravel render bằng Blade; một số thao tác như lọc, giỏ và chat dùng JavaScript/AJAX để cập nhật mà không tải lại toàn bộ trang.

**7. Vai trò của Vite là gì?**  
Vite phục vụ quá trình phát triển và build tài nguyên frontend. Cấu hình nằm ở [vite.config.js](Veggie/vite.config.js).

**8. Vai trò của Eloquent ORM là gì?**  
Eloquent biểu diễn các bảng thành model và quan hệ, giúp truy vấn sản phẩm, người dùng, đơn và chi tiết đơn rõ ràng hơn so với tự viết toàn bộ SQL.

**9. Migration và seeder khác nhau thế nào?**  
Migration tạo/thay đổi cấu trúc CSDL; seeder tạo dữ liệu nền hoặc dữ liệu mẫu. Xem [migrations](Veggie/database/migrations) và [DatabaseSeeder](Veggie/database/seeders/DatabaseSeeder.php).

**10. Dự án dùng CSDL nào?**  
Repository có tệp SQLite cho phát triển cục bộ; bộ test dùng SQLite trong bộ nhớ. Ứng dụng Laravel có thể cấu hình kết nối khác qua môi trường. Khi bảo vệ cần nêu đúng DB của bản demo đang chạy.

### B. Cơ sở dữ liệu và phân quyền

**11. Các nhóm bảng chính là gì?**  
Người dùng/quyền (`users`, `roles`, `permissions`), hàng hóa (`categories`, `products`, `product_images`), giao dịch (`cart_items`, `orders`, `order_items`, `payments`, `shipping_addresses`) và tương tác (`reviews`, `wishlists`, `contacts`, `chat_messages`).

**12. Quan hệ đơn hàng–sản phẩm được thiết kế thế nào?**  
Một đơn có nhiều `order_items`; mỗi dòng chỉ tới một sản phẩm và lưu số lượng, đơn giá tại thời điểm mua. Xem [migration order_items](Veggie/database/migrations/2026_04_13_053610_create_order_items_table.php).

**13. Vì sao phải lưu giá ở `order_items`?**  
Giá sản phẩm có thể thay đổi sau khi mua. Giá trong chi tiết đơn giữ lại giá dùng để tính giao dịch lịch sử.

**14. Role khác permission thế nào?**  
Role là nhóm người dùng như `admin`, `staff`, `customer`. Permission là quyền thao tác như `manage_products` hay `manage_orders`. [CheckPermisson](Veggie/app/Http/Middleware/CheckPermisson.php) kiểm tra permission trước khi cho truy cập route quản trị.

**15. Nhân viên được quyền gì theo dữ liệu khởi tạo?**  
`staff` có `manage_products` và `manage_contacts`; `admin` được gán các quyền khai báo. Xem [RolesTableSeeder](Veggie/database/seeders/RolesTableSeeder.php).

**16. Vì sao có hai guard `web` và `admin`?**  
Để tách session đăng nhập cho khách và khu quản trị dù cùng dùng model `User`. Xem [auth.php](Veggie/config/auth.php).

**17. Chỉ ẩn nút trên giao diện có đủ để bảo vệ chức năng không?**  
Không. Người dùng vẫn gửi HTTP request trực tiếp được. Máy chủ phải kiểm tra xác thực, quyền và quyền sở hữu dữ liệu.

**18. Nếu nhân viên truy cập URL chỉ dành cho admin?**  
Middleware kiểm tra permission; chức năng không được cấp sẽ trả 403. Cần kiểm thử cả URL và request trực tiếp, không chỉ giao diện.

**19. CSRF là gì?**  
Là yêu cầu giả mạo từ trang khác khi người dùng đang có session. Laravel dùng CSRF token cho yêu cầu thay đổi dữ liệu; cơ chế này không thay cho phân quyền hay xác minh thanh toán.

### C. Sản phẩm, tìm kiếm và giỏ hàng

**20. Khách tìm kiếm sản phẩm như thế nào?**  
Hệ thống lọc từ không quan trọng trong câu hỏi, rồi thử tìm theo cụm từ, tất cả từ khóa, cuối cùng là bất kỳ từ khóa nào. Xem [SearchController](Veggie/app/Http/Controllers/Clients/SearchController.php).

**21. Lọc sản phẩm hỗ trợ gì?**  
Danh mục, khoảng giá và sắp xếp theo giá hoặc sản phẩm mới. Kết quả phân trang và trả HTML qua AJAX. Xem [ProductController](Veggie/app/Http/Controllers/Clients/ProductController.php).

**22. Khách chưa đăng nhập dùng giỏ được không?**  
Có. Giỏ khách vãng lai lưu trong session; giỏ người đăng nhập lưu trong bảng `cart_items`. Xem [CartController](Veggie/app/Http/Controllers/Clients/CartController.php).

**23. Khi đăng nhập, giỏ khách vãng lai được xử lý ra sao?**  
[MergeCartAfterLogin](Veggie/app/Listeners/MergeCartAfterLogin.php) cộng số lượng từ giỏ session vào giỏ tài khoản và xóa giỏ session. Sau khi gộp vẫn phải kiểm tra lại tồn kho trước thanh toán.

**24. Nếu thêm cùng sản phẩm hai lần thì số lượng được kiểm tra ra sao?**  
Số lượng mới bằng số lượng đang có cộng số lượng thêm; nếu lớn hơn tồn kho, server trả lỗi và giữ nguyên giỏ. Xem [CartController](Veggie/app/Http/Controllers/Clients/CartController.php).

**25. Nếu sản phẩm hết hàng sau khi đã nằm trong giỏ?**  
Giỏ vẫn hiển thị sản phẩm và cảnh báo, nhưng chặn checkout đến khi khách xử lý. Trường hợp này có test trong [CartInventoryTest](Veggie/tests/Feature/CartInventoryTest.php).

**26. Vì sao không trừ kho ngay khi thêm vào giỏ?**  
Thêm giỏ chưa phải cam kết mua và có thể bỏ dở. Hệ thống kiểm tra tồn kho khi thêm/cập nhật, rồi kiểm tra lại và trừ kho lúc tạo đơn.

**27. Vì sao cần kiểm tra tồn kho lại ở checkout?**  
Tồn kho có thể đổi sau khi thêm giỏ. Kiểm tra lần cuối dựa trên dữ liệu hiện tại mới giảm nguy cơ bán vượt số lượng.

**28. Vì sao dùng transaction và `lockForUpdate`?**  
Transaction giữ việc tạo đơn, tạo dòng đơn, trừ kho, tạo thanh toán và xóa giỏ cùng thành công hoặc rollback. `lockForUpdate` nhằm hạn chế nhiều checkout cùng dùng một tồn kho. Xem [CheckoutController](Veggie/app/Http/Controllers/Clients/CheckoutController.php). Cần thử cạnh tranh trên DB triển khai thực tế.

### D. Checkout, thanh toán và đơn hàng

**29. Luồng COD gồm những bước nào?**  
Khách đăng nhập → chọn địa chỉ → kiểm tra giỏ/tồn kho → tạo đơn và các dòng đơn → trừ kho → tạo thanh toán `pending` → xóa giỏ. Xem [CheckoutController](Veggie/app/Http/Controllers/Clients/CheckoutController.php).

**30. Tổng tiền tính thế nào?**  
Tổng giá từng dòng giỏ theo giá hiện tại cộng phí giao hàng 15.000 VNĐ. Cần thống nhất chính sách này với chatbot vì chatbot đang nói miễn phí ship từ 300.000 VNĐ.

**31. PayPal hiện hoạt động thế nào?**  
Trình duyệt dùng PayPal SDK tạo và capture thanh toán bằng USD, rồi gửi thông tin về server để lập đơn. Server chưa xác minh lại giao dịch với PayPal, nên chỉ nên mô tả đây là luồng demo, chưa đạt mức an toàn vận hành thật.

**32. Vì sao không thể tin `transactionID` do trình duyệt gửi?**  
Request có thể bị sửa. Máy chủ cần hỏi PayPal về mã giao dịch, trạng thái, tiền tệ, số tiền, người nhận và kiểm tra mã chưa từng được dùng.

**33. Rủi ro của tỷ giá cố định là gì?**  
[custom.js](Veggie/public/assets/clients/js/custom.js) chia VNĐ cho 23.000 để ra USD. Tỷ giá thực thay đổi và số tiền sau làm tròn có thể lệch; máy chủ cũng chưa đối soát số tiền.

**34. Nếu PayPal đã thu tiền nhưng tạo đơn thất bại?**  
Mã hiện chưa có xử lý hoàn chỉnh. Hướng giải quyết là lưu trạng thái trung gian, đối soát, retry an toàn hoặc hoàn tiền theo quy trình.

**35. Vòng đời đơn hàng là gì?**  
Tạo đơn `pending`, quản trị xác nhận thành `processing`, khách xác nhận đã nhận thành `completed`, hoặc hủy. Hiện chuỗi trạng thái hủy chưa thống nhất nên cần chuẩn hóa trước khi gọi đây là state machine hoàn chỉnh.

**36. Hủy đơn có hoàn tồn kho không?**  
Có cộng lại tồn kho, nhưng chưa bảo đảm hoàn đúng một lần khi hủy lặp ở phía quản trị. Nên kiểm tra trạng thái nguồn, khóa bản ghi và hoàn kho trong một transaction.

**37. Vì sao không nên xóa sản phẩm đã từng bán?**  
Lịch sử đơn cần giữ dòng hàng và dữ liệu mua. Với khóa ngoại hiện tại, xóa sản phẩm có thể kéo theo xóa dòng chi tiết đơn. Nên ngừng bán/xóa mềm và giữ snapshot thông tin hàng trong đơn.

**38. Dashboard tính doanh thu thế nào?**  
Cộng `total_price` các đơn không bị hủy theo ngày/tuần/tháng. Vì bao gồm cả đơn chưa thanh toán, nên cách gọi chính xác hiện tại là giá trị đơn chưa hủy, không chắc là tiền thực thu. Xem [DashboardController](Veggie/app/Http/Controllers/Admin/DashboardController.php).

### E. Tài khoản, đánh giá và chatbot

**39. Đăng ký tài khoản gồm các bước gì?**  
Validate dữ liệu → hash mật khẩu → tạo tài khoản `pending` và token → gửi email kích hoạt → đổi thành `active` sau khi bấm link. Xem [AuthController](Veggie/app/Http/Controllers/Clients/AuthController.php).

**40. Mật khẩu được lưu ra sao?**  
Luồng đăng ký dùng `Hash::make`, không lưu mật khẩu rõ. Xem [AuthController](Veggie/app/Http/Controllers/Clients/AuthController.php).

**41. Người dùng được đánh giá khi nào?**  
Giao diện có xét đã mua và đã đánh giá, nhưng API lưu đánh giá chưa xác minh điều kiện này tại server. Nếu hội đồng hỏi, cần thừa nhận giới hạn và nêu cách sửa bằng kiểm tra đơn hợp lệ cùng ràng buộc chống đánh giá trùng.

**42. Chatbot dùng Gemini cho mọi câu hỏi không?**  
Không. Câu hỏi chính sách hoặc tra cứu sản phẩm thường được trả lời cục bộ; câu hỏi mở mới gọi Gemini nếu cấu hình và API khả dụng. Xem [ChatController](Veggie/app/Http/Controllers/Clients/ChatController.php).

**43. Chatbot lấy dữ liệu sản phẩm từ đâu?**  
Từ bảng sản phẩm và danh mục. Hệ thống chọn sản phẩm phù hợp, còn hàng rồi đưa tên, giá, đơn vị và tồn kho vào ngữ cảnh. Đây là đưa dữ liệu vào prompt, chưa phải RAG dùng vector database.

**44. Khi Gemini lỗi thì sao?**  
Chatbot dùng câu trả lời dự phòng; lỗi được ghi log và kết quả lỗi được cache khoảng 5 phút để tránh gọi liên tục. Xem [ChatController](Veggie/app/Http/Controllers/Clients/ChatController.php).

**45. Lịch sử chat khách vãng lai được bảo vệ thế nào?**  
Token khách được tạo và lưu trong Laravel session, không tin token do trình duyệt tự gửi. Lịch sử người đăng nhập gắn với `user_id`; API trả tối đa 50 tin gần đây.

**46. AI có thể trả lời sai không?**  
Có. Prompt và dữ liệu sản phẩm giúp giảm sai sót, nhưng không bảo đảm tuyệt đối. Giá, tồn kho, phí ship và thanh toán phải do logic máy chủ quyết định.

### F. Kiểm thử, bảo mật và triển khai

**47. Đã test những gì?**  
Các trường hợp giỏ vượt tồn kho/hết hàng, chatbot fallback và phân nhóm số liệu dashboard. Lần chạy ngày 13/11/2026: 12 test đạt, 95 assertions. Xem [tests/Feature](Veggie/tests/Feature).

**48. Chưa test những gì quan trọng?**  
Checkout đồng thời, quyền sở hữu đơn/địa chỉ, PayPal, hủy đơn lặp, phân quyền admin, email thật và demo đầu cuối trên trình duyệt.

**49. Lỗ hổng quyền sở hữu dữ liệu lớn nhất?**  
Khách có thể yêu cầu chi tiết đơn theo ID mà controller chưa ràng buộc người sở hữu. Ngoài ra `address_id` chưa được xác minh quyền sở hữu ngay lúc lập đơn. Cần dùng policy/truy vấn theo người dùng và thêm test.

**50. Có cần README riêng không?**  
Có. Người chấm cần biết yêu cầu PHP/Node/Composer, cấu hình DB/mail/PayPal/Gemini, migrate/seed, tài khoản demo, lệnh chạy và lệnh test. [README](Veggie/README.md) hiện vẫn là mẫu Laravel.

**51. Nếu triển khai thật, cần ưu tiên việc gì?**  
Sửa quyền sở hữu đơn/địa chỉ, xác minh PayPal ở server, chuẩn hóa trạng thái và hủy đơn, bổ sung test cho những luồng đó, rồi hoàn thiện vận hành, hiệu năng và tài liệu.

**52. Khi bị hỏi “hệ thống đã hoàn thiện chưa”, nên trả lời thế nào?**  
Nói rõ phần đã chạy được, bằng chứng test hiện có và giới hạn chưa hoàn thiện. Không khẳng định PayPal đã an toàn hoặc hệ thống đã kiểm thử toàn bộ khi mã nguồn chưa chứng minh được.

## 4. Kịch bản demo gợi ý

1. Vào trang sản phẩm, tìm kiếm/lọc và mở chi tiết.
2. Thêm vào giỏ; thử số lượng vượt tồn kho để thấy thông báo lỗi và giỏ không bị sửa sai.
3. Đăng nhập để minh họa giỏ được gộp.
4. Chọn địa chỉ và đặt đơn **COD**; mở lịch sử/chi tiết đơn.
5. Vào khu quản trị, xem đơn, xác nhận và mở dashboard.
6. Hỏi chatbot về tên/giá sản phẩm và minh họa câu trả lời dự phòng khi Gemini không khả dụng.

**Trước demo:** view trang chủ đã được khôi phục; hãy chạy thử trang chủ, thống nhất phí giao hàng và không trình bày PayPal như đã xác minh an toàn khi chưa có kiểm tra tại server.

## 5. Cách trả lời khi hội đồng hỏi vào điểm yếu

Mẫu trả lời hiệu quả: **“Hiện mã nguồn đang làm X; rủi ro là Y; em sẽ sửa bằng Z và thêm test cho tình huống W.”** Ví dụ với PayPal: “Hiện trình duyệt capture rồi gửi mã giao dịch về server; server chưa đối soát lại với PayPal. Em sẽ xác minh trạng thái, số tiền, tiền tệ và mã giao dịch tại server trước khi đánh dấu đã thanh toán, sau đó thêm test giả lập giao dịch sai hoặc dùng lại mã.”
