# Báo cáo tuần 3

## 1. Công việc thực hiện

Trong tuần 3, tôi tập trung vào việc làm quen với hệ thống Web Backend, khảo sát kiến trúc dữ liệu và luồng xử lý dữ liệu trong dự án `smartwater-admin`. Các nội dung đã thực hiện gồm:

- Đọc tài liệu đề xuất của công ty để hiểu yêu cầu từ phía khách hàng.
- Lên ý tưởng xây dựng hệ thống `smartwater-admin` dưới dạng dashboard quản lý tập trung.
- Khảo sát ngôn ngữ, framework và cấu trúc source code của hệ thống.
- Phân tích quá trình xây dựng backend, database và cách triển khai ứng dụng.
- Thiết kế giao diện frontend cho các màn hình quản trị chính.
- Chạy thử hệ thống để kiểm tra luồng hoạt động cơ bản.

## 2. Nội dung tìm hiểu từ source code

Sau khi đọc source code, tôi xác định `smartwater-admin` là một hệ thống quản trị cho dịch vụ bảo trì máy lọc nước. Ứng dụng được xây dựng bằng Laravel, sử dụng mô hình MVC kết hợp Service để tách riêng phần xử lý nghiệp vụ và phần hiển thị.

Hệ thống hỗ trợ các phân hệ chính:

- Quản lý khách hàng
- Quản lý hợp đồng
- Quản lý sản phẩm
- Quản lý kho linh kiện và vật tư
- Quản lý lô hàng nhập kho
- Quản lý thiết bị IoT đã lắp đặt
- Theo dõi telemetry từ thiết bị
- Quản lý nhân viên, hoạt động hệ thống và hồ sơ cá nhân

## 3. Khảo sát kiến trúc hệ thống

Kiến trúc của dự án được tổ chức theo các lớp chính sau:

- `routes/`: khai báo route cho web và API.
- `app/Http/Controllers/`: tiếp nhận request và điều phối xử lý.
- `app/Services/`: chứa logic tổng hợp dữ liệu và nghiệp vụ.
- `app/Models/`: ánh xạ dữ liệu từ database bằng Eloquent.
- `resources/views/`: giao diện Blade cho các màn hình quản trị.
- `smartwater-database/`: nơi chứa migration và seed dữ liệu dùng chung cho hệ thống.

Từ file route `web.php`, tôi thấy các màn hình được phân tách rõ theo từng nghiệp vụ như dashboard, products, inventory, categories, batches, customers, contracts, devices, employees, activities và profile. Điều này giúp hệ thống dễ mở rộng và dễ bảo trì.

## 4. Khảo sát kiến trúc dữ liệu

Từ tài liệu schema và các model, tôi nhận thấy dữ liệu của hệ thống được thiết kế xoay quanh các thực thể chính:

- `customers`: thông tin khách hàng cá nhân hoặc doanh nghiệp.
- `contracts`: hợp đồng dịch vụ gắn với khách hàng.
- `products`: danh mục máy lọc nước và linh kiện.
- `inventories`: tồn kho theo từng sản phẩm.
- `batches`: lô nhập kho từ nhà cung cấp.
- `devices`: thiết bị đã lắp đặt tại khách hàng.
- `device_dashboard_data`: dữ liệu telemetry của thiết bị.
- `maintenance_records`: lịch sử bảo trì.
- `activity_logs`: nhật ký hoạt động của người dùng.

Mối quan hệ dữ liệu cho thấy hệ thống không chỉ quản lý thông tin danh mục mà còn theo dõi được toàn bộ vòng đời vận hành của thiết bị, từ nhập kho, lắp đặt, bảo trì đến giám sát dữ liệu cảm biến.

## 5. Luồng xử lý dữ liệu

### 5.1 Luồng dữ liệu trên dashboard

Controller `DashboardController` lấy dữ liệu từ `DashboardService` để dựng trang tổng quan. Dữ liệu được tổng hợp gồm:

- KPI số lượng khách hàng, sản phẩm, thiết bị và hợp đồng.
- Thống kê trạng thái thiết bị.
- Biểu đồ khách hàng mới theo tháng.
- Biểu đồ lượt bảo trì theo tháng.
- Danh sách hoạt động gần đây.
- Danh sách lịch bảo trì gần đây.
- Danh sách hợp đồng sắp hết hạn.

Điều này cho thấy dashboard được thiết kế theo hướng tổng hợp dữ liệu từ nhiều bảng khác nhau để phục vụ theo dõi vận hành nhanh.

### 5.2 Luồng dữ liệu telemetry

Tôi cũng khảo sát luồng nhận dữ liệu telemetry qua `TelemetryController`. Quy trình xử lý gồm:

1. Nhận request chứa `mcu_id`, `api_key`, thời gian và các chỉ số đo.
2. Tìm MCU theo mã thiết bị.
3. Kiểm tra API key để xác thực nguồn gửi.
4. Lấy thiết bị đang gắn với MCU.
5. Ghi dữ liệu vào bảng `device_dashboard_data`.
6. Cập nhật trạng thái MCU sang `online`.

Luồng này cho thấy hệ thống có phần backend phục vụ thiết bị gửi dữ liệu thật hoặc dữ liệu mô phỏng, từ đó hỗ trợ giám sát chỉ số như TDS, nhiệt độ, lưu lượng nước và pH.

## 6. Ngôn ngữ và framework sử dụng

Từ source code, tôi xác định các công nghệ chính:

- Backend: PHP với Laravel 11
- Database: SQLite cho môi trường phát triển và MySQL cho triển khai chính
- Frontend: Blade, Bootstrap và các file asset được build bằng Vite
- Kiến trúc: MVC kết hợp Service Layer

Việc tách logic vào `Services` giúp controller gọn hơn, còn giao diện Blade giúp việc dựng màn hình nhanh và đồng bộ.

## 7. Quá trình xây dựng backend và database

Trong quá trình khảo sát source, tôi nhận thấy hệ thống được xây dựng theo hướng:

- Định nghĩa model và quan hệ giữa các bảng bằng Eloquent.
- Tạo migration và seed để khởi tạo dữ liệu mẫu.
- Xây dựng service để gom các truy vấn thống kê và dữ liệu hiển thị.
- Tạo controller để xử lý request CRUD và điều hướng trang.
- Tổ chức route theo từng phân hệ riêng.

Database được thiết kế khá rõ ràng cho bài toán quản lý dịch vụ bảo trì:

- `products` liên kết với `categories`.
- `contracts` liên kết với `customers`.
- `devices` liên kết với `products`, `customers`, `contracts` và `batches`.
- `maintenance_records` liên kết với `devices` và `employees`.
- `device_dashboard_data` liên kết với `devices`.

Thiết kế này đảm bảo dữ liệu có thể truy vết theo từng khách hàng và từng thiết bị.

## 8. Thiết kế frontend

Phần frontend của dự án được xây dựng theo hướng dashboard quản trị, tập trung vào việc đọc nhanh số liệu và trạng thái hệ thống.

Các thành phần giao diện chính gồm:

- Khối KPI tổng quan ở đầu trang dashboard.
- Biểu đồ trạng thái thiết bị.
- Biểu đồ khách hàng mới và lượt bảo trì theo tháng.
- Danh sách hợp đồng sắp hết hạn.
- Bảng hoạt động gần đây và lịch bảo trì gần đây.

Giao diện dùng các component Blade như `kpi-card`, `panel`, `status-badge` để tái sử dụng bố cục và đồng nhất style giữa các màn hình.

## 9. Chạy thử hệ thống

Sau khi đọc và khảo sát source code, tôi thực hiện chạy thử để kiểm tra luồng cơ bản của ứng dụng:

- Kiểm tra màn hình đăng nhập.
- Kiểm tra dashboard tổng quan.
- Kiểm tra các trang quản lý khách hàng, hợp đồng, sản phẩm, kho, thiết bị và nhân viên.
- Kiểm tra việc hiển thị dữ liệu mẫu và các biểu đồ tổng hợp.

Kết quả chạy thử cho thấy hệ thống đã bao phủ được các chức năng quản trị cốt lõi và có thể sử dụng làm nền tảng để phát triển tiếp các tính năng nâng cao.

## 10. Kết luận tuần 3

Tuần 3 giúp tôi hiểu rõ hơn cách một hệ thống Web Backend được tổ chức từ dữ liệu, route, controller, service đến giao diện hiển thị. Từ source code `smartwater-admin`, tôi đã xác định được kiến trúc tổng thể, cách dữ liệu telemetry được ghi nhận, và cách dashboard tổng hợp thông tin để phục vụ quản trị.

Đây là nền tảng quan trọng để tôi tiếp tục hoàn thiện thiết kế, mở rộng tính năng và chuẩn bị cho các tuần triển khai tiếp theo.
