# Device Monitor

Web nhận telemetry từ HiveMQ bằng PHP + Slim Framework.

## Mục tiêu

- Topic chung cho mọi device: `devices/telemetry`
- Lưu toàn bộ telemetry vào một bảng
- Truy xuất và sort dữ liệu theo `device_id`
- Hiển thị bảng telemetry và phục vụ dữ liệu cho biểu đồ

## Chức năng

- Kết nối HiveMQ qua MQTT over WebSocket
- Subscribe topic chung `devices/telemetry`
- Nhận message JSON từ ESP32
- Parse payload
- Lưu telemetry vào database qua API Slim
- Xem danh sách telemetry theo `device_id`
- Tổng hợp dữ liệu để vẽ biểu đồ theo từng thiết bị

## Luồng xử lý

1. ESP32 publish telemetry lên HiveMQ tại topic `devices/telemetry`
2. Frontend subscribe topic này và nhận message
3. Frontend gửi message về `POST /api/telemetry`
4. Backend parse JSON và lưu vào bảng `telemetry`
5. Frontend đọc `GET /api/telemetry` và sort theo `device_id`, `timestamp`

## Cấu trúc thư mục

```text
Device-monitor/
├─ public/
│  ├─ index.php
│  └─ assets/
│     ├─ app.css
│     └─ app.js
├─ src/
│  ├─ Database.php
│  ├─ Responder.php
│  ├─ TelemetryRepository.php
│  ├─ TelemetryService.php
│  └─ View.php
├─ templates/
│  ├─ layout.php
│  ├─ dashboard.php
│  ├─ config.php
│  └─ telemetry.php
├─ storage/
├─ composer.json
├─ composer.lock
└─ README.md
```

## API

- `GET /api/health`
- `GET /api/telemetry?limit=100`
- `GET /api/telemetry/summary`
- `POST /api/telemetry`

## JSON message

### Payload từ ESP32

```json
{
  "device_id": "esp32-01",
  "timestamp": "2026-07-13 10:00:00",
  "tds": 284,
  "alert": "normal"
}
```

### Ý nghĩa field

- `device_id`: mã thiết bị
- `timestamp`: thời điểm đo
- `tds`: giá trị TDS
- `alert`: trạng thái cảnh báo

### Message gửi về backend

```json
{
  "topic": "devices/telemetry",
  "payload": {
    "device_id": "esp32-01",
    "timestamp": "2026-07-13 10:00:00",
    "tds": 284,
    "alert": "normal"
  }
}
```

## Schema telemetry

Bảng `telemetry` lưu các cột chính:

- `device_id`
- `timestamp`
- `tds`
- `alert`

Các cột hỗ trợ:

- `id`
- `topic`
- `source`
- `payload_raw`
- `payload_json`
- `created_at`

Index phục vụ truy xuất theo thiết bị:

- `device_id + timestamp`

## Database

### MySQL

Biến môi trường:

- `DB_CONNECTION=mysql`
- `DB_DRIVER=mysql`
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

Mặc định dự án sẽ dùng MySQL. Nếu muốn đổi sang driver khác, cấu hình lại trong môi trường chạy.

## Chạy dự án

```bash
composer install
php -S localhost:8000 -t public public/index.php
```

## Ghi chú

- Dữ liệu telemetry từ mọi device được lưu chung một bảng.
- Khi truy xuất, backend sắp xếp theo `device_id` rồi `timestamp`.
