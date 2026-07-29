# Thiết kế loại bỏ MockData runtime

## Mục tiêu

SmartWater Admin không tạo dữ liệu giả trong lúc xử lý request. Màn hình chỉ hiển thị dữ liệu đã lưu trong database; dữ liệu phục vụ demo phải được tạo rõ ràng bằng seeder thuộc `smartwater-database`.

## Phạm vi

- Xóa `App\Support\MockData` sau khi loại bỏ toàn bộ nơi sử dụng.
- `ActivityController` đọc `activity_logs` cùng quan hệ `user.employee` từ database, sắp xếp mới nhất trước và phân trang.
- Màn hình lịch sử hoạt động hiển thị bản ghi Eloquent thật và empty state khi database chưa có dữ liệu.
- Layout tiếp tục lấy người dùng đăng nhập qua `Auth`; không tạo người dùng hoặc thông báo giả.
- Giữ `ActivityLogSeeder` trong `smartwater-database` làm nguồn dữ liệu demo có chủ đích.

## Luồng dữ liệu

`activity_logs` → `ActivityLog` → `ActivityController` → `activities/index.blade.php`.

Tên và ảnh người thực hiện lấy từ `activityLog.user.employee`, sau đó mới dùng `user.username` hoặc `user.email` nếu nhân viên không tồn tại. Không dùng avatar từ dịch vụ tạo ảnh ngẫu nhiên.

## Xử lý trường hợp rỗng và lỗi

- Không có activity log: bảng hiển thị thông báo chưa có dữ liệu.
- Quan hệ user/employee bị thiếu: hiển thị `Hệ thống` và avatar mặc định bằng biểu tượng.
- Database không khả dụng: dùng cơ chế phản hồi an toàn hiện có; không fallback sang dữ liệu mẫu.

## Kiểm thử

- Feature test tạo activity log trong SQLite và xác nhận route hiển thị đúng bản ghi database.
- Xác nhận trang không còn trả về tập hoạt động giả khi database chỉ có fixture của test.
- Kiểm tra phân trang, PHP syntax, Blade compilation và `git diff --check`.

## Ngoài phạm vi

- Không tạo migration mới trong web app.
- Không xây dựng hệ thống thông báo mới.
- Không thay đổi dữ liệu telemetry hoặc quy trình bảo trì.
