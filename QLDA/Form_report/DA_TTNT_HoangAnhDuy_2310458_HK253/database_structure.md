# Cấu Trúc Cơ Sở Dữ Liệu SmartWater Admin

## 1. Tổng quan

Cơ sở dữ liệu của hệ thống SmartWater Admin được thiết kế theo hướng quan hệ, nhằm phục vụ bài toán quản lý dịch vụ bảo trì máy lọc nước. Cấu trúc dữ liệu tập trung vào việc liên kết giữa khách hàng, hợp đồng, sản phẩm, kho, thiết bị, dữ liệu telemetry và lịch sử hoạt động. Cách tổ chức này giúp hệ thống không chỉ lưu trữ thông tin danh mục mà còn theo dõi được toàn bộ vòng đời vận hành của thiết bị trong thực tế.

Thiết kế database cho thấy hệ thống được chia thành các nhóm thực thể rõ ràng:

- Nhóm phân quyền và người dùng: `roles`, `users`, `employees`
- Nhóm danh mục và sản phẩm: `categories`, `products`, `suppliers`
- Nhóm kho và nhập hàng: `inventories`, `batches`, `batch_details`
- Nhóm khách hàng và hợp đồng: `customers`, `contracts`, `contract_services`
- Nhóm thiết bị và giám sát: `devices`, `device_dashboard_data`, `maintenance_records`
- Nhóm nhật ký và tệp đính kèm: `activity_logs`, `attachments`

## 2. Quan hệ giữa các bảng

Mối quan hệ giữa các bảng được xây dựng để đảm bảo dữ liệu có thể truy vết xuyên suốt giữa các phân hệ. Một số quan hệ chính gồm:

- `roles` liên kết một-nhiều với `users` và `employees`
- `categories` liên kết một-nhiều với `products`
- `suppliers` liên kết một-nhiều với `batches`
- `products` liên kết với `inventories`, `batch_details` và `devices`
- `customers` liên kết một-nhiều với `contracts` và `devices`
- `contracts` liên kết một-nhiều với `contract_services` và `devices`
- `devices` liên kết với `device_dashboard_data` và `maintenance_records`
- `maintenance_records` liên kết với `employees`
- `users` liên kết với `activity_logs` và `attachments`

Thiết kế này cho phép mỗi thực thể giữ đúng vai trò của mình trong hệ thống. Ví dụ, khách hàng là đầu vào của hợp đồng, hợp đồng là cơ sở để gắn thiết bị, còn thiết bị là đối tượng phát sinh dữ liệu telemetry và lịch sử bảo trì. Nhờ vậy, các thông tin nghiệp vụ được nối với nhau theo một chuỗi logic rõ ràng.

## 3. Các bảng dữ liệu chính

### 3.1 Nhóm người dùng và phân quyền

- `roles`: lưu vai trò người dùng như Administrator, Employee và Technician.
- `users`: lưu tài khoản đăng nhập, trạng thái hoạt động và liên kết đến nhân viên.
- `employees`: lưu thông tin nhân sự, vị trí công việc và trạng thái làm việc.

Nhóm bảng này được dùng để kiểm soát quyền truy cập và gắn hoạt động người dùng với người thực hiện trong hệ thống.

### 3.2 Nhóm danh mục và sản phẩm

- `categories`: phân loại sản phẩm theo nhóm máy lọc nước hoặc linh kiện.
- `products`: lưu mã sản phẩm, tên, model, công suất, đơn vị, giá và trạng thái.
- `suppliers`: lưu thông tin nhà cung cấp.

Ba bảng này tạo thành lớp dữ liệu nền cho việc quản lý hàng hóa, từ phân loại đến nguồn cung.

### 3.3 Nhóm kho và lô hàng

- `inventories`: quản lý tồn kho theo từng sản phẩm, gồm số lượng, số lượng đã giữ và chi phí đơn vị.
- `batches`: lưu thông tin lô hàng nhập kho theo nhà cung cấp.
- `batch_details`: bảng trung gian giữa lô hàng và sản phẩm, đồng thời lưu số lượng và đơn giá.

Thiết kế này giúp hệ thống theo dõi được nguồn gốc vật tư, số lượng nhập và tình trạng tồn kho theo từng sản phẩm.

### 3.4 Nhóm khách hàng và hợp đồng

- `customers`: lưu thông tin khách hàng cá nhân hoặc doanh nghiệp.
- `contracts`: lưu hợp đồng dịch vụ gắn với khách hàng.
- `contract_services`: lưu các dịch vụ định kỳ hoặc dịch vụ đi kèm hợp đồng.

Nhóm bảng này phản ánh quan hệ nghiệp vụ cốt lõi của hệ thống, trong đó khách hàng là trung tâm và hợp đồng là lớp xác định phạm vi dịch vụ.

### 3.5 Nhóm thiết bị và giám sát

- `devices`: lưu thiết bị đã lắp đặt, liên kết với sản phẩm, khách hàng, hợp đồng và lô hàng.
- `device_dashboard_data`: lưu dữ liệu telemetry theo thời gian như TDS, nhiệt độ, lưu lượng nước và pH.
- `maintenance_records`: lưu lịch sử bảo trì, thay thế hoặc sửa chữa thiết bị.

Đây là nhóm bảng quan trọng nhất đối với bài toán vận hành, vì nó kết nối dữ liệu nghiệp vụ với dữ liệu thực tế từ thiết bị.

### 3.6 Nhóm nhật ký và tệp đính kèm

- `activity_logs`: ghi lại các hoạt động của người dùng trong hệ thống.
- `attachments`: lưu tệp đính kèm theo kiểu đa hình, có thể gắn với nhiều loại thực thể khác nhau.

Nhóm bảng này giúp hệ thống có khả năng kiểm tra lịch sử thao tác và lưu trữ tài liệu liên quan đến từng nghiệp vụ.

## 4. Đặc điểm thiết kế

### 4.1 Chuẩn hóa dữ liệu

Cơ sở dữ liệu được tổ chức theo hướng chuẩn hóa để giảm lặp dữ liệu và tăng tính nhất quán. Các thông tin như khách hàng, sản phẩm, nhà cung cấp, thiết bị hay nhân viên được tách thành từng bảng riêng thay vì lưu chung trong một bảng lớn. Điều này giúp hệ thống dễ bảo trì, dễ mở rộng và thuận lợi hơn khi cập nhật dữ liệu.

### 4.2 Tách dữ liệu telemetry khỏi dữ liệu nghiệp vụ

Dữ liệu telemetry được lưu riêng trong `device_dashboard_data` vì đây là loại dữ liệu có tần suất ghi nhận cao và thay đổi liên tục theo thời gian. Việc tách riêng giúp:

- Giữ cho dữ liệu nghiệp vụ sạch và dễ truy vấn.
- Tăng hiệu quả khi giám sát dữ liệu cảm biến.
- Tránh làm phức tạp các bảng quản lý chính.

### 4.3 Tính liên kết giữa các thực thể

Mối liên hệ giữa `customers`, `contracts`, `devices` và `device_dashboard_data` cho phép hệ thống theo dõi đầy đủ trạng thái vận hành của từng thiết bị theo từng khách hàng cụ thể. Đây là điểm quan trọng để hỗ trợ tra cứu, bảo trì và tổng hợp báo cáo trong hệ thống SmartWater Admin.

## 5. Chỉ mục và tối ưu truy vấn

Database có sử dụng các chỉ mục để tối ưu hiệu năng truy vấn cho những cột thường xuyên được tìm kiếm hoặc liên kết:

- `products`: `product_code`, `category_id`
- `inventories`: `product_id`
- `batches`: `batch_code`, `supplier_id`
- `customers`: `customer_code`
- `contracts`: `contract_code`, `customer_id`, `status`
- `devices`: `device_code`, `serial_number`, `customer_id`
- `maintenance_records`: `device_id`, `maintenance_date`
- `activity_logs`: `user_id`, `created_at`
- `batch_details`: `batch_id`, `product_id`

Việc tạo chỉ mục cho các cột này giúp hệ thống phản hồi tốt hơn trong các chức năng tra cứu, lọc danh sách và tổng hợp dữ liệu trên dashboard.

## 6. Dữ liệu mẫu

Hệ thống có sẵn dữ liệu seed để hỗ trợ kiểm thử và phát triển ban đầu:

- 3 vai trò
- 3 người dùng
- 9 nhân viên
- 4 danh mục
- 10 sản phẩm
- 3 nhà cung cấp
- 10 bản ghi tồn kho
- 6 lô hàng
- 18 hợp đồng
- 24 khách hàng
- 30 thiết bị
- 24 bản ghi telemetry
- 20 lịch sử bảo trì
- 25 nhật ký hoạt động

Nhờ bộ dữ liệu mẫu này, hệ thống có thể hiển thị đầy đủ các màn hình quản trị và kiểm tra được luồng dữ liệu ngay từ giai đoạn phát triển.

## 7. Kết luận

Cấu trúc cơ sở dữ liệu của SmartWater Admin được thiết kế để phục vụ đồng thời hai mục tiêu: quản lý nghiệp vụ và giám sát vận hành. Việc phân tách dữ liệu theo từng thực thể, liên kết bằng các khóa ngoại và tối ưu bằng chỉ mục giúp hệ thống dễ mở rộng, dễ bảo trì và phù hợp với bài toán quản trị dịch vụ bảo trì máy lọc nước.
