# Thiết kế comment luồng chạy chính

## Mục tiêu

Bổ sung comment tiếng Việt cho các khối source code quan trọng để người đọc hiểu chức năng, thứ tự xử lý và luồng dữ liệu xuyên suốt hệ thống mà không mô tả lại từng dòng lệnh.

## Quy tắc comment

- Comment trước một khối nghiệp vụ, hàm điều phối hoặc bước chuyển dữ liệu quan trọng.
- Giải thích mục đích, dữ liệu đầu vào, tác động chính và nơi dữ liệu được chuyển tiếp.
- Không comment cú pháp hiển nhiên, import, getter/setter, cấu hình thư viện hoặc từng dòng lệnh.
- Không thay đổi hành vi, API, schema, payload, tên trường hoặc thứ tự xử lý.
- Dùng comment tiếng Việt ngắn gọn; giữ nguyên comment kỹ thuật hiện có nếu vẫn chính xác.

## Phạm vi source code

### Firmware và simulator

- Entry point khởi tạo phần cứng, Wi-Fi, cảm biến và vòng lặp gửi telemetry.
- Khối đọc TDS, chuẩn hóa dữ liệu cảm biến và tạo payload.
- Khối kết nối/reconnect MQTT và publish lên topic telemetry.
- Simulator tạo telemetry và mô phỏng chu kỳ gửi nhưng không comment thư viện PlatformIO hoặc file trong `.pio`.

### Device Monitor

- Entry point HTTP/API và cách route chuyển request đến service/repository.
- Kết nối database và phản hồi an toàn khi database không khả dụng.
- Luồng nhận, chuẩn hóa, lưu telemetry; cập nhật trạng thái MCU; tạo ticket khi có alert.
- Truy vấn danh sách MCU, phân trang log và chuỗi TDS theo cửa sổ thời gian.

### SmartWater Admin

- Route web/API/console điều phối các chức năng chính.
- Controller/service cho thiết bị, MCU, telemetry, dashboard, bảo trì và Activity Log.
- Luồng tạo bảo trì định kỳ theo hợp đồng và bảo trì tức thì theo alert.
- Luồng quản lý chỉ đọc trạng thái MCU; backend là phía cập nhật trạng thái.
- Quan hệ model chỉ được comment khi khóa liên kết hoặc ý nghĩa nghiệp vụ không hiển nhiên.

### Database

- `DatabaseSeeder` giải thích thứ tự seed phụ thuộc dữ liệu.
- Migration bảo trì giải thích mục đích các khóa chống trùng và trường lịch/alert quan trọng.
- Không thêm migration vào web app và không comment file dữ liệu/runtime sinh bởi MySQL.

## Loại trừ

- `vendor`, `node_modules`, `.pio`, `storage/framework`, cache, log, binary và output build.
- CSS/JS thư viện, file minified và code do framework/build sinh ra.
- Blade thuần trình bày, request validation đơn giản và model CRUD không có luồng nghiệp vụ đặc biệt.
- Test fixture chỉ dùng để dựng dữ liệu, trừ khi test có luồng tích hợp khó hiểu.

## Kiểm tra

- PHP lint cho các file PHP được chỉnh sửa.
- Build firmware/simulator tương ứng nếu file C++ được chỉnh sửa.
- Laravel Blade compilation nếu có comment trong Blade quan trọng.
- Chạy các kiểm thử telemetry, MCU và bảo trì hiện có.
- `git diff --check` và rà soát diff để xác nhận chỉ có comment, không đổi hành vi.
