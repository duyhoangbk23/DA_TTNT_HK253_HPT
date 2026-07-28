# Đặc tả dự án tài liệu SmartWater Solution Design

## Mục tiêu

Xây dựng một dự án LaTeX hoàn chỉnh tại `QLDA/Form_report/Truliliva-proposal`, phản ánh trung thực kiến trúc và trạng thái triển khai của hệ thống SmartWater dựa trên source code hiện tại.

## Cấu trúc đầu ra

- `main.tex`: cấu hình tài liệu, trang bìa, Document Control, mục lục, danh mục hình và danh mục bảng.
- `include/chapters/`: 17 chương của Solution Design Document.
- `include/appendices/`: repository inventory, component inventory, route catalogue, database catalogue, MQTT/JSON specification, configuration variables, sơ đồ, điểm không đồng bộ và source evidence index.
- `include/diagrams/`: sơ đồ TikZ về kiến trúc, triển khai, component, sequence, error flow và ERD.
- `include/images/`: các ảnh kiến trúc và giao diện được chọn từ nguồn ảnh người dùng cung cấp.

## Phạm vi khảo sát

- Firmware ESP32 và message simulator.
- HiveMQ/MQTT configuration và service liên quan.
- Device Monitor.
- SmartWater Admin.
- Shared MySQL database, migration, model, seeder và quan hệ dữ liệu.
- Giao diện, dashboard, route, controller, service, repository và tích hợp.
- Cấu hình triển khai, bảo mật, logging, xử lý lỗi và kiểm thử hiện có.

Không sửa source code ứng dụng trong quá trình khảo sát.

## Nguyên tắc nội dung

- Chỉ mô tả nội dung có bằng chứng trong source code hoặc tài liệu đi kèm.
- Phân loại từng thành phần theo: Implemented, Partially Implemented, Configured, Prototype/Mock, Planned hoặc Not Found.
- Mọi nhận định kỹ thuật quan trọng phải dẫn đường dẫn file, class, method, route, migration, table hoặc configuration key tương ứng.
- Không đưa secret, password, token, API key hoặc certificate value vào tài liệu.
- Migration là nguồn chuẩn khi model và schema không đồng nhất; khác biệt phải được ghi rõ.
- `mcu_id` là định danh chuẩn của telemetry theo schema hiện tại.
- Các bảng lô hàng vẫn được phân tích trong catalogue database nhưng giao diện Lô hàng được ghi nhận là đã ẩn khỏi sidebar Admin Dashboard.

## Trình bày

- Ngôn ngữ: tiếng Việt, văn phong giải pháp doanh nghiệp.
- Tiêu đề: `SMARTWATER SOLUTION DESIGN DOCUMENT`.
- Tác giả: Hoàng Anh Duy.
- Phiên bản: 0.1.
- Trạng thái: Draft.
- Không dùng ngôi thứ nhất hoặc văn phong nhật ký.
- Hình ảnh chỉ được sử dụng khi làm rõ kiến trúc, luồng dữ liệu hoặc bằng chứng giao diện.

## Kiểm chứng

- Đối chiếu danh mục thành phần với cấu trúc repository.
- Đối chiếu route, controller, model, view và database table.
- Đối chiếu payload firmware, parser Device Monitor và schema telemetry.
- Kiểm tra mọi file ảnh được tham chiếu tồn tại.
- Biên dịch LaTeX và xử lý lỗi tham chiếu, font, bảng, hình và sơ đồ trước khi bàn giao.
- Không khẳng định test runtime hoặc tích hợp live nếu chưa có bằng chứng thực thi trong lượt làm việc.

## Tiêu chí hoàn thành

- Dự án LaTeX có thể biên dịch thành PDF.
- Đủ 17 chương và các phụ lục bắt buộc.
- Có inventory, feature matrix, database catalogue, integration analysis, gap/risk analysis và source evidence.
- Có sơ đồ kiến trúc tổng thể, triển khai, component, telemetry sequence, error flow và ERD.
- Phân biệt rõ chức năng hiện có, triển khai một phần, mock, planned và không tìm thấy.
- Không chứa bí mật cấu hình.
