# Device Monitor MCU telemetry design

## Goal

Trên trang Telemetry Live, hiển thị danh sách MCU từng gửi telemetry, telemetry đã lọc theo MCU đang chọn và biểu đồ TDS theo thời gian.

## Data source and APIs

- Dùng bảng `telemetry` hiện có; không tạo bảng MCU trùng dữ liệu.
- Thêm API trả về từng `mcu_id` duy nhất, số telemetry và thời điểm nhận gần nhất.
- Mở rộng API telemetry với tham số `mcu_id` để trả về riêng dữ liệu của MCU được chọn.
- Thêm API chuỗi điểm `{timestamp, tds}` cho biểu đồ TDS của MCU được chọn.

## Page behaviour

- Giữ trang `/telemetry` là trang duy nhất.
- Bố cục desktop gồm hai cột: danh sách MCU ở trái; bảng telemetry và biểu đồ ở phải.
- Chọn một MCU sẽ tải lại bảng và biểu đồ chỉ với `mcu_id` đó.
- Mỗi telemetry mới được lưu từ HiveMQ sẽ làm mới danh sách MCU; nếu thuộc MCU đang chọn thì làm mới bảng và biểu đồ.
- Với MCU không có giá trị TDS, biểu đồ bỏ qua điểm đó nhưng bảng vẫn hiện telemetry.

## Presentation

- Danh sách MCU hiển thị `mcu_id`, số bản ghi và thời điểm gần nhất.
- Bảng telemetry giữ các cột thời gian, topic, MCU ID, TDS và alert.
- Biểu đồ đường biểu diễn TDS theo timestamp, có trạng thái trống khi MCU chưa có TDS.

## Testing

- Kiểm tra repository truy vấn danh sách MCU không trùng và lọc telemetry theo `mcu_id`.
- Kiểm tra các API trả JSON hợp lệ cho danh sách MCU, telemetry đã lọc và điểm biểu đồ.
- Kiểm tra JavaScript cú pháp và trang `/telemetry` tải được.
