# Device Monitor

Device Monitor nhận telemetry từ HiveMQ qua MQTT over WebSocket, gửi payload đã nhận về Slim API, lưu MySQL và hiển thị dữ liệu trực tiếp.

## Yêu cầu

- PHP 8.1+ với `pdo_mysql`
- Composer
- MySQL đang chạy tại `127.0.0.1:3306`
- Bảng `telemetry` đã được tạo bởi `Project/smartwater-database`

## Khởi động

Từ thư mục gốc repository, chạy migration trước:

```bat
Project\smartwater-database\migrate.bat
```

Sau đó chạy Device Monitor:

```bat
Project\Device-monitor\run.bat
```

Launcher dùng các giá trị mặc định:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smartwater_database
DB_USERNAME=root
DB_PASSWORD=
```

Ứng dụng chạy tại `http://127.0.0.1:8001`.

## Luồng telemetry

1. ESP32 publish JSON lên topic `devices/telemetry`.
2. Trang `/telemetry` subscribe topic qua HiveMQ WebSocket.
3. Frontend gọi `POST /api/telemetry`.
4. Slim chuẩn hóa payload, sinh thời điểm UTC hiện tại và lưu vào bảng `telemetry`.
5. Trang Telemetry Live tải danh sách MCU, bảng telemetry đã lọc và biểu đồ TDS.

## Telemetry Live

Trang `/telemetry` có hai cột:

- Danh sách `mcu_id` không trùng, số telemetry và thời điểm gần nhất.
- Telemetry của MCU được chọn cùng biểu đồ TDS theo thời gian.

Khi nhận telemetry MQTT mới, danh sách MCU được làm mới. Nếu MCU đang được chọn nhận telemetry, bảng và biểu đồ cũng được làm mới.

## API

| Method | Endpoint | Mô tả |
| --- | --- | --- |
| `GET` | `/api/health` | Kiểm tra dịch vụ. |
| `GET` | `/api/mcus` | Danh sách MCU từng gửi telemetry. |
| `GET` | `/api/telemetry?limit=100&mcu_id=MCU-DEMO-001` | Telemetry, có thể lọc theo `mcu_id`. |
| `GET` | `/api/telemetry/chart?mcu_id=MCU-DEMO-001` | Điểm `{timestamp, tds}` theo thứ tự thời gian. |
| `GET` | `/api/telemetry/summary` | Tổng hợp telemetry, topic, MCU và alert. |
| `POST` | `/api/telemetry` | Lưu telemetry. |

`/api/telemetry/chart` trả `422` nếu thiếu `mcu_id`.

### Payload gửi vào

```json
{
  "topic": "devices/telemetry",
  "payload": {
    "mcu_id": "MCU-DEMO-001",
    "tds": 119.75,
    "alert": "normal"
  },
  "source": "hivemq-web"
}
```

`payload` có thể là object JSON hoặc chuỗi JSON. `mcu_id` là chuỗi không rỗng, tối đa 50 ký tự. Thời điểm lưu được Device Monitor tạo theo UTC.

## Bảng telemetry

| Cột | Ý nghĩa |
| --- | --- |
| `id` | Khóa chính. |
| `timestamp` | Thời điểm Device Monitor nhận telemetry. |
| `topic` | MQTT topic. |
| `mcu_id` | Mã MCU. |
| `tds` | Giá trị TDS, có thể rỗng. |
| `alert` | Trạng thái cảnh báo, có thể rỗng. |

## Kiểm tra

```powershell
php tests/TelemetryRepositoryTest.php
powershell -ExecutionPolicy Bypass -File tests/TelemetryApiTest.ps1
powershell -ExecutionPolicy Bypass -File tests/TelemetryUiTest.ps1
powershell -ExecutionPolicy Bypass -File tests/TelemetryEncodingTest.ps1
node --check public/assets/app.js
```
