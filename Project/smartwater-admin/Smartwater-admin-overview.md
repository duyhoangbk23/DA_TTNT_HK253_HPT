# SmartWater Admin - Tổng quan kiến trúc

## Mục tiêu

SmartWater Admin là ứng dụng Laravel quản lý sản phẩm, kho, khách hàng, hợp đồng, thiết bị, MCU và dữ liệu telemetry cho hệ thống SmartWater.

## Cấu trúc hiện tại

Ứng dụng đang tách theo các lớp kỹ thuật:

- `app/Http/Controllers`: tiếp nhận HTTP request, trả về Blade view hoặc JSON API.
- `app/Services`: xử lý nghiệp vụ của từng mảng như Product, Device, Contract, Mcu và Dashboard.
- `app/Models`: Eloquent model ánh xạ schema do dự án `smartwater-database` sở hữu.
- `app/Http/Requests`: xác thực dữ liệu đầu vào cho các nghiệp vụ đã áp dụng Form Request.
- `app/Http/Resources`: chuẩn hóa JSON response cho một số API.
- `resources/views`: giao diện Blade, phân theo từng màn hình và dùng layout, partial, component chung.
- `routes/web.php`: giao diện quản trị yêu cầu đăng nhập.
- `routes/api.php`: endpoint telemetry, health check và API quản lý dữ liệu.

## Nhóm chức năng

| Nhóm | Chức năng chính |
| --- | --- |
| Tổng quan | Dashboard, KPI, thống kê trạng thái thiết bị, hoạt động gần đây |
| Danh mục và kho | Sản phẩm, danh mục, lô hàng, tồn kho |
| Khách hàng | Khách hàng và hợp đồng |
| Thiết bị IoT | Thiết bị, MCU/controller, tiếp nhận telemetry |
| Vận hành hệ thống | Nhân viên, lịch sử hoạt động, hồ sơ cá nhân, xác thực |

Sidebar hiện cũng trình bày phần lớn các chức năng theo ba khu vực: tổng quan, quản lý và hệ thống.

## Đánh giá khả năng mở rộng

### Điểm tốt

- Controller, service và model đã được tách, giúp nghiệp vụ không dồn toàn bộ vào controller.
- View được chia theo màn hình, đồng thời tái sử dụng layout, sidebar, navbar và các component.
- Dashboard và các nghiệp vụ có quy trình nhiều bước như thay thiết bị hoặc tạo hợp đồng đã có service/transaction riêng.
- `mcu_id` được dùng như định danh chuỗi xuyên suốt model thiết bị, MCU và telemetry.

### Hạn chế

- Cấu trúc đang theo lớp kỹ thuật toàn cục, chưa theo module nghiệp vụ. Khi thêm một chức năng lớn phải thay đổi ở nhiều thư mục khác nhau.
- Web route và API route dùng chung controller cho một số mảng, nên dễ tăng độ phức tạp khi hai giao diện phát triển khác nhau.
- Một số controller vẫn tự truy vấn model để lấy dữ liệu phụ trợ cho form thay vì giao toàn bộ cho service.
- Telemetry mới là API ingest và service truy vấn; chưa có một module UI/quản trị telemetry độc lập trong web route và sidebar.
- Test hiện tập trung vào các điểm quan trọng của telemetry, database failure và quan hệ MCU; độ bao phủ chưa tương ứng toàn bộ các mảng nghiệp vụ.

## Hướng tổ chức khi hệ thống lớn hơn

Nên chuyển dần sang module theo chức năng, ví dụ:

```text
app/
  Modules/
    Inventory/
      Http/
      Services/
      Models/
      Requests/
      Resources/
    DeviceManagement/
      Http/
      Services/
      Models/
    Telemetry/
      Http/
      Services/
      Models/
    CustomerManagement/
      Http/
      Services/
      Models/
```

Mỗi module nên sở hữu route, request validation, service, resource, policy và test của chính nó. Với API mở rộng, nên tách controller web và API, dùng chung service/use-case ở tầng nghiệp vụ. Cách này giúp giảm phụ thuộc chéo và làm rõ phạm vi thay đổi khi bổ sung cảnh báo, lịch bảo trì, phân quyền hoặc báo cáo telemetry.

## Kết luận

Kiến trúc hiện tại phù hợp cho quy mô nhỏ đến trung bình và đã có nền tảng phân lớp tốt. Để dễ mở rộng dài hạn, ưu tiên tiếp theo là module hóa theo nghiệp vụ, hoàn thiện phần quản trị telemetry và mở rộng test theo từng module.
